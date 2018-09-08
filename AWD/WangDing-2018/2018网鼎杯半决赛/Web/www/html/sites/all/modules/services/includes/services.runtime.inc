<?php

/**
 * @file
 *  Contains functions that only are necessary when a service call is made.
 *  This has broken out so that this code isn't loaded for every page load.
 */

/**
 * A exception thrown by services and related modules when something goes
 * wrong.
 */
class ServicesException extends Exception {
  private $data;

  /**
   * Constructor for the ServicesException.
   *
   * @param string $message
   *  Error message.
   * @param int $code
   *  Optional. Error code. This often maps to the HTTP status codes. Defaults
   *  to 0.
   * @param mixed $data
   *  Information that can be used by the server to return information about
   *  the error.
   */
  public function __construct($message, $code = 0, $data = NULL) {
    parent::__construct($message, $code);

    $this->data = !empty($data) ? $data : $message;
  }

  /**
   * Returns the data associated with the exception.
   *
   * @return mixed
   */
  public function getData() {
    return $this->data;
  }
}

/**
 * A exception thrown by services and related modules when an error related to
 * a specific argument is encountered.
 */
class ServicesArgumentException extends ServicesException {
  private $argument;

  /**
   * Constructor for the ServicesException.
   *
   * @param string $message
   *  Error message.
   * @param string $argument_name
   *  The name of the argument that caused the error.
   * @param int $code
   *  Optional. Error code. This often maps to the HTTP status codes. Defaults
   *  to 0.
   * @param mixed $data
   *  Information that can be used by the server to return information about
   *  the error.
   */
  public function __construct($message, $argument_name, $code, $data) {
    parent::__construct($message, $code, $data);

    $this->argument = $argument_name;
  }

  /**
   * Returns the name of the argument that caused the error.
   *
   * @return string
   *  The name of the argument.
   */
  public function getArgumentName() {
    return $this->argument;
  }
}

/**
 * Performs access checks and executes a services controller.
 * This method is called by server implementations.
 *
 * @param array $controller
 *  An array containing information about the controller
 * @param array $args
 *  The arguments that should be passed to the controller.
 * @param array $options
 *  Options for the execution. Use 'skip_authentication'=>TRUE to skip the
 *  services-specific authentication checks. Access checks will always be
 *  made.
 */
function services_controller_execute($controller, $args = array(), $options = array()) {
  $server_info =  services_server_info_object();
  if (!empty($server_info->debug)) {
    watchdog('services', 'Controller: <pre>@controller</pre>', array('@controller' => print_r($controller, TRUE)), WATCHDOG_DEBUG);
    watchdog('services', 'Passed arguments: <pre>@arguments</pre>', array('@arguments' => print_r($args, TRUE)), WATCHDOG_DEBUG);
  }

  // Switch to anonymous user preserving currently session authenticated user.
  _services_controller_execute_preserve_user_switch_anonymous($controller);

  try {
    $result = _services_controller_execute_internals($controller, $args, $options);
  }
  catch (Exception $exception) {
    _services_controller_execute_restore_user();
    throw $exception;
  }

  // Restore user only if callback is not logout.
  $restore_user = !is_string($controller['callback']) || strpos($controller['callback'], 'logout') === FALSE;
  if ($restore_user) {
    _services_controller_execute_restore_user();
  }

  if (!empty($server_info->debug)) {
    watchdog('services', 'results: <pre>@results</pre>', array('@results' => print_r($result, TRUE)), WATCHDOG_DEBUG);
  }
  return $result;
}

/**
 * As authentication methods should authenticate user themselves changing global $user variable
 * we preserve incoming session authenticated user and his session so changes made by authentication
 * do not interfere.
 */
function _services_controller_execute_preserve_user_switch_anonymous($controller) {
  global $user;
  services_set_server_info('original_user', $user);

  $preserve_session = !user_is_anonymous();
  services_set_server_info('preserve_session', $preserve_session);
  if ($preserve_session) {
    $original_session_state = drupal_save_session();
    services_set_server_info('original_session_state', $original_session_state);
    drupal_save_session(FALSE);
  }

  $user = drupal_anonymous_user();
  $user->timestamp = time();
}

function _services_controller_execute_restore_user() {
  if (services_get_server_info('preserve_session', FALSE)) {
    $original_user = services_get_server_info('original_user');
    $original_session_state = services_get_server_info('original_session_state');
    global $user;
    $user = $original_user;
    drupal_save_session($original_session_state);
  }
}

/**
 * Internals of the services_controller_execute().
 *
 * Arguments are the same as services_controller_execute().
 */
