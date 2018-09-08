<?php

/**
 * @file
 * Provide Drupal blocks as content.
 *
 * Since blocks don't provide all of the features we do, we have to do a little
 * extra work, including providing icons and categories for core blocks. Blocks
 * from contrib modules get to provide their own stuff, or get relegated to
 * the old "Miscellaneous" category.
 */

/**
 * Plugins are described by creating a $plugin array which will be used
 * by the system that includes this file.
 */
$plugin = array(
  // And this is just the administrative title.
  // All our callbacks are named according to the standard pattern and can be deduced.
  'title' => t('Block'),
  'content type' => 'ctools_block_content_type_content_type',
);

/**
 * Return the block content types with the specified $subtype_id.
 */
function ctools_block_content_type_content_type($subtype_id) {
  list($module, $delta) = explode('-', $subtype_id, 2);
  $module_blocks = module_invoke($module, 'block_info');
  if (isset($module_blocks[$delta])) {
    return _ctools_block_content_type_content_type($module, $delta, $module_blocks[$delta]);
  }
}

/**
 * Return all block content types available.
 *
 * Modules wanting to make special adjustments the way that CTools handles their blocks
 * can implement an extension to the hook_block() family, where the function name is
 * of the form "$module . '_ctools_block_info'".
 */
function ctools_block_content_type_content_types() {
  $types = &drupal_static(__FUNCTION__);
  if (isset($types)) {
    return $types;
  }

  $types = array();
  foreach (module_implements('block_info') as $module) {
    $module_blocks = module_invoke($module, 'block_info');
    if ($module_blocks) {
      foreach ($module_blocks as $delta => $block) {
        $info = _ctools_block_content_type_content_type($module, $delta, $block);
        // This check means modules can remove their blocks; particularly useful
        // if they offer the block some other way (like we do for views)
        if ($info) {
          $types["$module-$delta"] = $info;
        }
      }
    }
  }
  return $types;
}

/**
 * Return an info array for a specific block.
 */
function _ctools_block_content_type_content_type($module, $delta, $block) {
  // strip_tags used because it goes through check_plain and that
  // just looks bad.
  $info = array(
    'title' => strip_tags($block['info']),
  );

  // Ask around for further information by invoking the hook_block() extension.
  $function = $module . '_ctools_block_info';
  if (!function_exists($function)) {
    $function = 'ctools_default_block_info';
  }
  $function($module, $delta, $info);

  return $info;
}

/**
 * Load block info from the database.
 *
 * This is copied from _block_load_blocks(). It doesn't use that
 * function because _block_load_blocks sorts by region, and it
 * doesn't cache its results anyway.
 */
function _ctools_block_load_blocks() {
  if (!module_exists('block')) {
    return array();
  }

  $blocks = &drupal_static(__FUNCTION__, NULL);
  if (!isset($blocks)) {
    global $theme_key;

    $query = db_select('block', 'b');
    $result = $query
      ->fields('b')
      ->condition('b.theme', $theme_key)
      ->orderBy('b.region')
      ->orderBy('b.weight')
      ->orderBy('b.module')
      ->addTag('block_load')
      ->addTag('translatable')
      ->execute();

    $block_info = $result->fetchAllAssoc('bid');
    // Allow modules to modify the block list.
    drupal_alter('block_list', $block_info);

    $blocks = array();
    foreach ($block_info as $block) {
      $blocks["{$block->module}_{$block->delta}"] = $block;
    }
  }

  return $blocks;
}

/**
 * Fetch the stored info for a block.
 *
 * The primary reason to use this is so that modules which perform alters
 * can have their alters make it to the block.
 */
function _ctools_get_block_info($module, $delta) {
  $blocks = _ctools_block_load_blocks();

  $key = $module . '_' . $delta;
  if (isset($blocks[$key])) {
    return $blocks[$key];
  }
}

/**
 * Output function for the 'block' content type. Outputs a block
 * based on the module and delta supplied in the configuration.
 */
function ctools_block_content_type_render($subtype, $conf) {
  list($module, $delta) = _ctools_block_get_module_delta($subtype, $conf);

  $info = _ctools_get_block_info($module, $delta);
  $block = module_invoke($module, 'block_view', $delta);

  if (!empty($info)) {
    // Valid PHP function names cannot contain hyphens.
    $block_delta = str_replace('-', '_', $delta);

    // Allow modules to modify the block before it is viewed, via either
    // hook_block_view_alter() or hook_block_view_MODULE_DELTA_alter().
    drupal_alter(array('block_view', "block_view_{$module}_{$block_delta}"), $block, $info);
  }

  if (empty($block)) {
    return;
  }

  $block = (object) $block;
  $block->module = $module;
  $block->delta = $delta;

  if (!isset($block->title)) {
    if ($module == 'block' && !empty($info) && isset($info->title)) {
      $block->title = $info->title;
    }
    elseif (isset($block->subject)) {
      $block->title = $block->subject;
    }
    else {
      $block->title = NULL;
    }
  }

  if (module_exists('block') && user_access('administer blocks')) {
    $block->admin_links = array(
      array(
        'title' => t('Configure block'),
        'href' => "admin/structure/block/manage/$module/$delta/configure",
        'query' => drupal_get_destination(),
      ),
    );
  }

  return $block;
}

