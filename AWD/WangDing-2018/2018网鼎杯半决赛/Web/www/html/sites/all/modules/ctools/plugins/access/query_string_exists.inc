<?php

/**
 * @file
 * Plugin for controlling access based on the existence of a query string.
 */

$plugin = array(
  'title' => t('Query string exists'),
  'description' => t('Control access by whether or not a query string exists.'),
  'callback' => 'ctools_query_string_exists_ctools_access_check',
  'settings form' => 'ctools_query_string_exists_ctools_access_settings',
  'summary' => 'ctools_query_string_exists_ctools_access_summary',
  'defaults' => array('key' => ''),
);

/**
 * Settings form.
 */
function ctools_query_string_exists_ctools_access_settings($form, &$form_state, $config) {
  $form['settings']['key'] = array(
    '#title' => t('Query string key'),
    '#description' => t('Enter the key of the query string.'),
    '#type' => 'textfield',
    '#required' => TRUE,
    '#default_value' => $config['key'],
  );

  return $form;
}

/**
 * Check for access.
 */
function ctools_query_string_exists_ctools_access_check($config, $context) {
  return isset($_GET[$config['key']]);
}

/**
 * Provide a summary description.
 */
function ctools_query_string_exists_ctools_access_summary($config, $context) {
  return t('@identifier exists', array('@identifier' => $config['key']));
}
