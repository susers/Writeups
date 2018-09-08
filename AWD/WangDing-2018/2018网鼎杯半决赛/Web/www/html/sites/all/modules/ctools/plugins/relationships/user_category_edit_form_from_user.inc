<?php

/**
 * @file
 * Plugin to provide an relationship handler for term from node.
 */

/**
 * Plugins are described by creating a $plugin array which will be used
 * by the system that includes this file.
 */
$plugin = array(
  'title' => t('User category edit form from user'),
  'keyword' => 'user_category_form',
  'description' => t('Adds user category edit form from a user context.'),
  'required context' => new ctools_context_required(t('User'), 'user'),
  'context' => 'ctools_user_category_edit_form_from_user_context',
  'edit form' => 'ctools_user_category_edit_form_from_user_settings_form',
  'defaults' => array('category' => NULL),
);

/**
 * Return a new context based on an existing context.
 */
function ctools_user_category_edit_form_from_user_context($context, $conf) {
  if (empty($context->data)) {
    return ctools_context_create_empty('user_edit_form', NULL);
  }

  if (!empty($conf['category'])) {
    return ctools_context_create('user_edit_form', $context->data, array('category' => $conf['category']));
  }

  if (isset($context->data->user_category)) {
    return ctools_context_create('user_edit_form', $context->data, array('category' => $context->data->user_category));
  }

  return ctools_context_create('user_edit_form', $context->data);
}

/**
 * Settings form for the relationship.
 */
function ctools_user_category_edit_form_from_user_settings_form($form, &$form_state) {
  $conf = $form_state['conf'];

  $categories = _user_categories();
  $options = array();
  foreach ($categories as $category) {
    $options[$category['name']] = $category['title'];
  }
  $form['category'] = array(
    '#type' => 'select',
    '#title' => t('Category'),
    '#options' => $options,
    '#default_value' => isset($conf['category']) ? $conf['category'] : NULL,
    '#empty_option' => 'Default',
  );

  return $form;
}
