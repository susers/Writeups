<?php

/**
 * @file
 */

if (module_exists('book')) {
  /**
   * Plugins are described by creating a $plugin array which will be used
   * by the system that includes this file.
   */
  $plugin = array(
    'single' => TRUE,
    'title' => t('Book navigation menu'),
    'icon' => drupal_get_path('module', 'ctools') . '/plugins/content_types/block/icon_core_block_menu.png',
    'description' => t('The book menu belonging to the current book node.'),
    'required context' => new ctools_context_required(t('Node'), 'node'),
    'category' => t('Node'),
  );
}

function ctools_node_book_menu_content_type_render($subtype, $conf, $panel_args, $context) {
  $node = isset($context->data) ? clone $context->data : NULL;
  $block = new stdClass();
  $block->module = 'book_menu';

  if ($conf['override_title']) {
    $block->title = t($conf['override_title_text']);
  }
  else {
    $block->title = t('Book navigation menu');
  }
  if ($node) {
    $block->delta = $node->nid;
    // TODO: the value is not available somehow?!?
    $book_block_mode = isset($conf['book_block_mode']) ? $conf['book_block_mode'] : 'book pages';

    // Code below is taken from function book_block_view().
    $current_bid = empty($node->book['bid']) ? 0 : $node->book['bid'];

    if ($book_block_mode === 'all pages') {
      $block->subject = t('Book navigation');
      $book_menus = array();
      $pseudo_tree = array(0 => array('below' => FALSE));
      foreach (book_get_books() as $book_id => $book) {
        if ($book['bid'] === $current_bid) {
          // If the current page is a node associated with a book, the menu
          // needs to be retrieved.
          $book_menus[$book_id] = menu_tree_output(menu_tree_all_data($node->book['menu_name'], $node->book));
        }
        else {
          // Since we know we will only display a link to the top node, there
          // is no reason to run an additional menu tree query for each book.
          $book['in_active_trail'] = FALSE;
          // Check whether user can access the book link.
          $book_node = node_load($book['nid']);
          $book['access'] = node_access('view', $book_node);
          $pseudo_tree[0]['link'] = $book;
          $book_menus[$book_id] = menu_tree_output($pseudo_tree);
        }
      }
      $book_menus['#theme'] = 'book_all_books_block';
      $block->content = $book_menus;
    }
    elseif ($current_bid) {
      // Only display this block when the user is browsing a book.
      $select = db_select('node', 'n')
        ->fields('n', array('title'))
        ->condition('n.nid', $node->book['bid'])
        ->addTag('node_access');
      $title = $select->execute()->fetchField();
      // Only show the block if the user has view access for the top-level node.
      if ($title) {
        $tree = menu_tree_all_data($node->book['menu_name'], $node->book);
        // There should only be one element at the top level.
        $data = array_shift($tree);
        // TODO: subject is not rendered.
        $block->subject = theme('book_title_link', array('link' => $data['link']));
        $block->content = ($data['below']) ? menu_tree_output($data['below']) : '';
      }
    }
  }
  else {
    $block->content = t('Book navigation pager goes here.');
    $block->delta = 'unknown';
  }

  return $block;
}

function ctools_node_book_menu_content_type_admin_title($subtype, $conf, $context) {
  return t('"@s" book navigation pager', array('@s' => $context->identifier));
}

function ctools_node_book_menu_content_type_edit_form($form, &$form_state) {
  // Grab block form from the book module.
  $block_form = book_block_configure($delta = '');
  // TODO: this does not work yet.
  //       See TODO in: ctools_node_book_menu_content_type_render.
  if (isset($form_state['input']['book_block_mode'])) {
    $block_form['book_block_mode']['#default_value'] = $form_state['input']['book_block_mode'];
  }
  $form += $block_form;
  return $form;
}
