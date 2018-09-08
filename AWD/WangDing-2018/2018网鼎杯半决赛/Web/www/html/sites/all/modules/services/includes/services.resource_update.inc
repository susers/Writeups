<?php

/**
 * Updates resource definitions to the 3002 version with normalized definitions
 * for the CRUD operations. The CRUD operations will now be namespaced just
 * like relationships, targeted_actions and actions.
 *
 * @param array $resources
 * @return void
 */
function services_resource_api_update_3002(&$resources) {
  static $operations = array('create', 'retrieve', 'update', 'delete', 'index');
  foreach ($resources as $name => &$resource) {
    foreach ($operations as $key) {
      if (!empty($resource[$key])) {
        $resource['operations'][$key] = $resource[$key];
        unset($resource[$key]);
      }
    }
  }
}
