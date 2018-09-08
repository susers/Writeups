<?php

/**
 * @file
 * Handle rendering entity fields as panes.
 */

$plugin = array(
  'title' => t('Entity field'),
  'defaults' => array('label' => 'title', 'formatter' => '', 'delta_limit' => 0, 'delta_offset' => '0', 'delta_reversed' => FALSE),
  'content type' => 'ctools_entity_field_content_type_content_type',
);

/**
 * Just one subtype.
 *
 * Ordinarily this function is meant to get just one subtype. However, we are
 * using it to deal with the fact that we have changed the subtype names. This
 * lets us translate the name properly.
 */
function ctools_entity_field_content_type_content_type($subtype) {
  $types = ctools_entity_field_content_type_content_types();
  if (isset($types[$subtype])) {
    return $types[$subtype];
  }
}

/**
 * Return all field content types available.
 */
function ctools_entity_field_content_type_content_types() {
  $types = &drupal_static(__FUNCTION__, array());
  if (!empty($types)) {
    return $types;
  }

  $cache_key = 'ctools_entity_field_content_type_content_types';
  if ($cache = cache_get($cache_key)) {
    $types = $cache->data;
    if (!empty($types)) {
      return $types;
    }
  }

  // This will hold all the individual field content types.
  $context_types = array();
  $entities = entity_get_info();

  $description = t('Field on the referenced entity.');
  $styles = t('Formatter Styles');
  $categories = array();
  foreach ($entities as $entity_type => $entity) {
    $category = t(ucfirst($entity_type));
    $categories[$entity_type] = $category;
    foreach ($entity['bundles'] as $type => $bundle) {
      foreach (field_info_instances($entity_type, $type) as $field_name => $field) {
        if (!isset($types[$entity_type . ':' . $field_name])) {
          $label = t($field['label']);
          $types[$entity_type . ':' . $field_name] = array(
            'category' => $category,
            'icon' => 'icon_field.png',
            'title' => t('Field: @widget_label (@field_name)', array(
              '@widget_label' => $label,
              '@field_name' => $field_name,
            )),
            'description' => $description,
            'edit form' => array(
              'ctools_entity_field_content_type_formatter_options' => array(
                'default' => TRUE,
                'title' => t('Formatter options for: @widget_label (@field_name)', array(
                  '@widget_label' => $label,
                  '@field_name' => $field_name,
                )),
              ),
              'ctools_entity_field_content_type_formatter_styles' => $styles,
            ),
          );
        }
        $context_types[$entity_type . ':' . $field_name]['types'][$type] = $bundle['label'];
      }
    }
  }

  // Create the required context for each field related to the bundle types.
  foreach ($types as $key => $field_content_type) {
    list($entity_type, $field_name) = explode(':', $key, 2);
    $types[$key]['required context'] = new ctools_context_required($categories[$entity_type], $entity_type, array(
      'type' => array_keys($context_types[$key]['types']),
    ));
    unset($context_types[$key]['types']);
  }

  cache_set($cache_key, $types);

  return $types;
}

/**
 * Render the custom content type.
 */
function ctools_entity_field_content_type_render($subtype, $conf, $panel_args, $context) {
  if (empty($context) || empty($context->data)) {
    return;
  }

  // Get a shortcut to the entity.
  $entity = $context->data;
  list($entity_type, $field_name) = explode(':', $subtype, 2);

  // Load the entity type's information for this field.
  $ids = entity_extract_ids($entity_type, $entity);
  $field = field_info_instance($entity_type, $field_name, $ids[2]);

  // Do not render if the entity type does not have this field.
  if (empty($field)) {
    return;
  }
  $language = field_language($entity_type, $entity, $field_name);

  if (empty($conf['label']) || $conf['label'] == 'title') {
    $label = 'hidden';
    $conf['label'] = 'title';
  }
  else {
    $label = $conf['label'];
  }

  $field_settings = array(
    'label' => $label,
    'type' => $conf['formatter'],
    // Pass all entity field panes settings to field display settings.
    'pane_settings' => $conf,
  );

  // Get the field output, and the title.
  if (!empty($conf['formatter_settings'])) {
    $field_settings['settings'] = $conf['formatter_settings'];
  }

  $clone = clone $entity;
  $all_values = field_get_items($entity_type, $entity, $field_name, $language);

  if (is_array($all_values)) {
    // Reverse values.
    if (isset($conf['delta_reversed']) && $conf['delta_reversed']) {
      $all_values = array_reverse($all_values, TRUE);
    }

    if (isset($conf['delta_limit'])) {
      $offset = intval($conf['delta_offset']);
      $limit = !empty($conf['delta_limit']) ? $conf['delta_limit'] : NULL;
      $all_values = array_slice($all_values, $offset, $limit, TRUE);
    }

    $clone->{$field_name}[$language] = $all_values;
  }

  $field_output = field_view_field($entity_type, $clone, $field_name, $field_settings, $language);

  if (!empty($field_output)) {
    if (!empty($conf['override_title'])) {
      $field_output['#title'] = filter_xss_admin($conf['override_title_text']);
    }
    $field_output['#ctools_context'] = $context;
    $field_output['#post_render'][] = 'ctools_entity_field_content_type_substitute_keywords';
  }

  // Build the content type block.
  $block = new stdClass();
  $block->module = 'entity_field';
  if ($conf['label'] == 'title' && isset($field_output['#title'])) {
    $block->title = $field_output['#title'];
  }

  $block->content = $field_output;
  $block->delta   = $ids[0];

  return $block;
}