function _services_controller_execute_internals($controller, $args, $options) {
  $server_info =  services_server_info_object();
  $endpoint_name = services_get_server_info('endpoint');
  $endpoint = services_endpoint_load($endpoint_name);

  _services_authenticate_user($controller, $endpoint, $args, $options);

  // Load the proper file.
  if (!empty($controller['file']) && $file = $controller['file']) {
    module_load_include($file['type'], $file['module'], (isset($file['name']) ? $file['name'] : NULL));
  }

  _services_run_access_callback($controller, $args);

  $endpoint_path = services_get_server_info('endpoint_path', '');
  $endpoint_path_len = drupal_strlen($endpoint_path) + 1;
  $canonical_path = drupal_substr($_GET['q'], $endpoint_path_len, drupal_strlen($_GET['q']) - $endpoint_path_len);

  // Prepare $path array and $resource_name.
  $path = explode('/', $canonical_path);
  $resource_name = array_shift($path);
  $options['version'] = _services_version_header_options() ? _services_version_header_options() : NULL;
  $options['resource'] = $resource_name;
  //because index is never filled, we have to add this on an else, $remove_index is just a paranoid variable to make sure that futher code flow is not altered.
  $remove_index = FALSE;

  if (isset($path[0])) {
    $options['method'] = $path[0];
  } else{
    $options['method'] = 'index';
    $remove_index = TRUE;
  }
  if (!empty($options['version'])) {
    services_request_apply_version($controller, $options);
  }
  if($remove_index){
    unset($options['method']);
  }
  drupal_alter('services_request_preprocess', $controller, $args, $options);

  // Log the arguments.
  if (!empty($server_info->debug)) {
    watchdog('services', 'Called arguments: <pre>@arguments</pre>', array('@arguments' => print_r($args, TRUE)), WATCHDOG_DEBUG);
  }

  // Execute the controller callback.
  $result = call_user_func_array($controller['callback'], $args);

  drupal_alter('services_request_postprocess', $controller, $args, $result);

  return $result;
}

/**
 * Gets information about a authentication module.
 *
 * @param string $module
 *  The module to get info for.
 * @return mixed
 *  The information array, or FALSE if the information wasn't found.
 */
function services_authentication_info($module) {
  $info = FALSE;
  if (!empty($module) && module_exists($module) && is_callable($module . '_services_authentication_info')) {
    $info = call_user_func($module . '_services_authentication_info');
  }
  return $info;
}

/**
 * Invokes a authentication module callback.
 *
 * @param string $module
 *  The authentication module to invoke the callback for.
 * @param string $method
 *  The callback to invoke.
 * @param string $arg1
 *  Optional. First argument to pass to the callback.
 * @param string $arg2
 *  Optional. Second argument to pass to the callback.
 * @param string $arg3
 *  Optional. Third argument to pass to the callback.
 * @return mixed
 *
 * Aren't these really the following?
 *  arg1 = Settings
 *  arg2 = Method
 *  arg3 = Controller
 *
 */
function services_auth_invoke($module, $method, &$arg1 = NULL, &$arg2 = NULL, &$arg3 = NULL) {
  // Get information about the auth module
  $info = services_authentication_info($module);
  drupal_alter('services_authentication_info', $info, $module);
  $func = $info && !empty($info[$method]) ? $info[$method] : FALSE;
  if (!$func) {
    return TRUE;
  }

  if (!empty($info['file'])) {
    require_once(drupal_get_path('module', $module) . '/' . $info['file']);
  }

  if (is_callable($func)) {
    $args = func_get_args();
    // Replace module and method name and arg1 with reference to $arg1 and $arg2.
    array_splice($args, 0, 5, array(&$arg1, &$arg2, &$arg3));
    return call_user_func_array($func, $args);
  }
}

/**
 * Formats a resource uri using the formatter registered through
 * services_set_server_info().
 *
 * @param array $path
 *  An array of strings containing the component parts of the path to the resource.
 * @return string
 *  Returns the formatted resource uri, or NULL if no formatter has been registered.
 */
function services_resource_uri($path) {
  $endpoint_name = services_get_server_info('endpoint');
  $endpoint = services_endpoint_load($endpoint_name);
  if (!empty($path[0]) && !empty($endpoint->resources[$path[0]]['alias'])) {
    $path[0] = $endpoint->resources[$path[0]]['alias'];
  }
  $formatter = services_get_server_info('resource_uri_formatter');
  if ($formatter) {
    return call_user_func($formatter, $path);
  }
  return NULL;
}

/**
 * Sets a server info value
 *
 * @param string $key
 *  The key of the server info value.
 * @param mixed $value
 *  The value.
 * @return void
 */
function services_set_server_info($key, $value) {
  $info = services_server_info_object();
  $info->$key = $value;
}

