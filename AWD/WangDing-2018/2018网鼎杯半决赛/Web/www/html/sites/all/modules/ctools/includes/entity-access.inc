<?php

/**
 * @file
 * Provides various callbacks for the whole core module integration.
 * This is a copy of Entity API's functionality for use when Entity API isn't
 * Enabled, and only works on view functions.
 */

/**
 * Core hack to include entity api-esque 'access callback' functions to core
 * entities without needing to rely on entity api.
 * Exception: We don't touch file entity. You must have entity API enabled to
 * view files.
 */
function _ctools_entity_access(&$entity_info, $entity_type) {
  // If the access callback is already set, don't change anything.
  if (isset($entity_info['access callback'])) {
    return;
  }

  switch ($entity_type) {
    case 'node':
      // Sad panda, we don't use Entity API, lets manually add access callbacks.
      $entity_info['access callback'] = 'ctools_metadata_no_hook_node_access';
      break;

    case 'user':
      $entity_info['access callback'] = 'ctools_metadata_user_access';
      break;

    case 'comment':
      if (module_exists('comment')) {
        $entity_info['access callback'] = 'ctools_metadata_comment_access';
      }
      break;

    case 'taxonomy_term':
      if (module_exists('taxonomy')) {
        $entity_info['access callback'] = 'ctools_metadata_taxonomy_access';
      }
      break;

    case 'taxonomy_vocabulary':
      if (module_exists('taxonomy')) {
        $entity_info['access callback'] = 'ctools_metadata_taxonomy_access';
      }
      break;
  }
}

/**
 * Access callback for the node entity.
 *
 * This function does not implement hook_node_access(), thus it may not be
 * called ctools_metadata_node_access().
 *
 * @see entity_access()
 *
 * @param $op
 *   The operation being performed. One of 'view', 'update', 'create' or
 *   'delete'.
 * @param $node
 *   A node to check access for. Must be a node object. Must have nid,
 *   except in the case of 'create' operations.
 * @param $account
 *   The user to check for. Leave it to NULL to check for the global user.
 *
 * @throws EntityMalformedException
 *
 * @return bool
 *   TRUE if access is allowed, FALSE otherwise.
 */
function ctools_metadata_no_hook_node_access($op, $node = NULL, $account = NULL) {
  // First deal with the case where a $node is provided.
  if (isset($node)) {
    // If a non-default revision is given, incorporate revision access.
    $default_revision = node_load($node->nid);
    if ($node->vid !== $default_revision->vid) {
      return _node_revision_access($node, $op, $account);
    }
    else {
      return node_access($op, $node, $account);
    }
  }
  // No node is provided. Check for access to all nodes.
  if (user_access('bypass node access', $account)) {
    return TRUE;
  }
  if (!user_access('access content', $account)) {
    return FALSE;
  }
  if ($op == 'view' && node_access_view_all_nodes($account)) {
    return TRUE;
  }
  return FALSE;
}

/**
 * Access callback for the user entity.
 */
function ctools_metadata_user_access($op, $entity = NULL, $account = NULL, $entity_type) {
  $account = isset($account) ? $account : $GLOBALS['user'];
  // Grant access to the users own user account and to the anonymous one.
  if (isset($entity) && $op != 'delete' && (($entity->uid == $account->uid && $entity->uid) || (!$entity->uid && $op == 'view'))) {
    return TRUE;
  }
  if (user_access('administer users', $account) || user_access('access user profiles', $account) && $op == 'view' && $entity->status) {
    return TRUE;
  }
  return FALSE;
}

/**
 * Access callback for the comment entity.
 */
function ctools_metadata_comment_access($op, $entity = NULL, $account = NULL) {
  // When determining access to a comment, if comment has an associated node,
  // the user must be able to view the node in order to access the comment.
  if (isset($entity->nid)) {
    if (!node_access('view', node_load($entity->nid), $account)) {
      return FALSE;
    }
  }

  // Comment administrators are allowed to perform all operations on all
  // comments.
  if (user_access('administer comments', $account)) {
    return TRUE;
  }

  // Unpublished comments can never be accessed by non-admins.
  if (isset($entity->status) && $entity->status == COMMENT_NOT_PUBLISHED) {
    return FALSE;
  }

  if (user_access('access comments', $account) && $op == 'view') {
    return TRUE;
  }
  return FALSE;
}

/**
 * Access callback for the taxonomy entities.
 */
function ctools_metadata_taxonomy_access($op, $entity = NULL, $account = NULL, $entity_type) {
  if ($entity_type == 'taxonomy_vocabulary') {
    return user_access('administer taxonomy', $account);
  }
  if (user_access('administer taxonomy', $account) || user_access('access content', $account) && $op == 'view') {
    return TRUE;
  }
  return FALSE;
}
