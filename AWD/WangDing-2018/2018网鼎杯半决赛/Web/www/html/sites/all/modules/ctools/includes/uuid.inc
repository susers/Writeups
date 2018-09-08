<?php

/**
 * @file
 * Enables ctools generated modules to use UUIDs without the UUID module enabled.
 * Per the ctools.module, this file only gets included if UUID doesn't exist.
 */

/**
 * Pattern for detecting a valid UUID.
 */
if (!defined('UUID_PATTERN')) {
  define('UUID_PATTERN', '[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}');
}

/**
 * Generates a UUID using the Windows internal GUID generator.
 *
 * @see http://php.net/com_create_guid
 */
function _ctools_uuid_generate_com() {
  // Remove {} wrapper and make lower case to keep result consistent.
  return drupal_strtolower(trim(com_create_guid(), '{}'));
}

/**
 * Generates an universally unique identifier using the PECL extension.
 */
function _ctools_uuid_generate_pecl() {
  $uuid_type = UUID_TYPE_DEFAULT;
  return uuid_create($uuid_type);
}

/**
 * Generates a UUID v4 using PHP code.
 *
 * Based on code from @see http://php.net/uniqid#65879 , but corrected.
 */
function _ctools_uuid_generate_php() {
  // The field names refer to RFC 4122 section 4.1.2.
  return sprintf('%04x%04x-%04x-4%03x-%04x-%04x%04x%04x',
    // 32 bits for "time_low".
    mt_rand(0, 65535), mt_rand(0, 65535),
    // 16 bits for "time_mid".
    mt_rand(0, 65535),
    // 12 bits after the 0100 of (version) 4 for "time_hi_and_version".
    mt_rand(0, 4095),
    bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '10', 0, 2)),
    // 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
    // (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
    // 8 bits for "clk_seq_low" 48 bits for "node".
    mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)
  );
}

// This is wrapped in an if block to avoid conflicts with PECL's uuid_is_valid().
/**
 * Check that a string appears to be in the format of a UUID.
 *
 * @param $uuid
 *  The string to test.
 *
 * @return
 *   TRUE if the string is well formed.
 */
if (!function_exists('uuid_is_valid')) {
  function uuid_is_valid($uuid) {
    return preg_match('/^' . UUID_PATTERN . '$/', $uuid);
  }

}
