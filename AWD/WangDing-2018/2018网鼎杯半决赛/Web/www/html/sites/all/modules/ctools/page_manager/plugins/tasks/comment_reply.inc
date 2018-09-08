<?php

/**
 * @file
 */

/**
 * Specialized implementation of hook_page_manager_task_tasks(). See api-task.html for
 * more information.
 */
function page_manager_comment_reply_page_manager_tasks() {
  if (!module_exists('comment')) {
    return;
  }

  return array(
    // This is a 'page' task and will fall under the page admin UI.
    'task type' => 'page',

    'title' => t('Comment Reply page'),
    'admin title' => t('Comment Reply page'),
    'admin description' => t('When enabled, this overrides the default Drupal behavior for the site contact page at <em>/contact</em>. If no variant is selected, the default Drupal contact form will be used.'),
    'admin path' => 'comment/reply/%node',

    // Menu hooks so that we can alter the node/%node menu entry to point to us.
    'hook menu alter' => 'page_manager_comment_reply_menu_alter',

    // This is task uses 'context' handlers and must implement these to give the
    // handler data it needs.
    'handler type' => 'context',
    'get arguments' => 'page_manager_comment_reply_get_arguments',
    'get context placeholders' => 'page_manager_comment_reply_get_contexts',

  // Allow this to be enabled or disabled:
    'disabled' => variable_get('page_manager_comment_reply_disabled', TRUE),
    'enable callback' => 'page_manager_comment_reply_enable',
    'access callback' => 'page_manager_comment_reply_check',
  );
}

/**
 * Callback to enable/disable the page from the UI.
 */
function page_manager_comment_reply_enable($cache, $status) {
  variable_set('page_manager_comment_reply_disabled', $status);
  // Set a global flag so that the menu routine knows it needs
  // to set a message if enabling cannot be done.
  if (!$status) {
    $GLOBALS['page_manager_enabling_comment_reply'] = TRUE;
  }
}

/**
 * Entry point for our overridden comment.
 */
function page_manager_comment_reply_page($node, $pid = NULL) {
  // Load my task plugin.
  $task = page_manager_get_task('comment_reply');

  // Load the node into a context.
  ctools_include('context');
  ctools_include('context-task-handler');

  $contexts = ctools_context_handler_get_task_contexts($task, '', array($node, $pid));

  if (array_key_exists('argument_cid_3', $contexts) && $contexts['argument_cid_3']->data->nid != $node->nid) {
    // Attempting to reply to a comment not belonging to the current nid.
    drupal_set_message(t('The comment you are replying to does not exist.'), 'error');
    drupal_goto("node/$node->nid");
  }

  $output = ctools_context_handler_render($task, '', $contexts, array($node, $pid));
  if ($output !== FALSE) {
    return $output;
  }

  $function = 'comment_reply';
  foreach (module_implements('page_manager_override') as $module) {
    $call = $module . '_page_manager_override';
    if (($rc = $call('comment_reply')) && function_exists($rc)) {
      $function = $rc;
      break;
    }
  }

  module_load_include('inc', 'comment', 'comment.pages');
  return $function($node, $pid);
}

/**
 * Callback to get arguments provided by this task handler.
 *
 * Since this is the node view and there is no UI on the arguments, we
 * create dummy arguments that contain the needed data.
 */
function page_manager_comment_reply_get_arguments($task, $subtask_id) {
  return array(
    array(
      'keyword' => 'node',
      'identifier' => t('Node being commented on'),
      'id' => 2,
      'name' => 'entity_id:node',
      'settings' => array(),
    ),
    array(
      'keyword' => 'comment',
      'identifier' => t('Comment being replied to'),
      'id' => 3,
      'name' => 'entity_id:comment',
      'settings' => array(),
    ),
  );
}

/**
 * Callback to get context placeholders provided by this handler.
 */
function page_manager_comment_reply_get_contexts($task, $subtask_id) {
  return ctools_context_get_placeholders_from_argument(page_manager_comment_reply_get_arguments($task, $subtask_id));
}

/**
 * Callback defined by page_manager_node_view_page_manager_tasks().
 *
 * Alter the node view input so that node view comes to us rather than the
 * normal node view process.
 */
function page_manager_comment_reply_menu_alter(&$items, $task) {
  if (variable_get('page_manager_comment_reply_disabled', TRUE)) {
    return;
  }
  // Override the node view handler for our purpose.
  $callback = $items['comment/reply/%node']['page callback'];
  if ($callback == 'comment_reply' || variable_get('page_manager_override_anyway', FALSE)) {
    $items['comment/reply/%node']['page callback'] = 'page_manager_comment_reply_page';
    $items['comment/reply/%node']['file path'] = $task['path'];
    $items['comment/reply/%node']['file'] = $task['file'];
  }
  else {
    // Automatically disable this task if it cannot be enabled.
    variable_set('page_manager_comment_reply_disabled', TRUE);
    if (!empty($GLOBALS['page_manager_enabling_comment_reply'])) {
      drupal_set_message(t('Page manager module is unable to enable comment/reply/%node because some other module already has overridden with %callback.', array('%callback' => $callback)), 'error');
    }
  }

  // @todo override node revision handler as well?
}

/**
 * Callback to determine if a page is accessible.
 *
 * @param $task
 *   The task plugin.
 * @param $subtask_id
 *   The subtask id
 * @param $contexts
 *   The contexts loaded for the task.
 *
 * @return
 *   TRUE if the current user can access the page.
 */
function page_manager_comment_reply_access_check($task, $subtask_id, $contexts) {
  $context = reset($contexts);
  return TRUE;
}