/**
 * Empty form so we can have the default override title.
 */
function ctools_block_content_type_edit_form($form, &$form_state) {
  // Does nothing!
  return $form;
}

/**
 * Submit function to fix the subtype for really old panel panes.
 */
function ctools_block_content_type_edit_form_submit($form, &$form_state) {
  if (empty($form_state['subtype']) && isset($form_state['pane'])) {
    $form_state['pane']->subtype = $form_state['conf']['module'] . '-' . $form_state['conf']['delta'];
    unset($form_state['conf']['module']);
    unset($form_state['conf']['delta']);
  }
}

/**
 * Returns the administrative title for a type.
 */
function ctools_block_content_type_admin_title($subtype, $conf) {
  list($module, $delta) = _ctools_block_get_module_delta($subtype, $conf);
  $block = module_invoke($module, 'block_info');
  if (empty($block) || empty($block[$delta])) {
    return t('Deleted/missing block @module-@delta', array('@module' => $module, '@delta' => $delta));
  }

  // The block description reported by hook_block() is plain text, but the title
  // reported by this hook should be HTML.
  $title = check_plain($block[$delta]['info']);
  return $title;
}

/**
 * Output function for the 'block' content type. Outputs a block
 * based on the module and delta supplied in the configuration.
 */
function ctools_block_content_type_admin_info($subtype, $conf) {
  list($module, $delta) = _ctools_block_get_module_delta($subtype, $conf);
  $block = (object) module_invoke($module, 'block_view', $delta);

  if (!empty($block)) {
    // Sanitize the block because <script> tags can hose javascript up:
    if (!empty($block->content)) {
      $block->content = filter_xss_admin(render($block->content));
    }

    if (!empty($block->subject)) {
      $block->title = $block->subject;
    }
    elseif (empty($block->title)) {
      $block->title = t('No title');
    }
    return $block;
  }
}

function _ctools_block_get_module_delta($subtype, $conf) {
  if (strpos($subtype, '-')) {
    return explode('-', $subtype, 2);
  }
  else {
    return array($conf['module'], $conf['delta']);
  }
}

/**
 * Provide default icon and categories for blocks when modules don't do this
 * for us.
 */
function ctools_default_block_info($module, $delta, &$info) {
  $core_modules = array('aggregator', 'block', 'blog', 'blogapi', 'book', 'color', 'comment', 'contact', 'drupal', 'filter', 'forum', 'help', 'legacy', 'locale', 'menu', 'node', 'path', 'ping', 'poll', 'profile', 'search', 'statistics', 'taxonomy', 'throttle', 'tracker', 'upload', 'user', 'watchdog', 'system');

  if (in_array($module, $core_modules)) {
    $info['icon'] = 'icon_core_block.png';
    $info['category'] = t('Miscellaneous');
  }
  else {
    $info['icon'] = 'icon_contrib_block.png';
    $info['category'] = t('Miscellaneous');
  }
}

/**
 * These are all on behalf of modules that don't implement ctools but that
 * we care about.
 */
function menu_ctools_block_info($module, $delta, &$info) {
  $info['icon'] = 'icon_core_block_menu.png';
  $info['category'] = t('Menus');
  if ($delta == 'primary-links' || $delta == 'secondary-links') {
    $info['icon'] = 'icon_core_primarylinks.png';
  }
}

function forum_ctools_block_info($module, $delta, &$info) {
  $info['category'] = t('Activity');
  switch ($delta) {
    case 'active':
      $info['icon'] = 'icon_core_activeforumtopics.png';
      break;

    case 'new':
      $info['icon'] = 'icon_core_newforumtopics.png';
      break;

    default:
      // Safety net.
      ctools_default_block_info($module, $delta, $info);
  }
}

function profile_ctools_block_info($module, $delta, &$info) {
  // Hide the author information block which isn't as rich as what we can
  // do with context.
  $info = NULL;
}

function book_ctools_block_info($module, $delta, &$info) {
  $info['title'] = t('Book navigation menu');
  $info['icon'] = 'icon_core_block_menu.png';
  $info['category'] = t('Node');
}

function blog_ctools_block_info($module, $delta, &$info) {
  $info['icon'] = 'icon_core_recentblogposts.png';
  $info['category'] = t('Activity');
}

function poll_ctools_block_info($module, $delta, &$info) {
  $info['icon'] = 'icon_core_recentpoll.png';
  $info['category'] = t('Activity');
}

function comment_ctools_block_info($module, $delta, &$info) {
  $info['icon'] = 'icon_core_recentcomments.png';
  $info['category'] = t('Activity');
}

