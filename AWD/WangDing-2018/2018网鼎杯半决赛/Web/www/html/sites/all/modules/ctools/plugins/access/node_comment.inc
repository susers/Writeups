<?php

/**
 * @file
 * Plugin to provide access control based upon node comment status.
 */

/**
 * Plugins are described by creating a $plugin array which will be used
 * by the system that includes this file.
 */
$plugin = array(
  'title' => t("Node: comments are open"),
  'description' => t('Control access by the nodes comment status.'),
  'callback' => 'ctools_node_comment_ctools_access_check',
  'summary' => 'ctools_node_comment_ctools_access_summary',
  'required context' => new ctools_context_required(t('Node'), 'node'),
);

/**
 * Checks for access.
 */
function ctools_node_comment_ctools_access_check($conf, $context) {
  return (!empty($context->data) && $context->data->comment == 2);
}

/**
 * Provides a summary description based upon the checked node_status.
 */
function ctools_node_comment_ctools_access_summary($conf, $context) {
  return t('Returns true if the nodes comment status is "open".');
}
