<?php

/**
 * @file
 * Plugins are described by creating a $plugin array which will be used
 * by the system that includes this file.
 */

$plugin = array(
  'single' => TRUE,
  'title' => t('Term description'),
  'icon' => 'icon_term.png',
  'description' => t('Term description.'),
  'required context' => new ctools_context_required(t('Term'), array('term', 'taxonomy_term')),
  'category' => t('Taxonomy term'),
);

function ctools_term_description_content_type_render($subtype, $conf, $panel_args, $context) {
  $term = isset($context->data) ? clone $context->data : NULL;
  $block = new stdClass();
  $block->module = 'node_type';

  if (!empty($term)) {
    $block->title = $term->name;
    $block->content = check_markup($term->description, $term->format, '', TRUE);
    $block->delta = $term->tid;

    if (user_access('administer taxonomy')) {
      $block->admin_links['update'] = array(
        'title' => t('Edit term'),
        'alt' => t("Edit this term"),
        'href' => "taxonomy/term/$term->tid/edit",
        'query' => drupal_get_destination(),
      );
    }
  }
  else {
    $block->title = '';
    $block->content = t('Term description goes here.');
    $block->delta = 'unknown';
  }

  return $block;
}

function ctools_term_description_content_type_admin_title($subtype, $conf, $context) {
  return t('"@s" term description', array('@s' => $context->identifier));
}

function ctools_term_description_content_type_edit_form($form, &$form_state) {
  // Provide a blank form so we have a place to have context setting.
  return $form;
}