/**
 * Replace context keywords.
 */
function ctools_entity_field_content_type_substitute_keywords($markup, array $element) {
  return ctools_context_keyword_substitute($markup, array(), array($element['#ctools_context']));
}

/**
 * Returns an edit form for custom type settings.
 */
function ctools_entity_field_content_type_formatter_options($form, &$form_state) {
  if (empty($form_state['conf']['formatter_settings'])) {
    $form_state['conf']['formatter_settings'] = array();
  }
  $conf = $form_state['conf'];
  $subtype = $form_state['subtype_name'];
  list($entity_type, $field_name) = explode(':', $subtype, 2);

  $field = field_info_field($field_name);
  module_load_include('inc', 'field_ui', 'field_ui.admin');
  $formatter_options = field_ui_formatter_options($field['type']);

  $field_label_options = array(
    'title' => t('Pane title'),
    'above' => t('Above'),
    'inline' => t('Inline'),
    'hidden' => t('Hidden'),
  );

  $form['label'] = array(
    '#type' => 'select',
    '#title' => t('Label'),
    '#options' => $field_label_options,
    '#default_value' => $conf['label'],
  );

  $form['formatter'] = array(
    '#type' => 'select',
    '#title' => t('Select a formatter'),
    '#options' => $formatter_options,
    '#default_value' => $conf['formatter'],
  );

  return $form;
}

function ctools_entity_field_content_type_formatter_options_submit($form, &$form_state) {
  $form_state['conf']['formatter'] = $form_state['values']['formatter'];
  $form_state['conf']['label'] = $form_state['values']['label'];
}

function ctools_entity_field_content_type_formatter_styles($form, &$form_state) {
  if (!$form_state['conf']['formatter_settings']) {
    $form_state['conf']['formatter_settings'] = array();
  }
  $conf = $form_state['conf'];
  $subtype = $form_state['subtype_name'];
  list($entity_type, $field_name) = explode(':', $subtype, 2);
  $field = field_info_field($field_name);

  ctools_form_include($form_state, 'field_ui.admin', 'field_ui', '');
  ctools_form_include($form_state, 'fields');

  $form['ctools_keywords'] = array(
    '#type' => 'item',
    '#description' => t('You may use keywords for substitutions.'),
  );

  $form['ctools_field_list'] = array(
    '#type' => 'value',
    '#value' => array(),
  );

  ctools_fields_get_field_formatter_settings_form($field, $conf['formatter'], $form, $form_state);
  return $form;
}

function ctools_entity_field_content_type_formatter_styles_submit($form, &$form_state) {
  $fields = $form_state['values']['ctools_field_list'];
  $formatter_info = ctools_fields_get_field_formatter_info($fields);
  foreach ($formatter_info as $info) {
    if (!empty($info['settings'])) {
      foreach ($info['settings'] as $field_name => $value) {
        if (isset($form_state['values'][$field_name])) {
          $form_state['conf']['formatter_settings'][$field_name] = $form_state['values'][$field_name];
        }
      }
    }
  }

  if (isset($form_state['values']['delta_limit'])) {
    $form_state['conf']['delta_limit'] = $form_state['values']['delta_limit'];
    $form_state['conf']['delta_offset'] = $form_state['values']['delta_offset'];
    $form_state['conf']['delta_reversed'] = $form_state['values']['delta_reversed'];
  }
}

/**
 * Returns the administrative title for a type.
 */
function ctools_entity_field_content_type_admin_title($subtype, $conf, $context) {
  list($bundle, $field_name) = explode(':', $subtype);
  ctools_include('fields');
  if (is_object($context) && isset($context->identifier)) {
    $identifier = $context->identifier;
  }
  else {
    watchdog('ctools_entity_field_content_type_admin_title', 'Context is missing for field: @name', array('@name' => $subtype), WATCHDOG_NOTICE);
    $identifier = t('Unknown');
  }

  return t('"@s" @field', array('@s' => $identifier, '@field' => ctools_field_label($field_name)));
}
