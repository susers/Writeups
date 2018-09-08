<?php

/**
 * @file
 * Plugin to provide an relationship handler for entities from query string.
 */

/**
 * Plugins are described by creating a $plugin array which will be used
 * by the system that includes this file.
 */
$plugin = array(
  'title' => t('Entity from query string'),
  'keyword' => 'query_string_entity',
  'description' => t('Entity from query string.'),
  'required context' => new ctools_context_required(t('Query string'), 'query_string'),
  'context' => 'ctools_entity_from_query_string_context',
  'edit form' => 'ctools_entity_from_query_string_settings_form',
);

/**
 * Return a new context based on an existing context.
 */
function ctools_entity_from_query_string_context($context, $conf) {
  $entity_type = $conf['entity_type'];

  // If unset it wants a generic, unfilled context, which is just NULL.
  if (empty($context->data) || !isset($context->data) || !is_numeric($context->data)) {
    return ctools_context_create_empty('entity:' . $entity_type, NULL);
  }

  if (!empty($context->data) && is_numeric($context->data)) {
    // Load the entity from the query string value.
    $entity_id = $context->data;
    $entity = entity_load_single($entity_type, $entity_id);

    // Send it to ctools.
    return ctools_context_create('entity:' . $entity_type, $entity);
  }
}

/**
 * Settings form for the relationship.
 */
function ctools_entity_from_query_string_settings_form($form, &$form_state) {

  // Get all avalible enity types.
  foreach (entity_get_info() as $key => $value) {
    $entity_types[$key] = $value['label'];
  }

  $form['entity_type'] = array(
    '#title' => t('Entity type'),
    '#description' => t('Choose entity type to load from query value'),
    '#type' => 'select',
    '#options' => $entity_types,
  );
  if (isset($form_state['conf']['entity_type'])) {
    $form['entity_type']['#default_value'] = $form_state['conf']['entity_type'];
  }

  return $form;
}

/**
 * Submit handler; settings form for the context.
 */
function ctools_entity_from_query_string_settings_form_submit($form, &$form_state) {
  $form_state['conf']['entity_type'] = $form_state['values']['entity_type'];
}
