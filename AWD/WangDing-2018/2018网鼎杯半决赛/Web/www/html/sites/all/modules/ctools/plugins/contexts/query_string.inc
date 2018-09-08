<?php

/**
 * @file
 * Context plugin that can extract arbitrary values from the query string.
 */

/**
 * $plugin array which will be used by the system that includes this file.
 */
$plugin = array(
  'title' => t('Query string value'),
  'description' => t('A context that extracts a value from the query string.'),
  'context' => 'ctools_context_query_string_create_query_string',
  'context name' => 'query_string',
  'keyword' => 'query_string',
  'edit form' => 'ctools_context_query_string_settings_form',
  'convert list' => array(
    'raw' => t('Raw string'),
    'html_safe' => t('HTML-safe string'),
  ),
  'convert' => 'ctools_context_query_string_convert',
);

/**
 * Create a context from manual configuration.
 */
function ctools_context_query_string_create_query_string($empty, $data = NULL, $conf = FALSE) {
  $context = new ctools_context('query_string');
  $context->plugin = 'query_string';

  if ($empty) {
    return $context;
  }

  if ($conf) {
    if (!empty($_GET[$data['key']])) {
      $context->data = $_GET[$data['key']];
    }
    else {
      $context->data = $data['fallback_value'];
    }
  }
  return $context;
}

/**
 * Form builder; settings for the context.
 */
function ctools_context_query_string_settings_form($form, &$form_state) {
  $form['key'] = array(
    '#title' => t('Query string key'),
    '#description' => t('Enter the key of the value that must be returned from the query string.'),
    '#type' => 'textfield',
    '#required' => TRUE,
  );
  if (isset($form_state['conf']['key'])) {
    $form['key']['#default_value'] = $form_state['conf']['key'];
  }
  $form['fallback_value'] = array(
    '#title' => t('Fallback value'),
    '#description' => t('Enter a value that must be returned if the above specified key does not exist in the query string.'),
    '#type' => 'textfield',
  );
  if (!empty($form_state['conf']['fallback_value'])) {
    $form['fallback_value']['#default_value'] = $form_state['conf']['fallback_value'];
  }
  return $form;
}

/**
 * Submit handler; settings form for the context.
 */
function ctools_context_query_string_settings_form_submit($form, &$form_state) {
  $form_state['conf']['key'] = $form_state['values']['key'];
  $form_state['conf']['fallback_value'] = $form_state['values']['fallback_value'];
}

/**
 * Convert a context into a string.
 */
function ctools_context_query_string_convert($context, $type) {
  switch ($type) {
    case 'raw':
      return $context->data;

    case 'html_safe':
      return check_plain($context->data);
  }
}
