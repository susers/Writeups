<?php

/**
 * @file Documentation about Services alter hooks and variables.
 */

/**
 * Variables.
 *
 * 'rest_server_default_response_format' default value 'json' -- default response format for REST server.
 */

/**
 * Allow to alter arguments before they are passed to service callback.
 *
 * @param $controller
 *   Controller definition
 * @param $args
 *   Array of arguments
 * @param $options
 *
 * @see services_controller_execute()
 * @see services.runtime.inc
 */
function hook_services_request_preprocess_alter($controller, &$args, $options) {

}

/**
 * Alter results of the services call.
 *
 * @param $controller
 *   Controller definition
 * @param array $args
 *   Arguments passed to services callback
 * @param array $result
 *   Array of results that can be altered
 *
 * @see services_controller_execute()
 * @see services.runtime.inc
 */
function hook_services_request_postprocess_alter($controller, $args, &$result) {

}

/**
 * Allows alteration of the services_resources array.
 *
 * @param array $resources
 *   The combined array of resource definitions from hook_services_resources.
 * @param array $endpoint
 *   An array describing the endpoint that resources are being built for.
 */
function hook_services_resources_alter(&$resources, &$endpoint) {

}

/**
 * Allows alteration of the services_resources array after processing
 *
 * @param array $resources
 *   The combined array of resource definitions from hook_services_resources.
 * @param array $endpoint
 *   An array describing the endpoint that resources are being built for.
 *
 * @deprecated
 * @see _services_build_resources()
 * @see services.resource_build.inc
 */
function hook_services_resources_post_processing_alter(&$resources, &$endpoint) {

}

/**
 * Allows alteration of authentication methods.
 *
 * The paramaters passed to this function are a bit difficult to understand.
 * Example params:
 *   $info = services_services_authentication_info();
 *   $module = 'services';
 *
 * @param array $info
 *   The array describing the authentication method provided by $module.
 * @param string $module
 *   The module providing the authentication method.
 *
 * @see services_authentication_info()
 */
function hook_services_authentication_info_alter(&$info, $module) {

}

/**
 * Allows alteration of controller settings for services endpoints
 *
 * @param array $controller_info
 *   An array of controller settings added by other modules. By default, this is
 *   an empty array.
 *
 * @see services_edit_form_endpoint_resources()
 */
function hook_controller_settings_alter(&$controller_info) {

}

/**
 * Allows alteration of the REST server request parser list.
 *
 * @param array $parsers
 *   An associative array of parser callbacks keyed by mime-type.
 *
 * @see rest_server_request_parsers()
 */
function hook_rest_server_request_parsers_alter(&$parsers) {

}

/**
 * Allows alteration of the REST server response formatter list.
 *
 * @param array $formatters
 *  An associative array of formatter info arrays keyed by type extension.
 *
 * @see rest_server_response_formatters()
 */
function hook_rest_server_response_formatters_alter(&$formatters) {

}

/**
 * Allows alteration of the response when just the endpoint is requested.
 *
 * @param string $response
 *   The text displayed to the user.
 *
 * @see RESTServer->handle()
 */
function hook_services_endpoint_response_alter(&$response) {

}

/**
 * Allows alteration of the parsed request data before calling the controller.
 *
 * @param array $data
 *   The parsed request data.
 * @param array $controller
 *   The current controller definition.
 *
 * @see RESTServer->getControllerArguments()
 */
function hook_rest_server_request_parsed_alter(&$data, $controller) {

}

/**
 * Allows alteration of the parsed request headers before calling the controller.
 *
 * @param array $data
 *   The parsed request data.
 *
 * @see RESTServer->getControllerArguments()
 */
function hook_rest_server_headers_parsed_alter(&$headers) {

}

/**
 * Allows alteration of error data before the status code or message are returned.
 *
 * @param array $error_alter_array
 *   An associative array with the following keys:
 *     - 'code': the HTTP status code.
 *     - 'header_message': the message returned as part of the error response
 *       (for instance, "404 Not found").
 *     - 'body_data': data that was passed to the thrown exception.
 * @param array $controller
 *   The current controller definition.
 * @param array $arguments
 *   Arguments passed to the current controller.
 *
 * @see RESTServer->handleException()
 */
function hook_rest_server_execute_errors_alter(&$error_alter_array, $controller, $arguments) {

}

/**
 * Allows alteration of the user object after services removes sensitive information.
 *
 * @param object $user
 *   A user object without the 'pass' attribute, and if the current user doesn't
 *   have the 'administer users' permission, this will also not include the
 *   'mail' or 'init' attributes.
 *
 * @see services_remove_user_data()
 */
function hook_services_account_object_alter(&$user) {

}
