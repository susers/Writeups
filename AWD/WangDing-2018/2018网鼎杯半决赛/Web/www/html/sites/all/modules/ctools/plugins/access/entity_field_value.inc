<?php

/**
 * @file
 * Plugin to provide access control based upon entity bundle.
 */

$plugin = array(
  'title' => t("(Custom) Entity: Field Value"),
  'description' => t('Control access by entity field value.'),
  'callback' => 'ctools_entity_field_value_ctools_access_check',
  'default' => array('type' => array()),
  'settings form' => 'ctools_entity_field_value_ctools_access_settings',
  'settings form submit' => 'ctools_entity_field_value_ctools_access_settings_submit',
  'summary' => 'ctools_entity_field_value_ctools_access_summary',
  'get child' => 'ctools_entity_field_value_ctools_access_get_child',
  'get children' => 'ctools_entity_field_value_ctools_access_get_children',
);

function ctools_entity_field_value_ctools_access_get_child($plugin, $parent, $child) {
  $plugins = &drupal_static(__FUNCTION__, array());
  if (empty($plugins[$parent . ':' . $child])) {
    list($entity_type, $bundle_type, $field_name) = explode(':', $child);
    $plugins[$parent . ':' . $child] = _ctools_entity_field_value_ctools_access_get_child($plugin, $parent, $entity_type, $bundle_type, $field_name);
  }

  return $plugins[$parent . ':' . $child];
}

function ctools_entity_field_value_ctools_access_get_children($plugin, $parent) {
  $plugins = &drupal_static(__FUNCTION__, array());
  if (!empty($plugins)) {
    return $plugins;
  }
  $entities = entity_get_info();
  foreach ($entities as $entity_type => $entity) {
    foreach ($entity['bundles'] as $bundle_type => $bundle) {
      foreach (field_info_instances($entity_type, $bundle_type) as $field_name => $field) {
        if (!isset($plugins[$parent . ':' . $entity_type . ':' . $bundle_type . ':' . $field_name])) {
          $plugin = _ctools_entity_field_value_ctools_access_get_child($plugin, $parent, $entity_type, $bundle_type, $field_name, $entity, $bundle, $field);
          $plugins[$parent . ':' . $entity_type . ':' . $bundle_type . ':' . $field_name] = $plugin;
        }
      }
    }
  }

  return $plugins;
}

function _ctools_entity_field_value_ctools_access_get_child($plugin, $parent, $entity_type, $bundle_type, $field_name, $entity = NULL, $bundle = NULL, $field = NULL) {
  // Check that the entity, bundle and field arrays have a value.
  // If not, load theme using machine names.
  if (empty($entity)) {
    $entity = entity_get_info($entity_type);
  }

  if (empty($bundle)) {
    $bundle = $entity['bundles'][$bundle_type];
  }

  if (empty($field)) {
    $field_instances = field_info_instances($entity_type, $bundle_type);
    $field = $field_instances[$field_name];
  }

  $plugin['title'] = t('@entity @type: @field Field', array('@entity' => $entity['label'], '@type' => $bundle_type, '@field' => $field['label']));
  $plugin['keyword'] = $entity_type;
  $plugin['description'] = t('Control access by @entity entity bundle.', array('@entity' => $entity_type));
  $plugin['name'] = $parent . ':' . $entity_type . ':' . $bundle_type . ':' . $field_name;
  $plugin['required context'] = new ctools_context_required(t(ucfirst($entity_type)), $entity_type, array(
    'type' => $bundle_type,
  ));

  return $plugin;
}

/**
 * Settings form for the 'by entity_bundle' access plugin.
 */
function ctools_entity_field_value_ctools_access_settings($form, &$form_state, $conf) {
  $plugin = $form_state['plugin'];
  list($parent, $entity_type, $bundle_type, $field_name) = explode(':', $plugin['name']);
  $entity_info = entity_get_info($entity_type);
  $instances   = field_info_instances($entity_type, $bundle_type);
  $instance    = $instances[$field_name];
  $field       = field_info_field_by_id($instance['field_id']);
  foreach ($field['columns'] as $column => $attributes) {
    $columns[$column] = _field_sql_storage_columnname($field_name, $column);
  }
  ctools_include('fields');
  $entity = (object) array(
    $entity_info['entity keys']['bundle'] => $bundle_type,
  );

  foreach ($columns as $column => $sql_column) {
    if (isset($conf[$sql_column])) {
      if (is_array($conf[$sql_column])) {
        foreach ($conf[$sql_column] as $delta => $conf_value) {
          if (is_numeric($delta)) {
            if (is_array($conf_value)) {
              $entity->{$field_name}[LANGUAGE_NONE][$delta][$column] = $conf_value[$column];
            }
            else {
              $entity->{$field_name}[LANGUAGE_NONE][$delta][$column] = $conf_value;
            }
          }
        }
      }
      else {
        $entity->{$field_name}[LANGUAGE_NONE][0][$column] = $conf[$sql_column];
      }
    }
  }

  $form['#parents'] = array('settings');
  $langcode = field_valid_language(NULL);
  $form['settings'] += (array) ctools_field_invoke_field($instance, 'form', $entity_type, $entity, $form, $form_state, array('default' => TRUE, 'language' => $langcode));
  // Weight is really not important once this is populated and will only interfere with the form layout.
  foreach (element_children($form['settings']) as $element) {
    unset($form['settings'][$element]['#weight']);
  }

  return $form;
}

