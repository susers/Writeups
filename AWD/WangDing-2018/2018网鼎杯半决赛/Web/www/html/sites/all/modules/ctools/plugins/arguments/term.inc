<?php

/**
 * @file
 * Plugin to provide an argument handler for a Taxonomy term.
 */

/**
 * Plugins are described by creating a $plugin array which will be used
 * by the system that includes this file.
 */
$plugin = array(
  'title' => t("Taxonomy term: ID"),
  // Keyword to use for %substitution.
  'keyword' => 'term',
  'description' => t('Creates a single taxonomy term from a taxonomy ID or taxonomy term name.'),
  'context' => 'ctools_term_context',
  'default' => array('input_form' => 'tid', 'breadcrumb' => TRUE, 'transform' => FALSE),
  'settings form' => 'ctools_term_settings_form',
  'settings form validate' => 'ctools_term_settings_form_validate',
  'placeholder form' => 'ctools_term_ctools_argument_placeholder',
  'breadcrumb' => 'ctools_term_breadcrumb',
);

/**
 * Discover if this argument gives us the term we crave.
 */
function ctools_term_context($arg = NULL, $conf = NULL, $empty = FALSE) {
  // If unset it wants a generic, unfilled context.
  if ($empty) {
    return ctools_context_create_empty('entity:taxonomy_term');
  }

  if (isset($conf['vocabularies'])) {
    $vocabularies = $conf['vocabularies'];
  }
  else {
    $vids = isset($conf['vids']) ? $conf['vids'] : array();

    // Convert legacy use of vids to machine names.
    $vocabularies = _ctools_term_vocabulary_machine_name_convert($vids);
  }

  if (is_object($arg)) {
    $term = $arg;
  }
  else {
    switch ($conf['input_form']) {
      case 'tid':
      default:
        if (!is_numeric($arg)) {
          return FALSE;
        }
        $term = taxonomy_term_load($arg);
        break;

      case 'term':
        if (!empty($conf['transform'])) {
          $arg = strtr($arg, '-', ' ');
        }

        $terms = taxonomy_get_term_by_name($arg);
        // If only one term is found, fall through to vocabulary check below.
        if ((count($terms) > 1) && $vocabularies) {
          foreach ($terms as $potential) {
            foreach ($vocabularies as $machine_name) {
              if ($potential->vocabulary_machine_name == $machine_name) {
                $term = $potential;
                // Break out of the foreaches AND the case.
                break 3;
              }
            }
          }
        }
        $term = array_shift($terms);
        break;
    }

    if (empty($term)) {
      return NULL;
    }
  }

  if ($vocabularies && !isset($vocabularies[$term->vocabulary_machine_name])) {
    return NULL;
  }

  $context = ctools_context_create('entity:taxonomy_term', $term);
  $context->original_argument = $arg;
  return $context;
}

/**
 * Settings form for the argument.
 */
function ctools_term_settings_form(&$form, &$form_state, $conf) {
  // @todo allow synonym use like Views does.
  $form['settings']['input_form'] = array(
    '#title' => t('Argument type'),
    '#type' => 'radios',
    '#options' => array('tid' => t('Term ID'), 'term' => t('Term name')),
    '#default_value' => $conf['input_form'],
    '#prefix' => '<div class="clearfix">',
    '#suffix' => '</div>',
  );

  $vocabularies = taxonomy_get_vocabularies();
  $options = array();
  foreach ($vocabularies as $vid => $vocab) {
    $options[$vocab->machine_name] = $vocab->name;
  }

  // Fallback on legacy 'vids', when no vocabularies are available.
  if (empty($conf['vocabularies']) && !empty($conf['vids'])) {
    $conf['vocabularies'] = _ctools_term_vocabulary_machine_name_convert(array_filter($conf['vids']));
    unset($conf['vids']);
  }

  $form['settings']['vocabularies'] = array(
    '#title' => t('Limit to these vocabularies'),
    '#type' => 'checkboxes',
    '#options' => $options,
    '#default_value' => !empty($conf['vocabularies']) ? $conf['vocabularies'] : array(),
    '#description' => t('If no vocabularies are checked, terms from all vocabularies will be accepted.'),
  );

  $form['settings']['breadcrumb'] = array(
    '#title' => t('Inject hierarchy into breadcrumb trail'),
    '#type' => 'checkbox',
    '#default_value' => !empty($conf['breadcrumb']),
    '#description' => t('If checked, taxonomy term parents will appear in the breadcrumb trail.'),
  );

  $form['settings']['transform'] = array(
    '#title' => t('Transform dashes in URL to spaces in term name filter values'),
    '#type' => 'checkbox',
    '#default_value' => !empty($conf['transform']),
  );
}

function ctools_term_settings_form_validate(&$form, &$form_state) {
  // Filter the selected vocabularies to avoid storing redundant data.
  $vocabularies = array_filter($form_state['values']['settings']['vocabularies']);
  form_set_value($form['settings']['vocabularies'], $vocabularies, $form_state);
}

/**
 * Form fragment to get an argument to convert a placeholder for preview.
 */
function ctools_term_ctools_argument_placeholder($conf) {
  switch ($conf['input_form']) {
    case 'tid':
    default:
      return array(
        '#type' => 'textfield',
        '#description' => t('Enter a taxonomy term ID.'),
      );

    case 'term':
      return array(
        '#type' => 'textfield',
        '#description' => t('Enter a taxonomy term name.'),
      );
  }
}

/**
 * Inject the breadcrumb trail if necessary.
 */
function ctools_term_breadcrumb($conf, $context) {
  if (empty($conf['breadcrumb']) || empty($context->data) || empty($context->data->tid)) {
    return;
  }

  $breadcrumb = array();
  $current = new stdClass();
  $current->tid = $context->data->tid;
  while ($parents = taxonomy_get_parents($current->tid)) {
    $current = array_shift($parents);
    $breadcrumb[] = l($current->name, 'taxonomy/term/' . $current->tid);
  }

  $breadcrumb = array_merge(drupal_get_breadcrumb(), array_reverse($breadcrumb));
  drupal_set_breadcrumb($breadcrumb);
}

/**
 * Helper function to convert convert legacy vocabulary ids into machine names.
 *
 * @param array $vids
 *   Array of either vids.
 *
 * @return array
 *   A keyed array of machine names.
 */
function _ctools_term_vocabulary_machine_name_convert($vids) {
  $vocabularies = taxonomy_vocabulary_load_multiple($vids);
  $return = array();
  foreach ($vocabularies as $vocabulary) {
    $return[$vocabulary->machine_name] = $vocabulary->machine_name;
  }
  return $return;
}
