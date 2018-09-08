<?php

/**
 * @file
 * Libraries test theme.
 */

/**
 * Implements hook_libraries_info().
 */
function libraries_test_theme_libraries_info() {
  $libraries['example_theme'] = array(
    'name' => 'Example theme',
    'theme_altered' => FALSE,
  );
  $libraries['example_theme_integration_files'] = array(
    'name' => 'Example theme integration file',
    'library path' => drupal_get_path('module', 'libraries') . '/tests/libraries/example',
    'version' => '1',
    'integration files' => array(
      'libraries_test_theme' => array(
        'js' => array('libraries_test_theme.js'),
        'css' => array('libraries_test_theme.css'),
        'php' => array('libraries_test_theme.inc'),
      ),
    ),
  );
  return $libraries;
}

/**
 * Implements hook_libraries_info_alter().
 */
function libraries_test_theme_libraries_info_alter(&$libraries) {
  $libraries['example_theme']['theme_altered'] = TRUE;
}
