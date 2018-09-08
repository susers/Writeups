<?php

/**
 * @file
 * Test PHP file for Libraries loading.

/**
 * Dummy function to see if this file was loaded.
 */
function _libraries_test_module_integration_file_post_load() {
}

// Call a function that is defined in the library file, to ensure that was
// loaded prior to this file.
_libraries_test_module_example_1();
