<?php
/**
 * @file
 * Explains how to use versions
 */

/*
 * All functions that want to be considered for updates need to use a specific naming convention
 * We took the same approach as the standard Drupal hook_update methods. The pattern of update functions is
 * _{RESOURCE_NAME}_resource_{METHOD_NAME}_update_{MAJOR_VERSION}_{MINOR_VERSION}
 * @see services_get_updates() for exact regular expression used.
 *
 * For clients that want to request a specific version they need to pass a certain header
 * services_RESOURCE_METHOD_version = version
 * as an example, services_system_set_variable_version = 1.2
 * @see ServicesVersionTests for code examples
 *
 * Services by default will always use the original resource shipped
 * with services. If you wish to change this you can go to the resource page,
 * and select an api version for the specific resource. The version option will
 * only be enabled if version changes exist.
 */

function _system_resource_set_variable_update_1_1() {
  $new_set = array(
    'help' => 'Create a node with an nid test',
  );
  return $new_set;
}

function _system_resource_set_variable_update_1_2() {
  $new_set = array(
    'help' => 'Create a node with an nid optional prams.',
    'args' => array(
      array(
        'name' => 'name',
        'optional' => TRUE,
        'source' => array('data' => 'name'),
        'description' => t('The name of the variable to set.'),
        'type' => 'string',
      ),
      array(
        'name' => 'value',
        'optional' => TRUE,
        'source' => array('data' => 'value'),
        'description' => t('The value to set.'),
        'type' => 'string',
      ),
    ),
  );
  return $new_set;
}
