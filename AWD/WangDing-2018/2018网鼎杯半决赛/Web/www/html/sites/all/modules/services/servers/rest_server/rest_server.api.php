<?php

/**
 * @file
 * Hooks provided by Services for the definition of servers.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Triggered when the REST server request a list of available request parsers.
 *
 * @param array $parsers
 *  An associative array of parser callbacks keyed by mime-type.
 * @return void
 */
function hook_rest_server_request_parsers_alter(&$parsers) {
  unset($parsers['application/x-www-form-urlencoded']);
}

/**
 * Triggered when the REST server request a list of supported response formats.
 *
 * @param array $formatters
 *  An associative array of formatter info arrays keyed by type extension. The
 *  formatter info specifies an array of 'mime types' that corresponds to the
 *  output format; a 'formatter class' class implements ServicesFormatterInterface
 *  and is responsible for encoding the output.
 * @return void
 */
function hook_rest_server_response_formatters_alter(&$formatters) {
  // Remove the jsonp response format.
  unset($formatters['jsonp']);
}

/**
 * Alter error messages right before delivering.
 *
 * @param array $errors
 *  Array of following properties:
 *   'code' -- error code
 *   'header_message' -- message that will be returned in Status header
 *   'body_data' -- data returned in the body of the response
 *  You can alter 'header_message' and 'body_data' in your hook implementations.
 * @param type $controller
 *  Executed controller.
 * @param type $arguments
 *  Arguments of the controller.
 */
function hook_rest_server_execute_errors_alter(&$error, $controller, $arguments) {
  $error_code = $error['code'];
  if (user_is_logged_in() && $error_code == 401) {
    global $user;
    $error['header_message'] = '403 ' . t('Access denied for user @user',
      array('@user' => $user->name));
  }
}