function search_ctools_block_info($module, $delta, &$info) {
  $info['icon'] = 'icon_core_searchform.png';
  $info['category'] = t('Widgets');
}

function node_ctools_block_info($module, $delta, &$info) {
  $info['icon'] = 'icon_core_syndicate.png';
  $info['category'] = t('Widgets');
}

function aggregator_ctools_block_info($module, $delta, &$info) {
  $info['icon'] = 'icon_core_syndicate.png';
  $info['category'] = t('Feeds');
}

function block_ctools_block_info($module, $delta, &$info) {
  $info['icon'] = 'icon_core_block_empty.png';
  $info['category'] = t('Custom blocks');

  // The title of custom blocks from the block module is stored in the
  // {block} table. Look for it in the default theme as a reasonable
  // default value for the title.
  $block_info_cache = drupal_static(__FUNCTION__);
  if (!isset($block_info_cache)) {
    $block_info_cache = db_select('block', 'b')
      ->fields('b')
      ->condition('b.module', 'block')
      ->condition('b.theme', variable_get('theme_default', 'bartik'))
      ->addTag('block_load')
      ->addTag('translatable')
      ->execute()
      ->fetchAllAssoc('delta');
  }

  if (isset($block_info_cache[$delta])) {
    $info['defaults'] = array(
      'override_title' => TRUE,
      'override_title_text' => $block_info_cache[$delta]->title,
    );
  }
}

function user_ctools_block_info($module, $delta, &$info) {
  $info['category'] = t('Activity');
  switch ($delta) {
    case 'login':
      $info['icon'] = 'icon_core_userlogin.png';
      $info['category'] = t('Widgets');
      // Provide a custom render callback, because the default login block
      // will not render on /user, /user/login, or any other URL beginning
      // /user (unless it's a user-specific page such as /user/123).
      $info['render callback'] = 'ctools_user_login_pane_render';
      break;

    case 'new':
      $info['icon'] = 'icon_core_whosnew.png';
      break;

    case 'online':
      $info['icon'] = 'icon_core_whosonline.png';
      break;

    default:
      // Safety net.
      ctools_default_block_info($module, $delta, $info);
  }
}

function locale_ctools_block_info($module, $delta, &$info) {
  $info['icon'] = 'icon_core_languageswitcher.png';
  $info['category'] = t('Widgets');
}

function statistics_ctools_block_info($module, $delta, &$info) {
  $info['icon'] = 'icon_core_popularcontent.png';
  $info['category'] = t('Activity');
}

function system_ctools_block_info($module, $delta, &$info) {
  // Remove the main content fake block.
  if ($delta == 'main') {
    $info = NULL;
    return;
  }

  $menus = array('main-menu', 'management', 'navigation', 'user-menu');

  if (in_array($delta, $menus)) {
    $info['icon'] = 'icon_core_block_menu.png';
    $info['category'] = t('Menus');

    if ($delta == 'navigation') {
      $info['icon'] = 'icon_core_navigation.png';
    }

    return;
  }

  $info['icon'] = 'icon_core_drupal.png';
  if ($delta == 'help') {
    $info['category'] = t('Page elements');
    return;
  }

  $info['category'] = t('Widgets');
}

function ctools_user_login_pane_render($subtype, $conf, $panel_args, $contexts) {
  list($module, $delta) = _ctools_block_get_module_delta($subtype, $conf);

  // The login form is only visible to anonymous users.
  global $user;
  if ($user->uid) {
    return;
  }

  $info = new stdClass();
  $info->module = $module;
  $info->delta = $delta;

  $block = array();
  $block['subject'] = t('User login');
  // Manually set the content (rather than invoking block_view) because the
  // block implementation won't render on certain URLs.
  $block['content'] = drupal_get_form('user_login_block');

  // Allow modules to modify the block before it is viewed, via either
  // hook_block_view_alter() or hook_block_view_MODULE_DELTA_alter().
  drupal_alter(array('block_view', "block_view_{$module}_{$delta}"), $block, $info);
  $block = (object) $block;

  if (empty($block)) {
    return;
  }

  $block->module = $module;
  $block->delta = $delta;

  // $block->title is not set for the blocks returned by block_block() (the
  // Block module adds the title in block_list() instead), so we look it up
  // manually, unless the title is overridden and does not use the %title
  // placeholder.
  if ($module == 'block') {
    $block->title = $info->title;
  }
  elseif (isset($block->subject)) {
    $block->title = $block->subject;
  }
  else {
    $block->title = NULL;
  }

  if (isset($block->subject)) {
    $block->title = $block->subject;
  }
  else {
    $block->title = NULL;
  }

  if (user_access('administer blocks')) {
    $block->admin_links = array(
      array(
        'title' => t('Configure block'),
        'href' => "admin/structure/block/manage/$module/$delta/configure",
        'query' => drupal_get_destination(),
      ),
    );
  }

  return $block;
}