function ctools_entity_field_value_ctools_access_settings_submit($form, &$form_state) {
  $plugin = $form_state['plugin'];
  list($parent, $entity_type, $bundle_type, $field_name) = explode(':', $plugin['name']);
  $langcode  = field_valid_language(NULL);
  $langcode  = isset($form_state['input']['settings'][$field_name][$langcode]) ? $langcode : LANGUAGE_NONE;
  $instances = field_info_instances($entity_type, $bundle_type);
  $instance  = $instances[$field_name];
  $field     = field_info_field_by_id($instance['field_id']);
  foreach ($field['columns'] as $column => $attributes) {
    $columns[$column] = _field_sql_storage_columnname($field_name, $column);
  }
  $items = _ctools_entity_field_value_get_proper_form_items($field, $form_state['values']['settings'][$field_name][$langcode], array_keys($columns));
  foreach ($columns as $column => $sql_column) {
    $column_items = _ctools_entity_field_value_filter_items_by_column($items, $column);
    $form_state['values']['settings'][$sql_column] = $column_items;
  }
  $form_state['values']['settings'][$field_name][$langcode] = $items;
}

function _ctools_entity_field_value_get_proper_form_items($field, $form_items, $columns) {
  $items = array();

  // Single value item.
  if (!is_array($form_items)) {
    foreach ($columns as $column) {
      $items[0][$column] = $form_items;
    }
    return $items;
  }

  foreach ($form_items as $delta => $value) {
    $item = array();
    if (is_numeric($delta)) { // Array of field values.
      if (!is_array($value)) {  // Single value in array.
        foreach ($columns as $column) {
          $item[$column] = $value;
        }
      }
      else { // Value has colums.
        foreach ($columns as $column) {
          $item[$column] = isset($value[$column]) ? $value[$column] : '';
        }
      }
    }
    $items[] = $item;
  }

  // Check if $form_items is an array of columns.
  $item = array();
  $has_columns = FALSE;
  foreach ($columns as $column) {
    if (isset($form_items[$column])) {
      $has_columns = TRUE;
      $item[$column] = $form_items[$column];
    }
    else {
      $item[$column] = '';
    }
  }
  if ($has_columns) {
    $items[] = $item;
  }

  // Remove empty values.
  $items = _field_filter_items($field, $items);
  return $items;
}

function _ctools_entity_field_value_filter_items_by_column($items, $column) {
  $column_items = array();
  foreach ($items as $delta => $values) {
    $column_items[$delta] = isset($values[$column]) ? $values[$column] : '';
  }
  return $column_items;
}

/**
 * Check for access.
 */
function ctools_entity_field_value_ctools_access_check($conf, $context, $plugin) {
  if ((!is_object($context)) || (empty($context->data))) {
    // If the context doesn't exist -- for example, a newly added entity
    // reference is used as a pane visibility criteria -- we deny access.
    return FALSE;
  }

  list($parent, $entity_type, $bundle_type, $field_name) = explode(':', $plugin['name']);

  if ($field_items = field_get_items($entity_type, $context->data, $field_name)) {
    $langcode = field_language($entity_type, $context->data, $field_name);
    // Get field storage columns.
    $instance = field_info_instance($entity_type, $field_name, $bundle_type);
    $field = field_info_field_by_id($instance['field_id']);
    $columns = array();
    foreach ($field['columns'] as $column => $attributes) {
      $columns[$column] = _field_sql_storage_columnname($field_name, $column);
    }

    if (isset($conf[$field_name])) {
      // We have settings for this field.
      $conf_value_array = _ctools_entity_field_value_ctools_access_get_conf_field_values($conf[$field_name], $langcode);
      if (empty($conf_value_array)) {
        return FALSE;
      }

      // Check field value.
      foreach ($field_items as $field_value) {
        // Iterate through config values.
        foreach ($conf_value_array as $conf_value) {
          $match = FALSE;
          foreach ($field_value as $field_column => $value) {
            // Check access only for stored in config column values.
            if (isset($conf_value[$field_column])) {
              if ($value == $conf_value[$field_column]) {
                $match = TRUE;
              }
              else {
                $match = FALSE;
                break;
              }
            }
          }
          if ($match) {
            return TRUE;
          }
        }
      }
      return FALSE;
    }
  }

  return FALSE;
}

function _ctools_entity_field_value_ctools_access_get_conf_field_values($values, $langcode = LANGUAGE_NONE) {
  if (!is_array($values) || !isset($values[$langcode])) {
    return NULL;
  }
  $conf_values = array();

  foreach ($values[$langcode] as $delta => $value) {
    $conf_values[$delta] = $value;
  }

  return $conf_values;
}

