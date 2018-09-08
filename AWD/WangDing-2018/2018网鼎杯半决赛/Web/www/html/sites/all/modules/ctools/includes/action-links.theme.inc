<?php

/**
 * @file
 * Theme function for wrapping menu local actions.
 */

/**
 * Delegated implementation of hook_theme()
 */
function ctools_action_links_theme(&$items) {
  $items['ctools_menu_local_actions_wrapper'] = array(
    'render element' => 'links',
    'file' => 'includes/action-links.theme.inc',
  );
}

/**
 * Render a menu local actions wrapper.
 *
 * @param $links
 *   Local actions links.
 * @param $attributes
 *   An array of attributes to append to the wrapper.
 */
function theme_ctools_menu_local_actions_wrapper($variables) {
  $links = drupal_render($variables['links']);

  if (empty($links)) {
    return;
  }

  return '<ul class="action-links">' . $links . '</ul>';
}
