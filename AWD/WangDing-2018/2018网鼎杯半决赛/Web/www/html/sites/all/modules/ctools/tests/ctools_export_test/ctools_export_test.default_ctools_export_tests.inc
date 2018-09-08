<?php

/**
 * @file
 */

/**
 * Implements hook_default_export_tests().
 */
function ctools_export_test_default_ctools_export_tests() {
  $ctools_export_tests = array();

  $ctools_export_test = new stdClass();
  $ctools_export_test->disabled = FALSE; /* Edit this to true to make a default export_test disabled initially */
  $ctools_export_test->api_version = 1;
  $ctools_export_test->machine = 'overridden_test';
  $ctools_export_test->title = 'Overridden test';
  $ctools_export_test->number = 1;
  $ctools_export_test->data = array(
    'test_1' => 'Test 1',
    'test_2' => 'Test 2',
  );
  $ctools_export_tests['overridden_test'] = $ctools_export_test;

  $ctools_export_test = new stdClass();
  $ctools_export_test->disabled = FALSE; /* Edit this to true to make a default export_test disabled initially */
  $ctools_export_test->api_version = 1;
  $ctools_export_test->machine = 'default_test';
  $ctools_export_test->title = 'Default test';
  $ctools_export_test->number = 2;
  $ctools_export_test->data = '';
  $ctools_export_tests['default_test'] = $ctools_export_test;

  return $ctools_export_tests;
}
