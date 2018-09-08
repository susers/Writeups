<?php

/**
 * @file
 * CTools content-type plugin to provide a comment-reply form (replying either
 * to a node or to another comment).
 */

// Only provide the plugin in the comment module is enabled.
if (module_exists('comment')) {
  $plugin = array(
    'single' => TRUE,
    'title' => t('Comment Reply Form'),
    'icon' => 'icon_comment.png',
    'description' => t('A form to add a new comment reply.'),
    'required context' => array(
      new ctools_context_required(t('Node'), 'node'),
      new ctools_context_optional(t('Comment'), 'comment'),
    ),
    'category' => t('Comment'),
    'render callback'  => 'ctools_comment_reply_form_content_type_render',
    'defaults' => array('anon_links' => FALSE),
  );
}

function ctools_comment_reply_form_content_type_render($subtype, $conf, $panel_args, $context) {

  $comment = ($context[1]->identifier == t('No context')) ? NULL : clone $context[1]->data;
  $block = new stdClass();
  $block->module = 'comments';
  if ($comment) {
    $block->delta = $comment->cid;
  }
  $block->title = t('Add comment');
  $node = $context[0]->data;

  module_load_include('inc', 'comment', 'comment.pages');
  $block->content = comment_reply($node, ($comment ? $comment->cid : NULL));

  return $block;
}

function ctools_comment_reply_form_content_type_admin_title($subtype, $conf, $context) {
  return t('"@s" comment form', array('@s' => $context[0]->identifier));
}

function ctools_comment_reply_form_content_type_edit_form($form, &$form_state) {
  return $form;
}

function ctools_comment_reply_form_content_type_edit_form_submit($form, &$form_state) {
}
