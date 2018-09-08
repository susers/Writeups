<?php

/**
 * @file
 *  Contains functions necessary for building the resource definitions. This
 *  is only needed the first time a the resources for a endpoint are fetched,
 *  or when the cache has been cleared.
 */

/**
 * Builds the resource definition array for a endpoint.
 *
 * @param string $endpoint_name
 *  Optional. The endpoint name.
 * @return array
 *  The resource definitions.
 */
function _services_build_resources($endpoint_name = '') {
  module_load_include('inc', 'services', 'includes/services.runtime');

  $resources = array();
  $version_info = services_resource_api_version_info();
  $resource_modules = module_implements('services_resources');
  // Get all installed resources
  foreach ($resource_modules as $resource_module) {
    $module_resources = call_user_func("{$resource_module}_services_resources");
    if (empty($module_resources)) {
      continue;
    }

    // Check resource versions.
    $api_version = $version_info['default_version'];
    if (!empty($module_resources['#api_version'])) {
      $api_version = $module_resources['#api_version'];
      unset($module_resources['#api_version']);
    }
    // Upgrade resources if needed.
    if ($api_version < $version_info['current_version']) {
      _services_resource_upgrade($api_version, $module_resources, $version_info);
    }

    $resources = array_merge_recursive($resources, $module_resources);
  }

  // Load the endpoint.
  $endpoint = NULL;
  if (!empty($endpoint_name)) {
    $endpoint = services_endpoint_load($endpoint_name);
    // Apply the endpoint on the services
    _services_apply_endpoint($resources, $endpoint, FALSE);
  }
  drupal_alter('services_resources', $resources, $endpoint);
  // Process the resources, and collect all controllers in the process
  $controllers = array();
  $class_info = services_operation_class_info();
  foreach ($resources as $resource_name => &$resource) {
    foreach ($class_info as $class_name => $class) {
      if (!isset($resource[$class_name])) {
        continue;
      }

      foreach (array_keys($resource[$class_name]) as $action_name) {
        $controllers["{$resource_name}/{$class['class_singular']}/{$action_name}"] = &$resource[$class_name][$action_name];
      }
    }
  }

  // Make sure that we got a access callback for all resources
  foreach ($controllers as &$controller) {
    if (!empty($controller['file'])) {
      // Convert old-style file references to something that fits module_load_include() better.
      if (!empty($controller['file']['file']) && empty($controller['file']['type'])) {
        $controller['file']['type'] = $controller['file']['file'];
      }
    }
    if (!isset($controller['access callback'])) {
      $controller['access callback'] = 'user_access';
    }
  }
  // This hook is deprecated and will be removed in next versions of services.
  // Use hook_services_resources_alter instead.
  drupal_alter('services_resources_post_processing', $resources, $endpoint);

  // Do some endpoint-dependent processing.
  if ($endpoint) {
    // Let the authentication modules alter our controllers
    foreach ($endpoint->authentication as $auth_module => $auth_settings) {
      services_auth_invoke($auth_module, 'alter_controllers', $auth_settings, $controllers, $endpoint);
    }

    // Apply any aliases from endpoint
    $aliased = array();
    foreach ($resources as $key => $def) {
      $def['key'] = $key;
      if (!empty($def['endpoint']['alias'])) {
        $aliased[$def['endpoint']['alias']] = $def;
      }
      else {
        $aliased[$key] = $def;
      }
    }
    $resources = $aliased;
  }

  return $resources;
}

/**
 * Upgrades old resource definition to the newer format.
 *
 * @param int $from
 *  The API version that the resource was written for.
 * @param array $resources
 *  The resource definitions.
 * @param array $version_info
 *  Optional. The version info array as returned from services_resource_api_version_info().
 * @return void
 */
function _services_resource_upgrade($from, &$resources, $version_info = NULL) {
  module_load_include('inc', 'services', 'includes/services.resource_update');

  // Get version info if needed.
  if ($version_info == NULL) {
    $version_info = services_resource_api_version_info();
  }

  // Run upgrades.
  foreach ($version_info['versions'] as $update) {
    if ($update > $from) {
      call_user_func_array("services_resource_api_update_{$update}", array(&$resources));
    }
  }
}

/**
 * Applies the endpoint to a set of resources. Resources and controllers that
 * aren't supported will be removed (if $strict is set to TRUE) and both
 * resources and controllers will get the 'endpoint' attribute set.
 *
 * @param array $resources
 *  An array of resources that the endpoint should be applied on.
 * @param array $endpoint
 *  A endpoint information array.
 * @param bool $strict
 *  Optional.
 * @return void
 */
function _services_apply_endpoint(&$resources, $endpoint, $strict = TRUE) {
  if (is_array($endpoint) && isset($endpoint['build_info'])) {
    $endpoint = $endpoint['build_info']['args'][0];
  }
  $classes = array_keys(services_operation_class_info());
  foreach ($resources as $name => &$resource) {
    $cres = ($endpoint && isset($endpoint->resources[$name])) ? $endpoint->resources[$name] : array();
    if (isset($resource['key']) && $resource['key'] !== $name && $endpoint && isset($endpoint->resources[$resource['key']])) {
      $cres = $endpoint->resources[$resource['key']];
    }
    $resource['endpoint'] = $cres;
    if ($strict && empty($cres)) {
      unset($resources[$name]);
    }
    else {
      foreach ($classes as $class) {
        if (!empty($resource[$class])) {
          foreach ($resource[$class] as $op => $def) {
            $cop = isset($cres[$class][$op]) ? $cres[$class][$op] : array();
            if (empty($cop) || !$cop['enabled']) {
              if ($strict) {
                unset($resource[$class][$op]);
              }
            }
            else {
              $resource[$class][$op]['endpoint'] = empty($cop['settings']) ? array() : $cop['settings'];
              if (isset($cres['alias'])) {
                $resource[$class][$op]['endpoint']['alias'] =  $cres['alias'];
              }
            }
          }
        }
      }
    }
  }
}

/**
 * Supplies the resource definitions for Drupal core data
 *
 * @return array
 */
function _services_core_resources() {
  module_load_include('inc', 'services', 'resources/comment_resource');
  module_load_include('inc', 'services', 'resources/file_resource');
  module_load_include('inc', 'services', 'resources/node_resource');
  module_load_include('inc', 'services', 'resources/system_resource');
  module_load_include('inc', 'services', 'resources/taxonomy_resource');
  module_load_include('inc', 'services', 'resources/user_resource');

  $resources = array(
    '#api_version' => 3002,
  );

  $resources += _comment_resource_definition();
  $resources += _file_resource_definition();
  $resources += _node_resource_definition();
  $resources += _system_resource_definition();
  $resources += _taxonomy_resource_definition();
  $resources += _user_resource_definition();

  return $resources;
}
