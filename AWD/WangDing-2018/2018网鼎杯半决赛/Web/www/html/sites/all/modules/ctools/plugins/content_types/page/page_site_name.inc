<?php

/**
 * @file
 * Plugin to handle the 'page_site_name' content type which allows the
 * site_name of the site to be embedded into a panel.
 */

/**
 * Plugins are described by creating a $plugin array which will be used by the
 * system that includes this file.
 */
$plugin = array(
  'title' => t('Site name'),
  'single' => TRUE,
  'icon' => 'icon_page.png',
  'description' => t('The name of the site, optionally links to the front page.'),
  'category' => t('Page elements'),
  'render last' => TRUE,
  'defaults' => array(
    'linked' => FALSE,
  ),
);

/**
 * Settings form for the Site Name pane.
 */
function ctools_page_site_name_content_type_edit_form($form, &$form_state) {
  $conf = $form_state['conf'];

  $form['linked'] = array(
    '#title' => t('Linked'),
    '#description' => t('Link the site name to the home page.'),
    '#type' => 'checkbox',
    '#default_value' => isset($conf['linked']) ? $conf['linked'] : FALSE,
  );

  return $form;
}

/**
 * The submit form stores the data in $conf.
 */
function ctools_page_site_name_content_type_edit_form_submit($form, &$form_state) {
  foreach (array_keys($form_state['plugin']['defaults']) as $key) {
    if (isset($form_state['values'][$key])) {
      $form_state['conf'][$key] = $form_state['values'][$key];
    }
  }
}

/**
 * Output function for the 'page_site_name' content type.
 *
 * Outputs the site_name for the current page.
 */
function ctools_page_site_name_content_type_render($subtype, $conf, $panel_args) {
  $block = new stdClass();

  $block->content = filter_xss_admin(variable_get('site_name', 'Drupal'));

  // Optionally link the site name to the homepage.
  if (!empty($conf['linked'])) {
    $block->content = l($block->content, '<front>', array('html' => TRUE));
  }

  return $block;
}
