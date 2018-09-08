<?php

/**
 * @file
 * Hooks provided by Services for the definition of authentication plugins.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Supplies information about a given authentication method to Services.
 *
 * @return
 *   An associative array with information about the authentication method
 *   and its callbacks. The possible keys are as follows (all keys are
 *   optional unless noted).
 *
 *   - title (required): The display name for this authentication method.
 *   - description (required): Longer text describing this authentciation
 *     method.
 *   - authenticate_call (required): The name of a function to be called
 *     to perform the actual authentication. <details of params/return>
 *   - security_settings: A callback function which returns an associative
 *     array of Form API elements for a settings form.
 *   - default_security_settings: A callback funtion which returns an array
 *     with the default settings for the auth module.
 *   - _services_security_settings_validate: The name of a standard form
 *     validation callback for the form defined in 'security_settings'.
 *   - _services_security_settings_submit: The name of a standard form
 *     submit callback for the form defined in 'security_settings'.
 *   - alter_controllers: The name of a callback function which will alter a
 *     services method signature in order to add required arguments.
 *   - controller_settings: A callback function which returns an associative
 *     array of Form API elements for a controller settings form.
 *   - file: An include file which contains the authentication callbacks.
 */
function hook_services_authentication_info() {

}
