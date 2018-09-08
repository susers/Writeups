<?php

/**
 * @file
 * Plugin to provide access control based upon term vocabulary.
 */

/**
 * Plugins are described by creating a $plugin array which will be used
 * by the system that includes this file.
 */
$plugin = array(
  'title' => t("Taxonomy: vocabulary"),
  'description' => t('Control access by vocabulary.'),
  'callback' => 'ctools_term_vocabulary_ctools_access_check',
  'default' => array('vids' => array()),
  'settings form' => 'ctools_term_vocabulary_ctools_access_settings',
  'settings form submit' => 'ctools_term_vocabulary_ctools_access_settings_submit',
  'summary' => 'ctools_term_vocabulary_ctools_access_summary',
  'required context' => new ctools_context_required(t('Vocabulary'), array(
    'taxonomy_term',
    'terms',
    'taxonomy_vocabulary',
  )),
);

/**
 * Settings form for the 'by term_vocabulary' access plugin.
 */
function ctools_term_vocabulary_ctools_access_settings($form, &$form_state, $conf) {
  $options = array();
  $vocabularies = taxonomy_get_vocabularies();
  foreach ($vocabularies as $voc) {
    $options[$voc->machine_name] = check_plain($voc->name);
  }

  _ctools_term_vocabulary_ctools_access_map_vids($conf);

  $form['settings']['machine_name'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Vocabularies'),
    '#options' => $options,
    '#description' => t('Only the checked vocabularies will be valid.'),
    '#default_value' => $conf['machine_name'],
  );
  return $form;
}

/**
 * Compress the term_vocabularys allowed to the minimum.
 */
function ctools_term_vocabulary_ctools_access_settings_submit($form, &$form_state) {
  $form_state['values']['settings']['machine_name'] = array_filter($form_state['values']['settings']['machine_name']);
}

/**
 * Check for access.
 */
function ctools_term_vocabulary_ctools_access_check($conf, $context) {
  // As far as I know there should always be a context at this point, but this
  // is safe.
  if (empty($context) || empty($context->data) || empty($context->data->vocabulary_machine_name)) {
    return FALSE;
  }

  _ctools_term_vocabulary_ctools_access_map_vids($conf);

  if (array_filter($conf['machine_name']) && empty($conf['machine_name'][$context->data->vocabulary_machine_name])) {
    return FALSE;
  }

  return TRUE;
}

/**
 * Provide a summary description based upon the checked term_vocabularys.
 */
function ctools_term_vocabulary_ctools_access_summary($conf, $context) {
  if (!isset($conf['type'])) {
    $conf['type'] = array();
  }
  $vocabularies = taxonomy_get_vocabularies();

  _ctools_term_vocabulary_ctools_access_map_vids($conf);

  $names = array();
  if (!empty($conf['machine_name'])) {
    foreach (array_filter($conf['machine_name']) as $machine_name) {
      foreach ($vocabularies as $vocabulary) {
        if ($vocabulary->machine_name === $machine_name) {
          $names[] = check_plain($vocabulary->name);
          continue;
        }
      }
    }
  }

  if (empty($names)) {
    return t('@identifier is any vocabulary', array('@identifier' => $context->identifier));
  }

  return format_plural(count($names), '@identifier vocabulary is "@machine_names"', '@identifier vocabulary is one of "@machine_names"', array(
    '@machine_names' => implode(', ', $names),
    '@identifier' => $context->identifier,
  ));
}

/**
 * Helper function to map the vids from old features to the new machine_name.
 *
 * Add the machine_name key to $conf if the vids key exist.
 *
 * @param array $conf
 *   The configuration of this plugin.
 */
function _ctools_term_vocabulary_ctools_access_map_vids(&$conf) {
  if (!empty($conf['vids'])) {
    $conf['machine_name'] = array();
    $vocabularies = taxonomy_get_vocabularies();
    foreach ($conf['vids'] as $vid) {
      $machine_name = $vocabularies[$vid]->machine_name;
      $conf['machine_name'][$machine_name] = $vocabularies[$vid]->machine_name;
    }
  }
}
