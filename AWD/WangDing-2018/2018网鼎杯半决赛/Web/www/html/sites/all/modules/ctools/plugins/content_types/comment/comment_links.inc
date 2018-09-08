<?php

/**
 * @file
 * Plugins are described by creating a $plugin array which will be used
 * by the system that includes this file.
 */

$plugin = array(
  'single' => TRUE,
  'title' => t('Comment links'),
  'icon' => 'icon_comment.png',
  'description' => t('Comment links of the referenced comment.'),
  'required context' => new ctools_context_required(t('Comment'), 'entity:comment'),
  'category' => t('Comment'),
  'defaults' => array(
    'override_title' => FALSE,
    'override_title_text' => '',
    'build_mode' => '',
  ),
);

/**
 * Output function for the comment links.
 */
function ctools_comment_links_content_type_render($subtype, $conf, $panel_args, $context) {
  if (!empty($context) && empty($context->data)) {
    return;
  }

  $comment = isset($context->data) ? clone $context->data : NULL;
  $block = new stdClass();
  $block->module = 'comment';
  $block->delta = $comment->cid;

  if (empty($comment)) {
    $block->delta   = 'placeholder';
    $block->subject = t('Comment subject.');
    $block->content = t('Comment links go here.');
  }
  else {
    $node = node_load($comment->nid);
    $block->subject = $comment->subject;
    comment_build_content($comment, $node, $conf['build_mode']);
    $block->content = $comment->content['links'];
  }
  return $block;
}

/**
 * Returns an edit form for the custom type.
 */
function ctools_comment_links_content_type_edit_form($form, &$form_state) {
  $conf = $form_state['conf'];

  $entity = entity_get_info('comment');
  $build_mode_options = array();
  foreach ($entity['view modes'] as $mode => $option) {
    $build_mode_options[$mode] = $option['label'];
  }

  $form['build_mode'] = array(
    '#title' => t('Build mode'),
    '#type' => 'select',
    '#description' => t('Select a build mode for this comment.'),
    '#options' => $build_mode_options,
    '#default_value' => $conf['build_mode'],
  );

  return $form;
}

function ctools_comment_links_content_type_edit_form_submit($form, &$form_state) {
  // Copy everything from our defaults.
  foreach (array_keys($form_state['plugin']['defaults']) as $key) {
    $form_state['conf'][$key] = $form_state['values'][$key];
  }
}

function ctools_comment_links_content_type_admin_title($subtype, $conf, $context) {
  return t('"@s" links', array('@s' => $context->identifier));
}
