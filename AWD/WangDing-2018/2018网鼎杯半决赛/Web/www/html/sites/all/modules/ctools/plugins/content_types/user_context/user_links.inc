<?php

/**
 * @file
 * Plugins are described by creating a $plugin array which will be used
 * by the system that includes this file.
 */

$plugin = array(
  'single' => TRUE,
  'title' => t('User links'),
  'icon' => 'icon_user.png',
  'description' => t('User links of the referenced user.'),
  'required context' => new ctools_context_required(t('User'), 'user'),
  'category' => t('User'),
  'defaults' => array(
    'override_title' => FALSE,
    'override_title_text' => '',
    'build_mode' => '',
  ),
);

/**
 * Output function for the user links.
 */
function ctools_user_links_content_type_render($subtype, $conf, $panel_args, $context) {
  if (!empty($context) && empty($context->data)) {
    return;
  }

  $account = clone $context->data;
  $block = new stdClass();
  $block->module = 'user';
  $block->delta = $account->uid;

  if (empty($account)) {
    $block->delta   = 'placeholder';
    $block->subject = t('User name.');
    $block->content = t('User links go here.');
  }
  else {
    $block->subject = $account->name;
    user_build_content($account, $conf['build_mode']);
    if (!empty($account->content['links'])) {
      $block->content = $account->content['links'];
    }
    else {
      $block->content = '';
    }
  }
  return $block;
}

/**
 * Returns an edit form for the custom type.
 */
function ctools_user_links_content_type_edit_form($form, &$form_state) {
  $conf = $form_state['conf'];

  $entity = entity_get_info('user');
  $build_mode_options = array();
  foreach ($entity['view modes'] as $mode => $option) {
    $build_mode_options[$mode] = $option['label'];
  }

  $form['build_mode'] = array(
    '#title' => t('Build mode'),
    '#type' => 'select',
    '#description' => t('Select a build mode for this user.'),
    '#options' => $build_mode_options,
    '#default_value' => $conf['build_mode'],
  );

  return $form;
}

function ctools_user_links_content_type_edit_form_submit($form, &$form_state) {
  // Copy everything from our defaults.
  foreach (array_keys($form_state['plugin']['defaults']) as $key) {
    $form_state['conf'][$key] = $form_state['values'][$key];
  }
}

function ctools_user_links_content_type_admin_title($subtype, $conf, $context) {
  return t('"@s" links', array('@s' => $context->identifier));
}