/**
 * Provide a summary description based upon the checked entity_bundle.
 */
function ctools_entity_field_value_ctools_access_summary($conf, $context, $plugin) {
  list($parent, $entity_type, $bundle_type, $field_name) = explode(':', $plugin['name']);
  $instances   = field_info_instances($entity_type, $bundle_type);
  $instance    = $instances[$field_name];
  $field       = field_info_field_by_id($instance['field_id']);
  $entity_info = entity_get_info($entity_type);
  $entity      = (object)array(
    $entity_info['entity keys']['bundle'] => $bundle_type,
  );
  $keys   = array();
  $value_keys = array();
  $keyed_elements = array();
  foreach ($field['columns'] as $column => $attributes) {
    $conf_key = _field_sql_storage_columnname($field_name, $column);
    $keyed_elements["@{$column}_value"] = array();

    if (isset($conf[$conf_key])) {
      if (is_array($conf[$conf_key])) {
        $i = 0;
        foreach ($conf[$conf_key] as $conf_value) {
          if (!is_array($conf_value)) {
            $entity->{$field_name}[LANGUAGE_NONE][$i][$column] = $conf_value;
            $keyed_elements["@{$column}_value"][$i] = array('#markup' => $conf_value);
          }
          elseif (isset($conf_value[$column])) {
            $entity->{$field_name}[LANGUAGE_NONE][$i][$column] = $conf_value[$column];
            $keyed_elements["@{$column}_value"][$i] = array('#markup' => $conf_value[$column]);
          }
          $i++;
        }
      }
      else {
        $entity->{$field_name}[LANGUAGE_NONE][0][$column] = $conf[$conf_key];
        $keyed_elements["@{$column}_value"][0] = array('#markup' => $conf[$conf_key]);
      }
    }

    $keys['@' . $column] = $column;
    $value_keys[] = "@{$column}_value";
  }
  $elements = array();
  $items = isset($entity->{$field_name}[LANGUAGE_NONE]) ? $entity->{$field_name}[LANGUAGE_NONE] : array();
  $view_mode = 'full';
  ctools_include('fields');
  $display = field_get_display($instance, $view_mode, $entity);
  if (!isset($display['module'])) {
    $display['module'] = $field['module'];
  }
  if (isset($display['module'])) {
    // Choose simple formatter for well known cases.
    switch ($display['module']) {
      case 'text':
        $display['type'] = 'text_default';
        break;

      case 'list':
        $display['type'] = 'list_default';
        if ($field['type'] == 'list_boolean') {
          $allowed_values = list_allowed_values($field, $instance, $entity_type, $entity);
          foreach ($items as $item) {
            if (isset($allowed_values[$item['value']])) {
              if ($allowed_values[$item['value']] == '') {
                $display['type'] = 'list_key';
                break;
              }
            }
            else {
              $display['type'] = 'list_key';
            }
          }
        }
        break;

      case 'taxonomy':
        $display['type'] = 'taxonomy_term_reference_plain';
        break;

      case 'entityreference':
        $display['type'] = 'entityreference_label';
        break;

      default:
        // Use field instance formatter setting.
        break;
    }

    $function = $display['module'] . '_field_formatter_view';
    if (function_exists($function)) {
      $entity_group = array(0 => $entity);
      $item_group = array(0 => $items);
      $instance_group = array(0 => $instance);
      field_default_prepare_view($entity_type, $entity_group, $field, $instance_group, LANGUAGE_NONE, $item_group, $display);
      $elements = $function($entity_type, $entity, $field, $instance, LANGUAGE_NONE, $item_group[0], $display);
    }
  }
  if (count($elements) > 0) {
    foreach ($field['columns'] as $column => $attributes) {
      if (count($field['columns']) == 1) {
        $keyed_elements["@{$column}_value"] = $elements;
      }
    }
  }
  $values = array();
  foreach ($value_keys as $key) {
    $output = array();
    $elements = $keyed_elements[$key];
    if (is_array($elements)) {
      foreach ($elements as $element_key => $element) {
        if (is_numeric($element_key)) {
          $value_str = strip_tags(drupal_render($element));
          if (strlen($value_str) > 0) {
            $output[] = $value_str;
          }
        }
      }
    }
    else {
      $value_str = strip_tags(drupal_render($elements));
      if (strlen($value_str) > 0) {
        $output[] = $value_str;
      }
    }
    $value = implode(', ', $output);
    if ($value !== '') {
      $values[$key] = implode(', ', $output);
    }
  }
  $string = '';
  $value_count = count($values);
  foreach ($keys as $key_name => $column) {
    if (isset($values[$key_name . '_value'])) {
      $string .= ($value_count > 1) ? " @{$column} = @{$column}_value" : "@{$column}_value";
    }
  }
  return t('@field is set to "!value"', array('@field' => $instance['label'], '!value' => format_string($string, array_merge($keys, $values))));
}
