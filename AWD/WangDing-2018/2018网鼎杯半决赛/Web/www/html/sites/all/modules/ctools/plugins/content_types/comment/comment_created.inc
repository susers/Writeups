<?php

/**
 * @file
 * Plugins are described by creating a $plugin array which will be used
 * by the system that includes this file.
 */

$plugin = array(
  'single' => TRUE,
  'title' => t('Comment created date'),
  'icon' => 'icon_comment.png',
  'description' => t('The date the referenced comment was created.'),
  'required context' => new ctools_context_required(t('Comment'), 'entity:comment'),
  'category' => t('Comment'),
  'defaults' => array(
    'format' => 'small',
  ),
);

/**
 * Render the custom content type.
 */
function ctools_comment_created_content_type_render($subtype, $conf, $panel_args, $context) {
  if (empty($context) || empty($context->data)) {
    return;
  }

  // Get a shortcut to the comment.
  $comment = $context->data;

  // Build the content type block.
  $block          = new stdClass();
  $block->module  = 'comment_created';
  $block->title   = t('Created date');
  $block->content = format_date($comment->created, $conf['format']);
  $block->delta   = $comment->cid;

  return $block;
}

/**
 * Returns an edit form for custom type settings.
 */
function ctools_comment_created_content_type_edit_form($form, &$form_state) {
  $conf = $form_state['conf'];
  $date_types = array();

  foreach (system_get_date_types() as $date_type => $definition) {
    $date_types[$date_type] = format_date(REQUEST_TIME, $date_type);
  }
  $form['format'] = array(
    '#title' => t('Date format'),
    '#type' => 'select',
    '#options' => $date_types,
    '#default_value' => $conf['format'],
  );
  return $form;
}

/**
 * Submit handler for the custom type settings form.
 */
function ctools_comment_created_content_type_edit_form_submit($form, &$form_state) {
  // Copy everything from our defaults.
  foreach (array_keys($form_state['plugin']['defaults']) as $key) {
    $form_state['conf'][$key] = $form_state['values'][$key];
  }
}

/**
 * Returns the administrative title for a type.
 */
function ctools_comment_created_content_type_admin_title($subtype, $conf, $context) {
  return t('"@s" created date', array('@s' => $context->identifier));
}
