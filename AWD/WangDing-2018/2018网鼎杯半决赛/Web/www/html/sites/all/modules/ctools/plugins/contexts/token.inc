<?php

/**
 * @file
 * Provide a global context to allow for token support.
 */

$plugin = array(
  'title' => t('Token'),
  'description' => t('A context that contains token replacements from token.module.'),
  'context' => 'ctools_context_create_token',  // func to create context
  'context name' => 'token',
  'keyword' => 'token',
  'convert list' => 'ctools_context_token_convert_list',
  'convert' => 'ctools_context_token_convert',
);

/**
 * Create a context from manual configuration.
 *
 * @param $empty
 *   Unused.
 * @param $data
 *   Unused.
 * @param $conf
 *   Unused.
 *
 * @return ctools_context
 *   A context of type token, with the plugin set appropriately.
 */
function ctools_context_create_token($empty, $data = NULL, $conf = FALSE) {
  $context = new ctools_context('token');
  $context->plugin = 'token';

  return $context;
}

/**
 * Implementation of hook_ctools_context_convert_list().
 *
 * @return array|null
 *   An array of token type information, keyed by 'type:id', or NULL if
 *   none found.
 */
function ctools_context_token_convert_list() {
  $tokens = token_info();
  // Initialise $list here?
  foreach ($tokens['types'] as $type => $type_info) {
    if (empty($type_info['needs-data'])) {
      $real_type = isset($type_info['type']) ? $type_info['type'] : $type;
      foreach ($tokens['tokens'][$real_type] as $id => $info) {
        $key = "$type:$id";
        if (!isset($list[$key])) {
          $list[$key] = $type_info['name'] . ': ' . $info['name'];
        }
      }
    }
  }

  return $list;
}

/**
 * Token conversion function: look up the token and return it's value.
 *
 * @param $context
 *   Unused.
 * @param string $token
 *   The name of the token.
 *
 * @return array|null
 *   The token value, or NULL if not found.
 *
 * @see ctools_context_convert_context()
 */
function ctools_context_token_convert($context, $token) {
  $tokens = token_info();
  list($type, $token) = explode(':', $token, 2);
  $parts = explode(':', $token, 2);
  $real_type = isset($tokens['types'][$type]['type']) ? $tokens['types'][$type]['type'] : $type;
  if (isset($tokens['tokens'][$real_type][$parts[0]])) {
    $values = token_generate($type, array($token => $token));
    if (isset($values[$token])) {
      return $values[$token];
    }
  }
  return NULL;
}
