<?php

/**
 * @file
 * Plugin to provide access control based upon a parent term.
 */

/**
 * Plugins are described by creating a $plugin array which will be used
 * by the system that includes this file.
 */
$plugin = array(
  'title' => t("Taxonomy: term depth"),
  'description' => t('Control access by the depth of a term.'),
  'callback' => 'term_depth_term_depth_ctools_access_check',
  'default' => array('vid' => array(), 'depth' => 0),
  'settings form' => 'term_depth_term_depth_ctools_access_settings',
  'settings form validation' => 'term_depth_term_depth_ctools_access_settings_validate',
  'settings form submit' => 'term_depth_term_depth_ctools_access_settings_submit',
  'summary' => 'term_depth_term_depth_ctools_access_summary',
  'required context' => new ctools_context_required(t('Term'), array('taxonomy_term', 'terms')),
);

/**
 * Settings form for the 'term depth' access plugin.
 */
function term_depth_term_depth_ctools_access_settings($form, &$form_state, $conf) {
  $vocabularies = taxonomy_get_vocabularies();
  $options = array();
  // Loop over each of the configured vocabularies.
  foreach ($vocabularies as $vid => $vocab) {
    $options[$vocab->machine_name] = $vocab->name;
  }

  _term_depth_convert_config_vid_to_vocabulary_name($conf);

  $form['settings']['vocabulary'] = array(
    '#title' => t('Vocabulary'),
    '#type' => 'select',
    '#options' => $options,
    '#description' => t('Select the vocabulary for this form. If there exists a parent term in that vocabulary, this access check will succeed.'),
    '#id' => 'ctools-select-vocabulary',
    '#default_value' => !empty($conf['vocabulary']) ? $conf['vocabulary'] : array(),
    '#required' => TRUE,
  );

  $form['settings']['depth'] = array(
    '#title' => t('Depth'),
    '#type' => 'textfield',
    '#description' => t('Set the required depth of the term. If the term exists at the correct depth, this access check will succeed.'),
    '#default_value' => $conf['depth'],
    '#required' => TRUE,
  );

  return $form;
}

/**
 * @param $conf
 */
function _term_depth_convert_config_vid_to_vocabulary_name(&$conf) {
  // Fallback on legacy 'vid', when no vocabularies are available.
  if (empty($conf['vocabulary']) && !empty($conf['vid'])) {
    $vocabulary = _ctools_term_vocabulary_machine_name_convert(array($conf['vid']));
    $conf['vocabulary'] = reset($vocabulary);
    unset($conf['vid'], $vocabulary);
  }
}

/**
 * Submit function for the access plugins settings.
 */
function term_depth_term_depth_ctools_access_settings_submit($form, $form_state) {
  $form_state['conf']['depth'] = (integer) $form_state['values']['settings']['depth'];
  $form_state['conf']['vocabulary'] = array_filter($form_state['conf']['vocabulary']);
}

/**
 * Check for access.
 */
function term_depth_term_depth_ctools_access_check($conf, $context) {
  // As far as I know there should always be a context at this point, but this
  // is safe.
  if (empty($context) || empty($context->data) || empty($context->data->vid) || empty($context->data->tid)) {
    return FALSE;
  }

  _term_depth_convert_config_vid_to_vocabulary_name($conf);

  // Get the $vocabulary.
  if (!isset($conf['vocabulary'])) {
    return FALSE;
  }
  $vocab = taxonomy_vocabulary_machine_name_load($conf['vocabulary']);
  if ($vocab->vid != $context->data->vid) {
    return FALSE;
  }

  $depth = _term_depth($context->data->tid);

  return ($depth == $conf['depth']);
}

/**
 * Provide a summary description based upon the checked terms.
 */
function term_depth_term_depth_ctools_access_summary($conf, $context) {
  _term_depth_convert_config_vid_to_vocabulary_name($conf);
  $vocab = taxonomy_vocabulary_machine_name_load($conf['vocabulary']);
  return t('"@term" is in vocabulary "@vocab" at depth @depth', array(
    '@term' => $context->identifier,
    '@vocab' => $vocab->name,
    '@depth' => $conf['depth'],
  ));
}

/**
 * Find the depth of a term.
 */
function _term_depth($tid) {
  static $depths = array();

  if (!isset($depths[$tid])) {
    $parent = db_select('taxonomy_term_hierarchy', 'th')
      ->fields('th', array('parent'))
      ->condition('tid', $tid)
      ->execute()->fetchField();

    if ($parent == 0) {
      $depths[$tid] = 1;
    }
    else {
      $depths[$tid] = 1 + _term_depth($parent);
    }
  }

  return $depths[$tid];
}
