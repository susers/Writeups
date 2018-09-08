<?php

/**
 * @file
 *  Link general system functionalities to services module.
 */

function _system_resource_definition() {
  return array(
    'system' => array(
      'actions' => array(
        'connect' => array(
          'access callback' => 'services_access_menu',
          'help' => 'Returns the details of currently logged in user.',
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'resources/system_resource'),
          'callback' => '_system_resource_connect',
        ),
        'get_variable' => array(
          'help'   => 'Returns the value of a system variable using variable_get().',
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'resources/system_resource'),
          'callback' => 'variable_get',
          'access arguments' => array('get a system variable'),
          'access arguments append' => FALSE,
          'args' => array(
            array(
              'name' => 'name',
              'optional' => FALSE,
              'source' => array('data' => 'name'),
              'description' => t('The name of the variable to return.'),
              'type' => 'string',
            ),
            array(
              'name' => 'default',
              'optional' => TRUE,
              'source' => array('data' => 'default'),
              'description' => t('The default value to use if this variable has never been set.'),
              'type' => 'string',
            ),
          ),
        ),
        'set_variable' => array(
          'help'   => 'Sets the value of a system variable using variable_set().',
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'resources/system_resource'),
          'callback' => 'variable_set',
          'access arguments' => array('set a system variable'),
          'access arguments append' => FALSE,
          'args' => array(
            array(
              'name' => 'name',
              'optional' => FALSE,
              'source' => array('data' => 'name'),
              'description' => t('The name of the variable to set.'),
              'type' => 'string',
            ),
            array(
              'name' => 'value',
              'optional' => FALSE,
              'source' => array('data' => 'value'),
              'description' => t('The value to set.'),
              'type' => 'string',
            ),
          ),
        ),
        'del_variable' => array(
          'help'   => 'Deletes a system variable using variable_del().',
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'resources/system_resource'),
          'callback' => 'variable_del',
          'access arguments' => array('set a system variable'),
          'access arguments append' => FALSE,
          'args' => array(
            array(
              'name' => 'name',
              'optional' => FALSE,
              'source' => array('data' => 'name'),
              'description' => t('The name of the variable to delete.'),
              'type' => 'string',
            ),
          ),
        ),
      ),
    ),
  );
}

/**
 * Returns the details of currently logged in user.
 *
 * @return
 *   object with session id, session name and a user object.
 */
function _system_resource_connect() {
  global $user;
  services_remove_user_data($user);

  $return = new stdClass();
  $return->sessid = session_id();
  $return->session_name = session_name();
  $return->user = $user;

  return $return;
}
