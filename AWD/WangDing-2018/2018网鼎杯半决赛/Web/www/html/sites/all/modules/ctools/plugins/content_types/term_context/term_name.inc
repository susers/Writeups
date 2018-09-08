<?php

/**
 * @file
 * Plugins are described by creating a $plugin array which will be used
 * by the system that includes this file.
 */

$plugin = array(
  'single' => TRUE,
  'title' => t('Term name'),
  'icon' => 'icon_term.png',
  'description' => t('The name of this taxonomy term.'),
  'required context' => new ctools_context_required(t('Term'), array('term', 'taxonomy_term')),
  'category' => t('Taxonomy term'),
  'defaults' => array(
    'link' => TRUE,
    'markup' => 'none',
    'id' => '',
    'class' => '',
  ),
);

/**
 * Render the custom content type.
 */
function ctools_term_name_content_type_render($subtype, $conf, $panel_args, $context) {
  if (empty($context) || empty($context->data)) {
    return;
  }

  // Get a shortcut to the term.
  $term = $context->data;

  // Load the vocabulary.
  $vocab = taxonomy_vocabulary_load($term->vid);

  // Generate the title.
  $content = !empty($conf['link']) ? l($term->name, 'taxonomy/term/' . $term->tid) : check_plain($term->name);

  // Build any surrounding markup if so configured.
  if (isset($conf['markup']) && $conf['markup'] != 'none') {
    $markup = '<' . $conf['markup'];
    if (!empty($conf['id'])) {
      $markup .= ' id="' . $conf['id'] . '"';
    }
    if (!empty($conf['class'])) {
      $markup .= ' class="' . $conf['class'] . '"';
    }
    $markup .= '>' . $content . '</' . $conf['markup'] . '>' . "\n";
    $content = $markup;
  }

  // Build the content type block.
  $block          = new stdClass();
  $block->module  = 'term_name';
  $block->title   = t('Name');
  $block->content = $content;
  $block->delta   = $term->tid;

  return $block;
}

/**
 * Returns an edit form for custom type settings.
 */
function ctools_term_name_content_type_edit_form($form, &$form_state) {
  $conf = $form_state['conf'];

  $form['markup'] = array(
    '#title' => t('Title tag'),
    '#type' => 'select',
    '#options' => array(
      'none' => t('- No tag -'),
      'h1' => t('h1'),
      'h2' => t('h2'),
      'h3' => t('h3'),
      'h4' => t('h4'),
      'h5' => t('h5'),
      'h6' => t('h6'),
      'div' => t('div'),
    ),
    '#default_value' => $conf['markup'],
  );

  $form['id'] = array(
    '#title' => t('CSS id to use'),
    '#type' => 'textfield',
    '#default_value' => $conf['id'],
  );

  $form['class'] = array(
    '#title' => t('CSS class to use'),
    '#type' => 'textfield',
    '#default_value' => $conf['class'],
  );

  $form['link'] = array(
    '#title' => t('Link to term'),
    '#type' => 'checkbox',
    '#default_value' => $conf['link'],
    '#description' => t('Check here to make the name link to the term page.'),
  );
  return $form;
}

/**
 * Submit handler for the custom type settings form.
 */
function ctools_term_name_content_type_edit_form_submit($form, &$form_state) {
  // Copy everything from our defaults.
  foreach (array_keys($form_state['plugin']['defaults']) as $key) {
    $form_state['conf'][$key] = $form_state['values'][$key];
  }
}

/**
 * Returns the administrative title for a type.
 */
function ctools_term_name_content_type_admin_title($subtype, $conf, $context) {
  return t('"@s" name', array('@s' => $context->identifier));
}
