<?php

/**
 * @file
 * Plugin to provide access control based on whether a node belongs to a book.
 */

/**
 * Plugins are described by creating a $plugin array which will be used
 * by the system that includes this file.
 */
if (module_exists('book')) {
  $plugin = array(
    'title' => t("Book: node is in a book"),
    'description' => t('Control access based upon a node belonging to a book.'),
    'callback' => 'ctools_book_node_ctools_access_check',
    'default' => array('book' => array()),
    'settings form' => 'ctools_book_node_ctools_access_settings',
    'settings form submit' => 'ctools_book_node_ctools_access_settings_submit',
    'summary' => 'ctools_book_node_ctools_access_summary',
    'required context' => new ctools_context_required(t('Node'), 'node'),
  );
}

/**
 * Settings form for the 'by book_node' access plugin.
 */
function ctools_book_node_ctools_access_settings($form, &$form_state, $conf) {
  $options = array(
    'any' => t('In any book'),
  );
  $books = book_get_books();
  foreach ($books as $bid => $book) {
    $options[$bid] = $book['title'];
  }
  $form['settings']['book'] = array(
    '#title' => t('Book'),
    '#type' => 'checkboxes',
    '#options' => $options,
    '#description' => t('Pass only if the node belongs to one of the selected books'),
    '#default_value' => $conf['book'],
    '#required' => TRUE,
  );
  return $form;
}

/**
 * Check for access.
 */
function ctools_book_node_ctools_access_check($conf, $context) {
  // As far as I know there should always be a context at this point, but this
  // is safe.
  if (empty($context) || empty($context->data) || empty($context->data->book)) {
    return FALSE;
  }

  if ($conf['book']['any']) {
    return !empty($context->data->book);
  }

  foreach ($conf['book'] as $bid => $value) {
    if ($bid == 'any') {
      continue;
    }
    if ($value && ($bid == $context->data->book['bid'])) {
      return TRUE;
    }
  }

  return FALSE;
}

/**
 * Provide a summary description based upon the checked node_languages.
 */
function ctools_book_node_ctools_access_summary($conf, $context) {
  if ($conf['book']['any']) {
    return t('@identifier belongs to a book', array('@identifier' => $context->identifier));
  }

  $books = array();
  foreach ($conf['book'] as $bid => $value) {
    if ($value) {
      $node = node_load($bid);
      $books[] = $node->title;
    }
  }

  if (count($books) == 1) {
    return t('@identifier belongs to the book "@book"', array('@book' => $books[0], '@identifier' => $context->identifier));
  }

  return t('@identifier belongs in multiple books', array('@identifier' => $context->identifier));
}