/**
 * Sets multiple server info values from a associative array.
 *
 * @param array $values
 *  An associative array containing server info values.
 * @return void
 */
function services_set_server_info_from_array($values) {
  $info = services_server_info_object();
  foreach ($values as $key => $value) {
    $info->$key = $value;
  }
}

/**
 * Gets a server info value.
 *
 * @param string $key
 *  The key for the server info value.
 * @param mixed $default
 *  The default value to return if the value isn't defined.
 * @return mixed
 *  The server info value.
 */
function services_get_server_info($key, $default = NULL) {
  $info = services_server_info_object();
  $value = $default;
  if (isset($info->$key)) {
    $value = $info->$key;
  }
  return $value;
}

/**
 * Gets the server info object.
 *
 * @param bool $reset
 *  Pass TRUE if the server info object should be reset.
 * @return object
 *  Returns the server info object.
 */
function services_server_info_object($reset = FALSE) {
  static $drupal_static_fast;
  if (!isset($drupal_static_fast) || $reset) {
    $drupal_static_fast['info'] = &drupal_static(__FUNCTION__, new stdClass());
  }
  return $drupal_static_fast['info'];
}

/**
 * Prepare an error message for returning to the server.
 *
 * @param string $message
 *  Error message.
 * @param int $code
 *  Optional. Error code. This often maps to the HTTP status codes. Defaults
 *  to 0.
 * @param mixed $data
 *  Optional. Information that can be used by the server to return information about the error. Defaults to null.
 * @return mixed
 */
function services_error($message, $code = 0, $data = NULL) {
  throw new ServicesException($message, $code, $data);
}

/**
 * Extract arguments for a services method callback, preserving backwards compatibility with #1083242.
 *
 * @param array $data
 *  original argument passed to a resource method callback
 * @param string $field
 *  name of the field where arguments should be checked for
 * @return array
 */

// Adds backwards compatability with regression fixed in #1083242
function _services_arg_value($data, $field) {
  if (isset($data[$field]) && count($data) == 1 && is_array($data[$field])) {
    return $data[$field];
  }
  return $data;
}


/**
 * Extract arguments for a services method access callback, preserving backwards compatibility with #1083242.
 *
 * @param string $data
 *  original argument passed to a resource method callback
 * @param mixed $fields
 *  name of the field(s) where arguments should be checked for, either as a string or as an array of strings
 * @return array
 */

// Adds backwards compatability with regression fixed in #1083242
function _services_access_value($data, $fields) {

  if (!is_array($fields)) {
    $fields = array($fields);
  }

  foreach ($fields as $field) {
    if (is_array($data) && isset($data[$field]) && count($data) == 1) {
      return $data[$field];
    }
  }
  return $data;
}

/**
 * Run enabled authentication plugins.
 *
 * Plugin that authenticate user will change global $user.
 */
function _services_authenticate_user($controller, $endpoint, $args, $options) {
  if (empty($endpoint->authentication)) {
    return NULL;
  }
  if (!isset($options['skip_authentication']) || !$options['skip_authentication']) {
    foreach ($endpoint->authentication as $auth_module => $auth_settings) {
      if (!empty($auth_settings)) {
        // Add in the auth module's endpoint settings if present.
        if (isset($controller['endpoint'][$auth_module])) {
          if(is_array($auth_settings)) {
              $auth_settings += $controller['endpoint'][$auth_module];
          }
        }
        if ($auth_error = services_auth_invoke($auth_module, 'authenticate_call', $auth_settings, $controller, $args)) {
          return services_error($auth_error, 401);
        }
      }
    }
  }
}

/**
 * Call access callback of the method.
 */
function _services_run_access_callback($controller, $args) {
  // Construct access arguments array.
  if (isset($controller['access arguments'])) {
    $access_arguments = $controller['access arguments'];
    if (isset($controller['access arguments append']) && $controller['access arguments append']) {
      $access_arguments[] = $args;
    }
  }
  else {
    // Just use the arguments array if no access arguments have been specified
    $access_arguments = $args;
  }

  // Load the proper file for the access callback.
  if (!empty($controller['access callback file']) && $access_cb_file = $controller['access callback file']) {
    $access_cb_file_name = isset($access_cb_file['name']) ? $access_cb_file['name'] : NULL;
    module_load_include($access_cb_file['type'], $access_cb_file['module'], $access_cb_file_name);
  }

  // Call default or custom access callback.
  if (call_user_func_array($controller['access callback'], $access_arguments) != TRUE) {
    global $user;
    return services_error(t('Access denied for user @user', array(
      '@user' => isset($user->name) ? $user->name : 'anonymous',
    )), 403);
  }
}
