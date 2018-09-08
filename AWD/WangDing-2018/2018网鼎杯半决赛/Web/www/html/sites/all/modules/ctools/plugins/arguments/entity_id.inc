<?php

/**
 * @file
 * Plugin to provide an argument handler for all entity ids.
 */

/**
 * Plugins are described by creating a $plugin array which will be used
 * by the system that includes this file.
 */
$plugin = array(
  'title' => t("Entity: ID"),
  'description' => t('Creates an entity context from an entity ID argument.'),
  'context' => 'ctools_argument_entity_id_context',
  'get child' => 'ctools_argument_entity_id_get_child',
  'get children' => 'ctools_argument_entity_id_get_children',
  'default' => array(
    'entity_id' => '',
  ),
  'placeholder form' => 'ctools_argument_entity_id_ctools_argument_placeholder',
);

function ctools_argument_entity_id_get_child($plugin, $parent, $child) {
  $plugins = ctools_argument_entity_id_get_children($plugin, $parent);
  return $plugins[$parent . ':' . $child];
}

function ctools_argument_entity_id_get_children($original_plugin, $parent) {
  $entities = entity_get_info();
  $plugins = array();
  foreach ($entities as $entity_type => $entity) {
    $plugin = $original_plugin;
    $plugin['title'] = t('@entity: ID', array('@entity' => $entity['label']));
    $plugin['keyword'] = $entity_type;
    $plugin['description'] = t('Creates @entity context from an ID argument.', array('@entity' => $entity_type));
    $plugin['name'] = $parent . ':' . $entity_type;
    $plugin_id = $parent . ':' . $entity_type;
    drupal_alter('ctools_entity_context', $plugin, $entity, $plugin_id);
    $plugins[$plugin_id] = $plugin;
  }
  drupal_alter('ctools_entity_contexts', $plugins);

  return $plugins;
}

/**
 * Discover if this argument gives us the entity we crave.
 */
function ctools_argument_entity_id_context($arg = NULL, $conf = NULL, $empty = FALSE) {
  $entity_type = explode(':', $conf['name']);
  $entity_type = $entity_type[1];
  // If unset it wants a generic, unfilled context.
  if ($empty) {
    return ctools_context_create_empty('entity:' . $entity_type);
  }

  // We can accept either an entity object or a pure id.
  if (is_object($arg)) {
    return ctools_context_create('entity:' . $entity_type, $arg);
  }

  // Trim spaces and other garbage.
  $arg = trim($arg);

  if (!is_numeric($arg)) {
    $preg_matches = array();
    $match = preg_match('/\[id: (\d+)\]/', $arg, $preg_matches);
    if (!$match) {
      $match = preg_match('/^id: (\d+)/', $arg, $preg_matches);
    }

    if ($match) {
      $id = $preg_matches[1];
    }
    if (isset($id) && is_numeric($id)) {
      return ctools_context_create('entity:' . $entity_type, $id);
    }
    return FALSE;
  }

  $entities = entity_load($entity_type, array($arg));
  if (empty($entities)) {
    return FALSE;
  }

  return ctools_context_create('entity:' . $entity_type, reset($entities));
}

function ctools_argument_entity_id_settings_form(&$form, &$form_state, $conf) {
  $plugin = &$form_state['plugin'];

  $form['settings']['entity'] = array(
    '#title' => t('Enter the title or ID of a @entity entity', array('@entity' => $plugin['keyword'])),
    '#type' => 'textfield',
    '#maxlength' => 512,
    '#autocomplete_path' => 'ctools/autocomplete/' . $plugin['keyword'],
    '#weight' => -10,
  );

  if (!empty($conf['entity_id'])) {
    $info = entity_load($plugin['keyword'], array($conf['entity_id']));
    $info = $info[$conf['entity_id']];
    if ($info) {
      $entity = entity_get_info($plugin['keyword']);
      $uri = entity_uri($plugin['keyword'], $info);
      if (is_array($uri) && $entity['entity keys']['label']) {
        $link = l(t("'%title' [%type id %id]", array('%title' => $info->{$entity['entity keys']['label']}, '%type' => $plugin['keyword'], '%id' => $conf['entity_id'])), $uri['path'], array('attributes' => array('target' => '_blank', 'title' => t('Open in new window')), 'html' => TRUE));
      }
      elseif (is_array($uri)) {
        $link = l(t("[%type id %id]", array('%type' => $plugin['keyword'], '%id' => $conf['entity_id'])), $uri['path'], array('attributes' => array('target' => '_blank', 'title' => t('Open in new window')), 'html' => TRUE));
      }
      elseif ($entity['entity keys']['label']) {
        $link = l(t("'%title' [%type id %id]", array('%title' => $info->{$entity['entity keys']['label']}, '%type' => $plugin['keyword'], '%id' => $conf['entity_id'])), file_create_url($uri), array('attributes' => array('target' => '_blank', 'title' => t('Open in new window')), 'html' => TRUE));
      }
      else {
        $link = t("[%type id %id]", array('%type' => $plugin['keyword'], '%id' => $conf['entity_id']));
      }
      $form['settings']['entity']['#description'] = t('Currently set to !link', array('!link' => $link));
    }
  }

  $form['settings']['entity_id'] = array(
    '#type' => 'value',
    '#value' => isset($conf['entity_id']) ? $conf['entity_id'] : '',
  );

  $form['settings']['entity_type'] = array(
    '#type' => 'value',
    '#value' => $plugin['keyword'],
  );

  return $form;
}

function ctools_argument_entity_id_ctools_argument_placeholder($conf) {
  $conf = array(
    '#title' => t('Enter the title or ID of a @entity entity', array('@entity' => $conf['keyword'])),
    '#type' => 'textfield',
    '#maxlength' => 512,
    '#autocomplete_path' => 'ctools/autocomplete/' . $conf['keyword'],
    '#weight' => -10,
  );

  return $conf;
}
