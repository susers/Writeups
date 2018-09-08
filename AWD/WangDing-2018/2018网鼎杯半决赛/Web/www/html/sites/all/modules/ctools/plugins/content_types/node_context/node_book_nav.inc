<?php

/**
 * @file
 */

if (module_exists('book')) {
  /**
   * Plugins are described by creating a $plugin array which will be used
   * by the system that includes this file.
   */
  $plugin = array(
    'single' => TRUE,
    'title' => t('Book navigation pager'),
    'icon' => drupal_get_path('module', 'ctools') . '/plugins/content_types/block/icon_core_booknavigation.png',
    'description' => t('The navigational pager and sub pages of the current book node.'),
    'required context' => new ctools_context_required(t('Node'), 'node'),
    'category' => t('Node'),
  );
}

function ctools_node_book_nav_content_type_render($subtype, $conf, $panel_args, $context) {
  $node = isset($context->data) ? clone $context->data : NULL;
  $block = new stdClass();
  $block->module = 'book_nav';

  $block->title = t('Book navigation pager');
  if ($node) {
    $block->content = isset($node->book) ? theme('book_navigation', array('book_link' => $node->book)) : '';
    $block->delta = $node->nid;
  }
  else {
    $block->content = t('Book navigation pager goes here.');
    $block->delta = 'unknown';
  }

  return $block;
}

function ctools_node_book_nav_content_type_admin_title($subtype, $conf, $context) {
  return t('"@s" book navigation pager', array('@s' => $context->identifier));
}

function ctools_node_book_nav_content_type_edit_form($form, &$form_state) {
  // Provide a blank form so we have a place to have context setting.
  return $form;
}
