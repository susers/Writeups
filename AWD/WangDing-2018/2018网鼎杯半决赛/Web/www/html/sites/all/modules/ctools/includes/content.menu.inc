<?php

/**
 * @file
 * Contains menu item registration for the content tool.
 *
 * The menu items registered are AJAX callbacks for the things like
 * autocomplete and other tools needed by the content types.
 */

function ctools_content_menu(&$items) {
  $base = array(
    'access arguments' => array('access content'),
    'type' => MENU_CALLBACK,
    'file' => 'includes/content.menu.inc',
  );
  $items['ctools/autocomplete/%'] = array(
    'page callback' => 'ctools_content_autocomplete_entity',
    'page arguments' => array(2),
  ) + $base;
}

/**
 * Helper function for autocompletion of entity titles.
 */
function ctools_content_autocomplete_entity($entity_type, $string = '') {
  if ($string != '') {
    $entity_info = entity_get_info($entity_type);

    if (!module_exists('entity')) {
      module_load_include('inc', 'ctools', 'includes/entity-access');
      _ctools_entity_access($entity_info, $entity_type);
    }

    // We must query all ids, because if every one of the 10 don't have access
    // the user may never be able to autocomplete a node title.
    $preg_matches = array();
    $matches = array();
    $match = preg_match('/\[id: (\d+)\]/', $string, $preg_matches);
    if (!$match) {
      $match = preg_match('/^id: (\d+)/', $string, $preg_matches);
    }
    // If an ID match was found, use that ID rather than the whole string.
    if ($match) {
      $entity_id = $preg_matches[1];
      $results = _ctools_getReferencableEntities($entity_type, $entity_info, $entity_id, '=', 1);
    }
    else {
      // We cannot find results if the entity doesn't have a label to search.
      if (!isset($entity_info['entity keys']['label'])) {
        drupal_json_output(array("[id: NULL]" => '<span class="autocomplete_title">' . t('Entity Type !entity_type does not support autocomplete search.', array('!entity_type' => $entity_type)) . '</span>'));
        return;
      }
      $results = _ctools_getReferencableEntities($entity_type, $entity_info, $string, 'LIKE', 10);
    }
    foreach ($results as $entity_id => $result) {
      $matches[$result['label'] . " [id: $entity_id]"] = '<span class="autocomplete_title">' . check_plain($result['label']) . '</span>';
      $matches[$result['label'] . " [id: $entity_id]"] .= isset($result['bundle']) ? ' <span class="autocomplete_bundle">(' . check_plain($result['bundle']) . ')</span>' : '';
    }

    drupal_json_output($matches);
  }
}

/**
 * Use EntityReference_SelectionHandler_Generic class to build our search query.
 */
function _ctools_buildQuery($entity_type, $entity_info, $match = NULL, $match_operator = 'CONTAINS') {
  $base_table = $entity_info['base table'];
  $label_key = $entity_info['entity keys']['label'];
  $query = db_select($base_table)
    ->fields($base_table, array($entity_info['entity keys']['id']));

  if (isset($match)) {
    if (isset($label_key)) {
      $query->condition($base_table . '.' . $label_key, '%' . $match . '%', $match_operator);
    }
    // This should never happen, but double check just in case.
    else {
      return array();
    }
  }
  // Add a generic entity access tag to the query.
  $query->addTag('ctools');

  // We have to perform two checks. First check is a query alter (with tags)
  // in an attempt to only return results that have access. However, this is
  // not full-proof since entities many not implement hook_access query tag.
  // This is why we have a second check after entity load, before we display
  // the label of an entity.
  if ($entity_type == 'comment') {
    // Adding the 'comment_access' tag is sadly insufficient for comments: core 
    // requires us to also know about the concept of 'published' and 
    // 'unpublished'.
    if (!user_access('administer comments')) {
      $query->condition('comment.status', COMMENT_PUBLISHED);
    }

    // Join to a node if the user does not have node access bypass permissions 
    // to obey node published permissions.
    if (!user_access('bypass node access')) {
      $node_alias = $query->innerJoin('node', 'n', '%alias.nid = comment.nid');
      $query->condition($node_alias . '.status', NODE_PUBLISHED);
    }
    $query->addTag('node_access');
  }
  else {
    $query->addTag($entity_type . '_access');
  }

  // Add the sort option.
  if (isset($label_key)) {
    $query->orderBy($base_table . '.' . $label_key, 'ASC');
  }

  return $query;
}

/**
 * Private function to get referencable entities. Based on code from the
 * Entity Reference module.
 */
function _ctools_getReferencableEntities($entity_type, $entity_info, $match = NULL, $match_operator = 'LIKE', $limit = 0) {
  global $user;
  $account = $user;
  $options = array();
  // We're an entity ID, return the id.
  if (is_numeric($match) && $match_operator == '=') {
    if ($entity = array_shift(entity_load($entity_type, array($match)))) {
      if (isset($entity_info['access callback']) && function_exists($entity_info['access callback'])) {
        if ($entity_info['access callback']('view', $entity, $account, $entity_type)) {
          $label = entity_label($entity_type, $entity);
          return array(
            $match => array(
              'label' => !empty($label) ? $label : $entity->{$entity_info['entity keys']['id']},
              'bundle' => !empty($entity_info['entity keys']['bundle']) ? check_plain($entity->{$entity_info['entity keys']['bundle']}) : NULL,
            ),
          );
        }
      }
    }
    // If you don't have access, or an access callback or a valid entity, just
    // Return back the Entity ID.
    return array(
      $match => array(
        'label' => $match,
        'bundle' => NULL,
      ),
    );
  }

  // We have matches, build a query to fetch the result.
  if ($query = _ctools_buildQuery($entity_type, $entity_info, $match, $match_operator)) {
    if ($limit > 0) {
      $query->range(0, $limit);
    }

    $results = $query->execute();

    if (!empty($results)) {
      foreach ($results as $record) {
        $entities = entity_load($entity_type, array($record->{$entity_info['entity keys']['id']}));
        $entity = array_shift($entities);
        if (isset($entity_info['access callback']) && function_exists($entity_info['access callback'])) {
          if ($entity_info['access callback']('view', $entity, $account, $entity_type)) {
            $label = entity_label($entity_type, $entity);
            $options[$record->{$entity_info['entity keys']['id']}] = array(
              'label' => !empty($label) ? $label : $entity->{$entity_info['entity keys']['id']},
              'bundle' => !empty($entity_info['entity keys']['bundle']) ? check_plain($entity->{$entity_info['entity keys']['bundle']}) : NULL,
            );
          }
        }
      }
    }
    return $options;
  }
  return array();
}
