<?php

/**
 * @file
 * Include file for services_oauth module.
 */

/**
 * Authenticates a call using OAuth to verify the request.
 *
 * @param array $settings
  *  The settings for the authentication module.
 * @param array $method
 *  The method that's being called
 * @param array $args
 *  The arguments that are being used to call the method
 * @return void|string
 *  Returns nothing, or a error message if authentication fails
 */
function _services_oauth_authenticate_call($settings, $method, $args) {
  $endpoint = array();
  // Grep the specific settings for this method
  if (!empty($method['endpoint']['services_oauth']['credentials'])) {
    $endpoint += array_filter($method['endpoint']['services_oauth']);
  }
  $endpoint += $settings;
  $cred       = isset($endpoint['credentials']) ? $endpoint['credentials'] : 'token';
  $auth_level = isset($endpoint['authorization']) ? $endpoint['authorization'] : '*';

  // If no credentials are needed we'll pass this one through
  if ($cred == 'none') {
    return FALSE;
  }

  try {
    module_load_include('inc', 'oauth_common');

    list($signed, $consumer, $token) = oauth_common_verify_request();

    if (!$signed && ($cred == 'consumer' || $cred == 'token')) {
      throw new OAuthException('The request must be signed');
    }
    if ($consumer == NULL) {
      throw new OAuthException('Missing consumer token');
    }
    if ($consumer->context !== $settings['oauth_context']) {
      throw new OAuthException('The consumer is not valid in the current context');
    }

    // Validate the token, if it's required by the method
    if ($cred == 'token') {
      if (empty($token->key)) {
        throw new OAuthException('Missing access token');
      }
      if (!$token->authorized) {
        throw new OAuthException('The access token is not authorized');
      }
      // Check that the consumer has been granted the required authorization level
      if (!empty($auth_level) && !in_array('*', $token->services) && !in_array($auth_level, $token->services)) {
        throw new OAuthException('The consumer is not authorized to access this service');
      }
    }

    // Add the oauth authentication info to server info
    services_set_server_info('oauth_consumer', $consumer);
    services_set_server_info('oauth_token', $token);

    // Load the user if the request was authenticated using a token
    // that's associated with a account.
    if ($cred == 'token') {
      if ($token->uid) {
        global $user;
        $user = user_load($token->uid);
      }
    }
    else if ($cred == 'consumer') {
      if ($consumer->uid) {
        // This authenticates as the user who owns 'key';  It is for 2-stage
        // OAuth and is vastly inferior to 3-stage OAuth.
        global $user;
        $user = user_load($consumer->uid);
      }
    }
  }
  catch (OAuthException $e) {
    drupal_add_http_header('WWW-Authenticate', sprintf('OAuth realm="%s"', url('', array('absolute' => TRUE))));
    return $e->getMessage();
  }
}

function _services_oauth_security_settings_authorization($settings) {
  return isset($settings['authorization']) ? $settings['authorization'] : '';
}

function _services_oauth_security_settings($settings, &$form_state) {
  if (isset($form_state['values']['services_oauth']['oauth_context'])) {
    $settings['oauth_context'] = $form_state['values']['services_oauth']['oauth_context'];
  }

  $form = array();
  $form['oauth_context'] = array(
    '#type'          => 'select',
    '#options'       => array('' => t('-- Select an OAuth context')),
    '#default_value' => isset($settings['oauth_context']) ? $settings['oauth_context'] : '',
    '#title'         => t('OAuth context'),
    '#description'   => t('The OAuth contexts provides a scope for consumers and authorizations and have their own authorization levels. Different services endpoints may share OAuth contexts and thereby allow the use of consumers and tokens across the services endpoint boundraries.'),
  );
  $form['authorization'] = array(
    '#type'          => 'select',
    '#options'       => array(),
    '#default_value' => isset($settings['authorization']) ? $settings['authorization'] : '',
    '#title'         => t('Default required OAuth Authorization level'),
    '#description'   => t('The default OAuth authorization level that will be required to access resources.'),
  );

  $contexts = oauth_common_context_load_all();
  foreach ($contexts as $context) {
    $form['oauth_context']['#options'][$context->name] = $context->title;
    if (isset($context->authorization_levels) && $context->name == $settings['oauth_context']) {
      foreach ($context->authorization_levels as $name => $level) {
        $form['authorization']['#options'][$name] = t($level['title']) . " ({$context->name})";
      }
    }
  }
  if (empty($form['authorization']['#options'])) {
    $form['authorization'] = array(
      '#type' => 'item',
      '#title' => t('Select an OAuth context enable default required OAuth Authorization level')
    ) + $form['authorization'];
  }
  else {
    $form['authorization']['#options'] = array('' => t('None')) + $form['authorization']['#options'];
  }

  $form['credentials'] = array(
    '#type'          => 'select',
    '#options'       => array(
      'none'              => t('None, OAuth authentication will be disabled by default'),
      'unsigned_consumer' => t('Unsigned with consumer key'),
      'consumer'          => t('Consumer key, also known as 2-legged OAuth'),
      'token'             => t('Consumer key and access token, also known as 3-legged OAuth'),
    ),
    '#default_value' => $settings['credentials'],
    '#title'         => t('Default required authentication'),
    '#description'   => t('Authorization levels will <em>not</em> be applied if the consumer isn\'t required to supply a access token.'),
  );

  return $form;
}

function _services_oauth_default_security_settings() {
  return array(
    'oauth_context' => '',
    'authorization' => '',
    'credentials' => 'token',
  );
}

function _services_oauth_controller_settings($settings, $controller, $endpoint, $class, $name) {
  $form = array();
  $cc = array(
    'credentials' => '',
    'authorization' => '',
  );
  if (!empty($controller['endpoint']['services_oauth'])) {
     $cc = $controller['endpoint']['services_oauth'] + $cc;
  }
  $auth_levels = array();
  if (is_array($endpoint) && isset($endpoint['oauth_context'])) {
    $context = oauth_common_context_load($endpoint['oauth_context']);
    if (isset($context->authorization_levels)) {
      foreach ($context->authorization_levels as $name => $level) {
        $auth_levels[$name] = t($level['title']);
      }
    }
  }

  $form['credentials'] = array(
    '#type'          => 'select',
    '#options'       => array(
      ''                  => t('Default'),
      'none'              => t('None'),
      'unsigned_consumer' => t('Unsigned with consumer key'),
      'consumer'          => t('Consumer key'),
      'token'             => t('Consumer key and access token'),
    ),
    '#default_value' => isset($settings['credentials']) ? $settings['credentials'] : '',
    '#title'         => t('Required authentication'),
    '#description'   => t('Authorization levels will <em>not</em> be applied if the consumer isn\'t required to supply a access token.'),
  );

  $form['authorization'] = array(
    '#type'          => 'select',
    '#options'       => array(
      '' => t('Default'),
    ) + $auth_levels,
    '#default_value' => isset($settings['authorization']) ? $settings['authorization'] : '',
    '#title'         => t('Required authorization'),
  );

  return $form;
}
