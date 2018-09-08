<?php

/**
 * @file
 * Plugin to provide a string context.
 */

/**
 * Plugins are described by creating a $plugin array which will be used
 * by the system that includes this file.
 */
$plugin = array(
  'title' => t('String'),
  'description' => t('A context that is just a string.'),
  'context' => 'ctools_context_create_string',
  'edit form' => 'ctools_context_string_settings_form',
  'defaults' => '',
  'keyword' => 'string',
  'no ui' => FALSE,
  'context name' => 'string',
  'convert list' => array(
    'raw' => t('Raw string'),
    'html_safe' => t('HTML-safe string'),
    'uppercase_words_html_safe' => t('Uppercase words HTML-safe string'),
  ),
  'convert' => 'ctools_context_string_convert',
  'placeholder form' => array(
    '#type' => 'textfield',
    '#description' => t('Enter the string for this context.'),
  ),
);

/**
 * It's important to remember that $conf is optional here, because contexts
 * are not always created from the UI.
 */
function ctools_context_create_string($empty, $data = NULL, $conf = FALSE) {
  // The input is expected to be an object as created by ctools_break_phrase
  // which contains a group of string.
  $context = new ctools_context('string');
  $context->plugin = 'string';

  if ($empty) {
    return $context;
  }

  if ($data !== FALSE) {
    // Support the array storage from the settings form but also handle direct input from arguments.
    $context->data = is_array($data) ? $data['string'] : $data;
    $context->title = ($conf) ? check_plain($data['identifier']) : check_plain($data);
    return $context;
  }
}

/**
 * Convert a context into a string.
 */
function ctools_context_string_convert($context, $type) {
  switch ($type) {
    case 'raw':
      return $context->data;

    case 'html_safe':
      return check_plain($context->data);

    case 'uppercase_words_html_safe':
      return ucwords(str_replace('-', ' ', check_plain($context->data)));
  }
}

/**
 * String settings form.
 */
function ctools_context_string_settings_form($form, &$form_state) {
  $conf = &$form_state['conf'];

  $form['string'] = array(
    '#title' => t('Enter the string'),
    '#type' => 'textfield',
    '#maxlength' => 512,
    '#weight' => -10,
    '#default_value' => $conf['string'],
  );

  return $form;
}

function ctools_context_string_settings_form_submit($form, &$form_state) {
  $form_state['conf']['string'] = $form_state['values']['string'];
}
