<?php

/**
 * Base class for export UI.
 */
class ctools_export_ui {
  var $plugin;
  var $name;
  var $options = array();

  /**
   * Fake constructor -- this is easier to deal with than the real
   * constructor because we are retaining PHP4 compatibility, which
   * would require all child classes to implement their own constructor.
   */
  public function init($plugin) {
    ctools_include('export');

    $this->plugin = $plugin;
  }

  /**
   * Get a page title for the current page from our plugin strings.
   */
  public function get_page_title($op, $item = NULL) {
    if (empty($this->plugin['strings']['title'][$op])) {
      return;
    }

    // Replace %title that might be there with the exportable title.
    $title = $this->plugin['strings']['title'][$op];
    if (!empty($item)) {
      $export_key = $this->plugin['export']['key'];
      $title = (str_replace('%title', check_plain($item->{$export_key}), $title));
    }

    return $title;
  }

  /**
   * Called by ctools_export_ui_load to load the item.
   *
   * This can be overridden for modules that want to be able to export
   * items currently being edited, for example.
   */
  public function load_item($item_name) {
    $item = ctools_export_crud_load($this->plugin['schema'], $item_name);
    return empty($item) ? FALSE : $item;
  }

  // ------------------------------------------------------------------------
  // Menu item manipulation.

  /**
   * hook_menu() entry point.
   *
   * Child implementations that need to add or modify menu items should
   * probably call parent::hook_menu($items) and then modify as needed.
   */
  public function hook_menu(&$items) {
    // During upgrades, the schema can be empty as this is called prior to
    // actual update functions being run. Ensure that we can cope with this
    // situation.
    if (empty($this->plugin['schema'])) {
      return;
    }

    $prefix = ctools_export_ui_plugin_base_path($this->plugin);

    if (isset($this->plugin['menu']['items']) && is_array($this->plugin['menu']['items'])) {
      $my_items = array();
      foreach ($this->plugin['menu']['items'] as $item) {
        // Add menu item defaults.
        $item += array(
          'file' => 'export-ui.inc',
          'file path' => drupal_get_path('module', 'ctools') . '/includes',
        );

        $path = !empty($item['path']) ? $prefix . '/' . $item['path'] : $prefix;
        unset($item['path']);
        $my_items[$path] = $item;
      }
      $items += $my_items;
    }
  }

  /**
   * Menu callback to determine if an operation is accessible.
   *
   * This function enforces a basic access check on the configured perm
   * string, and then additional checks as needed.
   *
   * @param $op
   *   The 'op' of the menu item, which is defined by 'allowed operations'
   *   and embedded into the arguments in the menu item.
   * @param $item
   *   If an op that works on an item, then the item object, otherwise NULL.
   *
   * @return
   *   TRUE if the current user has access, FALSE if not.
   */
  public function access($op, $item) {
    if (!user_access($this->plugin['access'])) {
      return FALSE;
    }

    // More fine-grained access control:
    if ($op == 'add' && !user_access($this->plugin['create access'])) {
      return FALSE;
    }

    // More fine-grained access control:
    if (($op == 'revert' || $op == 'delete') && !user_access($this->plugin['delete access'])) {
      return FALSE;
    }

    // If we need to do a token test, do it here.
    if (!empty($this->plugin['allowed operations'][$op]['token']) && (!isset($_GET['token']) || !drupal_valid_token($_GET['token'], $op))) {
      return FALSE;
    }

    switch ($op) {
      case 'import':
        return user_access('use ctools import');

      case 'revert':
        return ($item->export_type & EXPORT_IN_DATABASE) && ($item->export_type & EXPORT_IN_CODE);

      case 'delete':
        return ($item->export_type & EXPORT_IN_DATABASE) && !($item->export_type & EXPORT_IN_CODE);

      case 'disable':
        return empty($item->disabled);

      case 'enable':
        return !empty($item->disabled);

      default:
        return TRUE;
    }
  }

  // ------------------------------------------------------------------------
  // These methods are the API for generating the list of exportable items.

  /**
   * Master entry point for handling a list.
   *
   * It is unlikely that a child object will need to override this method,
   * unless the listing mechanism is going to be highly specialized.
   */
  public function list_page($js, $input) {
    $this->items = ctools_export_crud_load_all($this->plugin['schema'], $js);

    // Respond to a reset command by clearing session and doing a drupal goto
    // back to the base URL.
    if (isset($input['op']) && $input['op'] == t('Reset')) {
      unset($_SESSION['ctools_export_ui'][$this->plugin['name']]);
      if (!$js) {
        drupal_goto($_GET['q']);
      }
      // Clear everything but form id, form build id and form token.
      $keys = array_keys($input);
      foreach ($keys as $id) {
        if (!in_array($id, array('form_id', 'form_build_id', 'form_token'))) {
          unset($input[$id]);
        }
      }
      $replace_form = TRUE;
    }

    // If there is no input, check to see if we have stored input in the
    // session.
    if (!isset($input['form_id'])) {
      if (isset($_SESSION['ctools_export_ui'][$this->plugin['name']]) && is_array($_SESSION['ctools_export_ui'][$this->plugin['name']])) {
        $input  = $_SESSION['ctools_export_ui'][$this->plugin['name']];
      }
    }
    else {
      $_SESSION['ctools_export_ui'][$this->plugin['name']] = $input;
      unset($_SESSION['ctools_export_ui'][$this->plugin['name']]['q']);
    }

    // This is where the form will put the output.
    $this->rows = array();
    $this->sorts = array();

    $form_state = array(
      'plugin' => $this->plugin,
      'input' => $input,
      'rerender' => TRUE,
      'no_redirect' => TRUE,
      'object' => &$this,
    );
    if (!isset($form_state['input']['form_id'])) {
      $form_state['input']['form_id'] = 'ctools_export_ui_list_form';
    }

    // If we do any form rendering, it's to completely replace a form on the
    // page, so don't let it force our ids to change.
    if ($js && isset($_POST['ajax_html_ids'])) {
      unset($_POST['ajax_html_ids']);
    }

    $form = drupal_build_form('ctools_export_ui_list_form', $form_state);
    $form = drupal_render($form);

    $output = $this->list_header($form_state) . $this->list_render($form_state) . $this->list_footer($form_state);

    if (!$js) {
      $this->list_css();
      return $form . $output;
    }

    $commands = array();
    $commands[] = ajax_command_replace('#ctools-export-ui-list-items', $output);
    if (!empty($replace_form)) {
      $commands[] = ajax_command_replace('#ctools-export-ui-list-form', $form);
    }
    print ajax_render($commands);
    ajax_footer();
  }

  /**
   * Create the filter/sort form at the top of a list of exports.
   *
   * This handles the very default conditions, and most lists are expected
   * to override this and call through to parent::list_form() in order to
   * get the base form and then modify it as necessary to add search
   * gadgets for custom fields.
   */
  public function list_form(&$form, &$form_state) {
    // This forces the form to *always* treat as submitted which is
    // necessary to make it work.
    $form['#token'] = FALSE;
    if (empty($form_state['input'])) {
      $form["#post"] = TRUE;
    }

    // Add the 'q' in if we are not using clean URLs or it can get lost when
    // using this kind of form.
    if (!variable_get('clean_url', FALSE)) {
      $form['q'] = array(
        '#type' => 'hidden',
        '#value' => $_GET['q'],
      );
    }

    $all = array('all' => t('- All -'));

    $form['top row'] = array(
      '#prefix' => '<div class="ctools-export-ui-row ctools-export-ui-top-row clearfix">',
      '#suffix' => '</div>',
    );

    $form['bottom row'] = array(
      '#prefix' => '<div class="ctools-export-ui-row ctools-export-ui-bottom-row clearfix">',
      '#suffix' => '</div>',
    );

    $form['top row']['storage'] = array(
      '#type' => 'select',
      '#title' => t('Storage'),
      '#options' => $all + array(
        t('Normal') => t('Normal'),
        t('Default') => t('Default'),
        t('Overridden') => t('Overridden'),
      ),
      '#default_value' => 'all',
    );

    $form['top row']['disabled'] = array(
      '#type' => 'select',
      '#title' => t('Enabled'),
      '#options' => $all + array(
        '0' => t('Enabled'),
        '1' => t('Disabled'),
      ),
      '#default_value' => 'all',
    );

    $form['top row']['search'] = array(
      '#type' => 'textfield',
      '#title' => t('Search'),
    );

    $form['bottom row']['order'] = array(
      '#type' => 'select',
      '#title' => t('Sort by'),
      '#options' => $this->list_sort_options(),
      '#default_value' => 'disabled',
    );

    $form['bottom row']['sort'] = array(
      '#type' => 'select',
      '#title' => t('Order'),
      '#options' => array(
        'asc' => t('Up'),
        'desc' => t('Down'),
      ),
      '#default_value' => 'asc',
    );

    $form['bottom row']['submit'] = array(
      '#type' => 'submit',
      '#id' => 'ctools-export-ui-list-items-apply',
      '#value' => t('Apply'),
      '#attributes' => array('class' => array('use-ajax-submit ctools-auto-submit-click')),
    );

    $form['bottom row']['reset'] = array(
      '#type' => 'submit',
      '#id' => 'ctools-export-ui-list-items-apply',
      '#value' => t('Reset'),
      '#attributes' => array('class' => array('use-ajax-submit')),
    );

    $form['#prefix'] = '<div class="clearfix">';
    $form['#suffix'] = '</div>';
    $form['#attached']['js'] = array(ctools_attach_js('auto-submit'));
    $form['#attached']['library'][] = array('system', 'drupal.ajax');
    $form['#attached']['library'][] = array('system', 'jquery.form');
    $form['#attributes'] = array('class' => array('ctools-auto-submit-full-form'));
  }

  /**
   * Validate the filter/sort form.
   *
   * It is very rare that a filter form needs validation, but if it is
   * needed, override this.
   */
  public function list_form_validate(&$form, &$form_state) { }

  /**
   * Submit the filter/sort form.
   *
   * This submit handler is actually responsible for building up all of the
   * rows that will later be rendered, since it is doing the filtering and
   * sorting.
   *
   * For the most part, you should not need to override this method, as the
   * fiddly bits call through to other functions.
   */
  public function list_form_submit(&$form, &$form_state) {
    // Filter and re-sort the pages.
    $plugin = $this->plugin;

    $prefix = ctools_export_ui_plugin_base_path($plugin);

    foreach ($this->items as $name => $item) {
      // Call through to the filter and see if we're going to render this
      // row. If it returns TRUE, then this row is filtered out.
      if ($this->list_filter($form_state, $item)) {
        continue;
      }

      $operations = $this->build_operations($item);

      $this->list_build_row($item, $form_state, $operations);
    }

    // Now actually sort
    if ($form_state['values']['sort'] == 'desc') {
      arsort($this->sorts);
    }
    else {
      asort($this->sorts);
    }

    // Nuke the original.
    $rows = $this->rows;
    $this->rows = array();
    // And restore.
    foreach ($this->sorts as $name => $title) {
      $this->rows[$name] = $rows[$name];
    }
  }

  /**
   * Determine if a row should be filtered out.
   *
   * This handles the default filters for the export UI list form. If you
   * added additional filters in list_form() then this is where you should
   * handle them.
   *
   * @return
   *   TRUE if the item should be excluded.
   */
  public function list_filter($form_state, $item) {
    $schema = ctools_export_get_schema($this->plugin['schema']);
    if ($form_state['values']['storage'] != 'all' && $form_state['values']['storage'] != $item->{$schema['export']['export type string']}) {
      return TRUE;
    }

    if ($form_state['values']['disabled'] != 'all' && $form_state['values']['disabled'] != !empty($item->disabled)) {
      return TRUE;
    }

    if ($form_state['values']['search']) {
      $search = strtolower($form_state['values']['search']);
      foreach ($this->list_search_fields() as $field) {
        if (strpos(strtolower($item->$field), $search) !== FALSE) {
          $hit = TRUE;
          break;
        }
      }
      if (empty($hit)) {
        return TRUE;
      }
    }
  }

  /**
   * Provide a list of fields to test against for the default "search" widget.
   *
   * This widget will search against whatever fields are configured here. By
   * default it will attempt to search against the name, title and description fields.
   */
  public function list_search_fields() {
    $fields = array(
      $this->plugin['export']['key'],
    );

    if (!empty($this->plugin['export']['admin_title'])) {
      $fields[] = $this->plugin['export']['admin_title'];
    }
    if (!empty($this->plugin['export']['admin_description'])) {
      $fields[] = $this->plugin['export']['admin_description'];
    }

    return $fields;
  }

  /**
   * Provide a list of sort options.
   *
   * Override this if you wish to provide more or change how these work.
   * The actual handling of the sorting will happen in build_row().
   */
  public function list_sort_options() {
    if (!empty($this->plugin['export']['admin_title'])) {
      $options = array(
        'disabled' => t('Enabled, title'),
        $this->plugin['export']['admin_title'] => t('Title'),
      );
    }
    else {
      $options = array(
        'disabled' => t('Enabled, name'),
      );
    }

    $options += array(
      'name' => t('Name'),
      'storage' => t('Storage'),
    );

    return $options;
  }

  /**
   * Add listing CSS to the page.
   *
   * Override this if you need custom CSS for your list.
   */
  public function list_css() {
    ctools_add_css('export-ui-list');
  }

  /**
   * Builds the operation links for a specific exportable item.
   */
  public function build_operations($item) {
    $plugin = $this->plugin;
    $schema = ctools_export_get_schema($plugin['schema']);
    $operations = $plugin['allowed operations'];
    $operations['import'] = FALSE;

    if ($item->{$schema['export']['export type string']} == t('Normal')) {
      $operations['revert'] = FALSE;
    }
    elseif ($item->{$schema['export']['export type string']} == t('Overridden')) {
      $operations['delete'] = FALSE;
    }
    else {
      $operations['revert'] = FALSE;
      $operations['delete'] = FALSE;
    }
    if (empty($item->disabled)) {
      $operations['enable'] = FALSE;
    }
    else {
      $operations['disable'] = FALSE;
    }

    $allowed_operations = array();

    foreach ($operations as $op => $info) {
      if (!empty($info)) {
        $allowed_operations[$op] = array(
          'title' => $info['title'],
          'href' => ctools_export_ui_plugin_menu_path($plugin, $op, $item->{$this->plugin['export']['key']}),
        );
        if (!empty($info['ajax'])) {
          $allowed_operations[$op]['attributes'] = array('class' => array('use-ajax'));
        }
        if (!empty($info['token'])) {
          $allowed_operations[$op]['query'] = array('token' => drupal_get_token($op));
        }
      }
    }

    return $allowed_operations;
  }

  /**
   * Build a row based on the item.
   *
   * By default all of the rows are placed into a table by the render
   * method, so this is building up a row suitable for theme('table').
   * This doesn't have to be true if you override both.
   */
  public function list_build_row($item, &$form_state, $operations) {
    // Set up sorting
    $name = $item->{$this->plugin['export']['key']};
    $schema = ctools_export_get_schema($this->plugin['schema']);

    // Note: $item->{$schema['export']['export type string']} should have already been set up by export.inc so
    // we can use it safely.
    switch ($form_state['values']['order']) {
      case 'disabled':
        $this->sorts[$name] = empty($item->disabled) . $name;
        break;
      case 'title':
        $this->sorts[$name] = $item->{$this->plugin['export']['admin_title']};
        break;
      case 'name':
        $this->sorts[$name] = $name;
        break;
      case 'storage':
        $this->sorts[$name] = $item->{$schema['export']['export type string']} . $name;
        break;
    }

    $this->rows[$name]['data'] = array();
    $this->rows[$name]['class'] = !empty($item->disabled) ? array('ctools-export-ui-disabled') : array('ctools-export-ui-enabled');

    // If we have an admin title, make it the first row.
    if (!empty($this->plugin['export']['admin_title'])) {
      $this->rows[$name]['data'][] = array('data' => check_plain($item->{$this->plugin['export']['admin_title']}), 'class' => array('ctools-export-ui-title'));
    }
    $this->rows[$name]['data'][] = array('data' => check_plain($name), 'class' => array('ctools-export-ui-name'));
    $this->rows[$name]['data'][] = array('data' => check_plain($item->{$schema['export']['export type string']}), 'class' => array('ctools-export-ui-storage'));

    $ops = theme('links__ctools_dropbutton', array('links' => $operations, 'attributes' => array('class' => array('links', 'inline'))));

    $this->rows[$name]['data'][] = array('data' => $ops, 'class' => array('ctools-export-ui-operations'));

    // Add an automatic mouseover of the description if one exists.
    if (!empty($this->plugin['export']['admin_description'])) {
      $this->rows[$name]['title'] = $item->{$this->plugin['export']['admin_description']};
    }
  }

  /**
   * Provide the table header.
   *
   * If you've added columns via list_build_row() but are still using a
   * table, override this method to set up the table header.
   */
  public function list_table_header() {
    $header = array();
    if (!empty($this->plugin['export']['admin_title'])) {
      $header[] = array('data' => t('Title'), 'class' => array('ctools-export-ui-title'));
    }

    $header[] = array('data' => t('Name'), 'class' => array('ctools-export-ui-name'));
    $header[] = array('data' => t('Storage'), 'class' => array('ctools-export-ui-storage'));
    $header[] = array('data' => t('Operations'), 'class' => array('ctools-export-ui-operations'));

    return $header;
  }

  /**
   * Render all of the rows together.
   *
   * By default we place all of the rows in a table, and this should be the
   * way most lists will go.
   *
   * Whatever you do if this method is overridden, the ID is important for AJAX
   * so be sure it exists.
   */
  public function list_render(&$form_state) {
    $table = array(
      'header' => $this->list_table_header(),
      'rows' => $this->rows,
      'attributes' => array('id' => 'ctools-export-ui-list-items'),
      'empty' => $this->plugin['strings']['message']['no items'],
    );
    return theme('table', $table);
  }

  /**
   * Render a header to go before the list.
   *
   * This will appear after the filter/sort widgets.
   */
  public function list_header($form_state) { }

  /**
   * Render a footer to go after thie list.
   *
   * This is a good place to add additional links.
   */
  public function list_footer($form_state) { }

  // ------------------------------------------------------------------------
  // These methods are the API for adding/editing exportable items

  /**
   * Perform a drupal_goto() to the location provided by the plugin for the
   * operation.
   *
   * @param $op
   *   The operation to use. A string must exist in $this->plugin['redirect']
   *   for this operation.
   * @param $item
   *   The item in use; this may be necessary as item IDs are often embedded in
   *   redirects.
   */
  public function redirect($op, $item = NULL) {
    if (isset($this->plugin['redirect'][$op])) {
      $destination = (array) $this->plugin['redirect'][$op];
      if ($item) {
        $export_key = $this->plugin['export']['key'];
        $destination[0] = str_replace('%ctools_export_ui', $item->{$export_key}, $destination[0]);
      }
      call_user_func_array('drupal_goto', $destination);
    }
    else {
      // If the operation isn't set, fall back to the plugin's base path.
      drupal_goto(ctools_export_ui_plugin_base_path($this->plugin));
    }
  }

  public function add_page($js, $input, $step = NULL) {
    drupal_set_title($this->get_page_title('add'), PASS_THROUGH);

    // If a step not set, they are trying to create a new item. If a step
    // is set, they're in the process of creating an item.
    if (!empty($this->plugin['use wizard']) && !empty($step)) {
      $item = $this->edit_cache_get(NULL, 'add');
    }
    if (empty($item)) {
      $item = ctools_export_crud_new($this->plugin['schema']);
    }

    $form_state = array(
      'plugin' => $this->plugin,
      'object' => &$this,
      'ajax' => $js,
      'item' => $item,
      'op' => 'add',
      'form type' => 'add',
      'rerender' => TRUE,
      'no_redirect' => TRUE,
      'step' => $step,
      // Store these in case additional args are needed.
      'function args' => func_get_args(),
    );

    $output = $this->edit_execute_form($form_state);
    if (!empty($form_state['executed']) && empty($form_state['rebuild'])) {
      $this->redirect($form_state['op'], $form_state['item']);
    }

    return $output;
  }

  /**
   * Main entry point to edit an item.
   */
  public function edit_page($js, $input, $item, $step = NULL) {
    drupal_set_title($this->get_page_title('edit', $item), PASS_THROUGH);

    // Check to see if there is a cached item to get if we're using the wizard.
    if (!empty($this->plugin['use wizard'])) {
      $cached = $this->edit_cache_get($item, 'edit');
      if (!empty($cached)) {
        $item = $cached;
      }
    }

    $form_state = array(
      'plugin' => $this->plugin,
      'object' => &$this,
      'ajax' => $js,
      'item' => $item,
      'op' => 'edit',
      'form type' => 'edit',
      'rerender' => TRUE,
      'no_redirect' => TRUE,
      'step' => $step,
      // Store these in case additional args are needed.
      'function args' => func_get_args(),
    );

    $output = $this->edit_execute_form($form_state);
    if (!empty($form_state['executed']) && empty($form_state['rebuild'])) {
      $this->redirect($form_state['op'], $form_state['item']);
    }

    return $output;
  }

  /**
   * Main entry point to clone an item.
   */
  public function clone_page($js, $input, $original, $step = NULL) {
    drupal_set_title($this->get_page_title('clone', $original), PASS_THROUGH);

    // If a step not set, they are trying to create a new clone. If a step
    // is set, they're in the process of cloning an item.
    if (!empty($this->plugin['use wizard']) && !empty($step)) {
      $item = $this->edit_cache_get(NULL, 'clone');
    }
    if (empty($item)) {
      // To make a clone of an item, we first export it and then re-import it.
      // Export the handler, which is a fantastic way to clean database IDs out of it.
      $export = ctools_export_crud_export($this->plugin['schema'], $original);
      $item = ctools_export_crud_import($this->plugin['schema'], $export);

      if (!empty($input[$this->plugin['export']['key']])) {
        $item->{$this->plugin['export']['key']} = $input[$this->plugin['export']['key']];
      }
      else {
        $item->{$this->plugin['export']['key']} = 'clone_of_' . $item->{$this->plugin['export']['key']};
      }
    }

    // Tabs and breadcrumb disappearing, this helps alleviate through cheating.
    // ...not sure this this is the best way.
    $trail = menu_set_active_item(ctools_export_ui_plugin_base_path($this->plugin));

    $name = $original->{$this->plugin['export']['key']};

    $form_state = array(
      'plugin' => $this->plugin,
      'object' => &$this,
      'ajax' => $js,
      'item' => $item,
      'op' => 'add',
      'form type' => 'clone',
      'original name' => $name,
      'rerender' => TRUE,
      'no_redirect' => TRUE,
      'step' => $step,
      // Store these in case additional args are needed.
      'function args' => func_get_args(),
    );

    $output = $this->edit_execute_form($form_state);
    if (!empty($form_state['executed']) && empty($form_state['rebuild'])) {
      $this->redirect($form_state['op'], $form_state['item']);
    }

    return $output;
  }

  /**
   * Execute the form.
   *
   * Add and Edit both funnel into this, but they have a few different
   * settings.
   */
  public function edit_execute_form(&$form_state) {
    if (!empty($this->plugin['use wizard'])) {
      return $this->edit_execute_form_wizard($form_state);
    }
    else {
      return $this->edit_execute_form_standard($form_state);
    }
  }

  /**
   * Execute the standard form for editing.
   *
   * By default, export UI will provide a single form for editing an object.
   */
  public function edit_execute_form_standard(&$form_state) {
    $output = drupal_build_form('ctools_export_ui_edit_item_form', $form_state);
    if (!empty($form_state['executed']) && empty($form_state['rebuild'])) {
      $this->edit_save_form($form_state);
    }

    return $output;
  }

  /**
   * Get the form info for the wizard.
   *
   * This gets the form info out of the plugin, then adds defaults based on
   * how we want edit forms to work.
   *
   * Overriding this can allow child UIs to tweak this info for specialized
   * wizards.
   *
   * @param array $form_state
   *   The already created form state.
   */
  public function get_wizard_info(&$form_state) {
    if (!isset($form_state['step'])) {
      $form_state['step'] = NULL;
    }

    $export_key = $this->plugin['export']['key'];

    // When cloning, the name of the item being cloned is referenced in the
    // path, not the name of this item.
    if ($form_state['form type'] == 'clone') {
      $name = $form_state['original name'];
    }
    else {
      $name = $form_state['item']->{$export_key};
    }

    $form_info = !empty($this->plugin['form info']) ? $this->plugin['form info'] : array();
    $form_info += array(
      'id' => 'ctools_export_ui_edit',
      'path' => ctools_export_ui_plugin_menu_path($this->plugin, $form_state['form type'], $name) . '/%step',
      'show trail' => TRUE,
      'free trail' => TRUE,
      'show back' => $form_state['form type'] == 'add',
      'show return' => FALSE,
      'show cancel' => TRUE,
      'finish callback' => 'ctools_export_ui_wizard_finish',
      'next callback' => 'ctools_export_ui_wizard_next',
      'back callback' => 'ctools_export_ui_wizard_back',
      'cancel callback' => 'ctools_export_ui_wizard_cancel',
      'order' => array(),
      'import order' => array(
        'import' => t('Import code'),
        'settings' => t('Settings'),
      ),
    );

    // Set the order of forms based on the op if we have a specific one.
    if (isset($form_info[$form_state['form type'] . ' order'])) {
      $form_info['order'] = $form_info[$form_state['form type'] . ' order'];
    }

    // We have generic fallback forms we can use if they are not specified,
    // and they automatically delegate back to the UI object. Use these if
    // not specified.
    foreach ($form_info['order'] as $key => $title) {
      if (empty($form_info['forms'][$key])) {
        $form_info['forms'][$key] = array(
          'form id' => 'ctools_export_ui_edit_item_wizard_form',
        );
      }
    }

    // 'free trail' means the wizard can freely go back and form from item
    // via the trail and not with next/back buttons.
    if ($form_state['form type'] == 'add' || ($form_state['form type'] == 'import' && empty($form_state['item']->{$export_key}))) {
      $form_info['free trail'] = FALSE;
    }

    return $form_info;
  }

  /**
   * Execute the wizard for editing.
   *
   * For complex objects, sometimes a wizard is needed. This is normally
   * activated by setting 'use wizard' => TRUE in the plugin definition
   * and then creating a 'form info' array to feed the wizard the data
   * it needs.
   *
   * When creating this wizard, the plugin is responsible for defining all forms
   * that will be utilized with the wizard.
   *
   * Using 'add order' or 'edit order' can be used to ensure that add/edit order
   * is different.
   */
  public function edit_execute_form_wizard(&$form_state) {
    $form_info = $this->get_wizard_info($form_state);

    // If there aren't any forms set, fail.
    if (empty($form_info['order'])) {
      return MENU_NOT_FOUND;
    }

    // Figure out if this is a new instance of the wizard
    if (empty($form_state['step'])) {
      $order = array_keys($form_info['order']);
      $form_state['step'] = reset($order);
    }

    if (empty($form_info['order'][$form_state['step']]) && empty($form_info['forms'][$form_state['step']])) {
      return MENU_NOT_FOUND;
    }

    ctools_include('wizard');
    $output = ctools_wizard_multistep_form($form_info, $form_state['step'], $form_state);
    if (!empty($form_state['complete'])) {
      $this->edit_save_form($form_state);
    }
    elseif ($output && !empty($form_state['item']->export_ui_item_is_cached)) {
      // @todo this should be in the plugin strings
      drupal_set_message(t('You have unsaved changes. These changes will not be made permanent until you click <em>Save</em>.'), 'warning');
    }

    // Unset the executed flag if any non-wizard button was pressed. Those
    // buttons require special handling by whatever client is operating them.
    if (!empty($form_state['executed']) && empty($form_state['clicked_button']['#wizard type'])) {
      unset($form_state['executed']);
    }
    return $output;
  }

  /**
   * Wizard 'back' callback when using a wizard to edit an item.
   *
   * The wizard callback delegates this back to the object.
   */
  public function edit_wizard_back(&$form_state) {
    // This only exists so child implementations can use it.
  }

  /**
   * Wizard 'next' callback when using a wizard to edit an item.
   *
   * The wizard callback delegates this back to the object.
   */
  public function edit_wizard_next(&$form_state) {
    $this->edit_cache_set($form_state['item'], $form_state['form type']);
  }

  /**
   * Wizard 'cancel' callback when using a wizard to edit an item.
   *
   * The wizard callback delegates this back to the object.
   */
  public function edit_wizard_cancel(&$form_state) {
    $this->edit_cache_clear($form_state['item'], $form_state['form type']);
  }

  /**
   * Wizard 'cancel' callback when using a wizard to edit an item.
   *
   * The wizard callback delegates this back to the object.
   */
  public function edit_wizard_finish(&$form_state) {
    $form_state['complete'] = TRUE;

    // If we are importing, and overwrite was selected, delete the original so
    // that this one writes properly.
    if ($form_state['form type'] == 'import' && !empty($form_state['item']->export_ui_allow_overwrite)) {
      ctools_export_crud_delete($this->plugin['schema'], $form_state['item']);
    }

    $this->edit_cache_clear($form_state['item'], $form_state['form type']);
  }

  /**
   * Retrieve the item currently being edited from the object cache.
   */
  public function edit_cache_get($item, $op = 'edit') {
    ctools_include('object-cache');
    if (is_string($item)) {
      $name = $item;
    }
    else {
      $name = $this->edit_cache_get_key($item, $op);
    }

    $cache = ctools_object_cache_get('ctui_' . $this->plugin['name'], $name);
    if ($cache) {
      $cache->export_ui_item_is_cached = TRUE;
      return $cache;
    }
  }

  /**
   * Cache the item currently currently being edited.
   */
  public function edit_cache_set($item, $op = 'edit') {
    ctools_include('object-cache');
    $name = $this->edit_cache_get_key($item, $op);
    return $this->edit_cache_set_key($item, $name);
  }

  public function edit_cache_set_key($item, $name) {
    return ctools_object_cache_set('ctui_' . $this->plugin['name'], $name, $item);
  }

  /**
   * Clear the object cache for the currently edited item.
   */
  public function edit_cache_clear($item, $op = 'edit') {
    ctools_include('object-cache');
    $name = $this->edit_cache_get_key($item, $op);
    return ctools_object_cache_clear('ctui_' . $this->plugin['name'], $name);
  }

  /**
   * Figure out what the cache key is for this object.
   */
  public function edit_cache_get_key($item, $op) {
    $export_key = $this->plugin['export']['key'];
    return $op == 'edit' ? $item->{$this->plugin['export']['key']} : "::$op";
  }

  /**
   * Called to save the final product from the edit form.
   */
  public function edit_save_form($form_state) {
    $item = &$form_state['item'];
    $export_key = $this->plugin['export']['key'];

    $result = ctools_export_crud_save($this->plugin['schema'], $item);

    if ($result) {
      $message = str_replace('%title', check_plain($item->{$export_key}), $this->plugin['strings']['confirmation'][$form_state['op']]['success']);
      drupal_set_message($message);
    }
    else {
      $message = str_replace('%title', check_plain($item->{$export_key}), $this->plugin['strings']['confirmation'][$form_state['op']]['fail']);
      drupal_set_message($message, 'error');
    }
  }

  /**
   * Provide the actual editing form.
   */
  public function edit_form(&$form, &$form_state) {
    $export_key = $this->plugin['export']['key'];
    $item = $form_state['item'];
    $schema = ctools_export_get_schema($this->plugin['schema']);

    if (!empty($this->plugin['export']['admin_title'])) {
      $form['info'][$this->plugin['export']['admin_title']] = array(
        '#type' => 'textfield',
        '#title' => t('Administrative title'),
        '#description' => t('This will appear in the administrative interface to easily identify it.'),
        '#default_value' => $item->{$this->plugin['export']['admin_title']},
      );
    }

    $form['info'][$export_key] = array(
      '#title' => t($schema['export']['key name']),
      '#type' => 'textfield',
      '#default_value' => $item->{$export_key},
      '#description' => t('The unique ID for this @export.', array('@export' => $this->plugin['title singular'])),
      '#required' => TRUE,
      '#maxlength' => 255,
    );

    if (!empty($this->plugin['export']['admin_title'])) {
      $form['info'][$export_key]['#type'] = 'machine_name';
      $form['info'][$export_key]['#machine_name'] = array(
        'exists' => 'ctools_export_ui_edit_name_exists',
        'source' => array('info', $this->plugin['export']['admin_title']),
      );
    }

    if ($form_state['op'] === 'edit') {
      $form['info'][$export_key]['#disabled'] = TRUE;
      $form['info'][$export_key]['#value'] = $item->{$export_key};
    }

    if (!empty($this->plugin['export']['admin_description'])) {
      $form['info'][$this->plugin['export']['admin_description']] = array(
        '#type' => 'textarea',
        '#title' => t('Administrative description'),
        '#default_value' => $item->{$this->plugin['export']['admin_description']},
      );
    }

    // Add plugin's form definitions.
    if (!empty($this->plugin['form']['settings'])) {
      // Pass $form by reference.
      $this->plugin['form']['settings']($form, $form_state);
    }

    // Add the buttons if the wizard is not in use.
    if (empty($form_state['form_info'])) {
      // Make sure that whatever happens, the buttons go to the bottom.
      $form['buttons']['#weight'] = 100;

      // Add buttons.
      $form['buttons']['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Save'),
      );

      $form['buttons']['delete'] = array(
        '#type' => 'submit',
        '#value' => $item->export_type & EXPORT_IN_CODE ? t('Revert') : t('Delete'),
        '#access' => $form_state['op'] === 'edit' && $item->export_type & EXPORT_IN_DATABASE,
        '#submit' => array('ctools_export_ui_edit_item_form_delete'),
      );
    }
  }

  /**
   * Validate callback for the edit form.
   */
  public function edit_form_validate(&$form, &$form_state) {
    if (!empty($this->plugin['form']['validate'])) {
      // Pass $form by reference.
      $this->plugin['form']['validate']($form, $form_state);
    }
  }

  /**
   * Perform a final validation check before allowing the form to be
   * finished.
   */
  public function edit_finish_validate(&$form, &$form_state) {
    if ($form_state['op'] != 'edit') {
      // Validate the export key. Fake an element for form_error().
      $export_key = $this->plugin['export']['key'];
      $element = array(
        '#value' => $form_state['item']->{$export_key},
        '#parents' => array($export_key),
      );
      $form_state['plugin'] = $this->plugin;
      ctools_export_ui_edit_name_validate($element, $form_state);
    }
  }

  /**
   * Handle the submission of the edit form.
   *
   * At this point, submission is successful. Our only responsibility is
   * to copy anything out of values onto the item that we are able to edit.
   *
   * If the keys all match up to the schema, this method will not need to be
   * overridden.
   */
  public function edit_form_submit(&$form, &$form_state) {
    if (!empty($this->plugin['form']['submit'])) {
      // Pass $form by reference.
      $this->plugin['form']['submit']($form, $form_state);
    }

    // Transfer data from the form to the $item based upon schema values.
    $schema = ctools_export_get_schema($this->plugin['schema']);
    foreach (array_keys($schema['fields']) as $key) {
      if(isset($form_state['values'][$key])) {
        $form_state['item']->{$key} = $form_state['values'][$key];
      }
    }
  }

  // ------------------------------------------------------------------------
  // These methods are the API for 'other' stuff with exportables such as
  // enable, disable, import, export, delete

  /**
   * Callback to enable a page.
   */
  public function enable_page($js, $input, $item) {
    return $this->set_item_state(FALSE, $js, $input, $item);
  }

  /**
   * Callback to disable a page.
   */
  public function disable_page($js, $input, $item) {
    return $this->set_item_state(TRUE, $js, $input, $item);
  }

  /**
   * Set an item's state to enabled or disabled and output to user.
   *
   * If javascript is in use, this will rebuild the list and send that back
   * as though the filter form had been executed.
   */
  public function set_item_state($state, $js, $input, $item) {
    ctools_export_crud_set_status($this->plugin['schema'], $item, $state);

    if (!$js) {
      drupal_goto(ctools_export_ui_plugin_base_path($this->plugin));
    }
    else {
      return $this->list_page($js, $input);
    }
  }

  /**
   * Page callback to delete an exportable item.
   */
  public function delete_page($js, $input, $item) {
    $form_state = array(
      'plugin' => $this->plugin,
      'object' => &$this,
      'ajax' => $js,
      'item' => $item,
      'op' => $item->export_type & EXPORT_IN_CODE ? 'revert' : 'delete',
      'rerender' => TRUE,
      'no_redirect' => TRUE,
    );

    $output = drupal_build_form('ctools_export_ui_delete_confirm_form', $form_state);
    if (!empty($form_state['executed'])) {
      $this->delete_form_submit($form_state);
      $this->redirect($form_state['op'], $item);
    }

    return $output;
  }

  /**
   * Deletes exportable items from the database.
   */
  public function delete_form_submit(&$form_state) {
    $item = $form_state['item'];

    ctools_export_crud_delete($this->plugin['schema'], $item);
    $export_key = $this->plugin['export']['key'];
    $message = str_replace('%title', check_plain($item->{$export_key}), $this->plugin['strings']['confirmation'][$form_state['op']]['success']);
    drupal_set_message($message);
  }

  /**
   * Page callback to display export information for an exportable item.
   */
  public function export_page($js, $input, $item) {
    drupal_set_title($this->get_page_title('export', $item), PASS_THROUGH);
    return drupal_get_form('ctools_export_form', ctools_export_crud_export($this->plugin['schema'], $item), t('Export'));
  }

  /**
   * Page callback to import information for an exportable item.
   */
  public function import_page($js, $input, $step = NULL) {
    drupal_set_title($this->get_page_title('import'), PASS_THROUGH);
    // Import is basically a multi step wizard form, so let's go ahead and
    // use CTools' wizard.inc for it.

    // If a step not set, they are trying to create a new item. If a step
    // is set, they're in the process of creating an item.
    if (!empty($step)) {
      $item = $this->edit_cache_get(NULL, 'import');
    }
    if (empty($item)) {
      $item = ctools_export_crud_new($this->plugin['schema']);
    }

    $form_state = array(
      'plugin' => $this->plugin,
      'object' => &$this,
      'ajax' => $js,
      'item' => $item,
      'op' => 'add',
      'form type' => 'import',
      'rerender' => TRUE,
      'no_redirect' => TRUE,
      'step' => $step,
      // Store these in case additional args are needed.
      'function args' => func_get_args(),
    );

    // import always uses the wizard.
    $output = $this->edit_execute_form_wizard($form_state);
    if (!empty($form_state['executed'])) {
      $this->redirect($form_state['op'], $form_state['item']);
    }

    return $output;
  }

  /**
   * Import form. Provides simple helptext instructions and textarea for
   * pasting a export definition.
   */
  public function edit_form_import(&$form, &$form_state) {
    $form['help'] = array(
      '#type' => 'item',
      '#value' => $this->plugin['strings']['help']['import'],
    );

    $form['import'] = array(
      '#title' => t('@plugin code', array('@plugin' => $this->plugin['title singular proper'])),
      '#type' => 'textarea',
      '#rows' => 10,
      '#required' => TRUE,
      '#default_value' => !empty($form_state['item']->export_ui_code) ? $form_state['item']->export_ui_code : '',
    );

    $form['overwrite'] = array(
      '#title' => t('Allow import to overwrite an existing record.'),
      '#type' => 'checkbox',
      '#default_value' => !empty($form_state['item']->export_ui_allow_overwrite) ? $form_state['item']->export_ui_allow_overwrite : FALSE,
    );
  }

  /**
   * Import form validate handler.
   *
   * Evaluates code and make sure it creates an object before we continue.
   */
  public function edit_form_import_validate($form, &$form_state) {
    $item = ctools_export_crud_import($this->plugin['schema'], $form_state['values']['import']);
    if (is_string($item)) {
      form_error($form['import'], t('Unable to get an import from the code. Errors reported: @errors', array('@errors' => $item)));
      return;
    }

    $form_state['item'] = $item;
    $form_state['item']->export_ui_allow_overwrite = $form_state['values']['overwrite'];
    $form_state['item']->export_ui_code = $form_state['values']['import'];
  }

  /**
   * Submit callback for import form.
   *
   * Stores the item in the session.
   */
  public function edit_form_import_submit($form, &$form_state) {
    // The validate function already imported and stored the item. This
    // function exists simply to prevent it from going to the default
    // edit_form_submit() method.
  }
}

// -----------------------------------------------------------------------
// Forms to be used with this class.
//
// Since Drupal's forms are completely procedural, these forms will
// mostly just be pass-throughs back to the object.

/**
 * Add all appropriate includes to forms so that caching the form
 * still loads the files that we need.
 */
function _ctools_export_ui_add_form_files($form, &$form_state) {
  ctools_form_include($form_state, 'export');
  ctools_form_include($form_state, 'export-ui');

  // Also make sure the plugin .inc file is loaded.
  ctools_form_include_file($form_state, $form_state['object']->plugin['path'] . '/' . $form_state['object']->plugin['file']);
}

/**
 * Form callback to handle the filter/sort form when listing items.
 *
 * This simply loads the object defined in the plugin and hands it off.
 */
function ctools_export_ui_list_form($form, &$form_state) {
  $form_state['object']->list_form($form, $form_state);
  return $form;
}

/**
 * Validate handler for ctools_export_ui_list_form.
 */
function ctools_export_ui_list_form_validate(&$form, &$form_state) {
  $form_state['object']->list_form_validate($form, $form_state);
}

/**
 * Submit handler for ctools_export_ui_list_form.
 */
function ctools_export_ui_list_form_submit(&$form, &$form_state) {
  $form_state['object']->list_form_submit($form, $form_state);
}

/**
 * Form callback to edit an exportable item.
 *
 * This simply loads the object defined in the plugin and hands it off.
 */
function ctools_export_ui_edit_item_form($form, &$form_state) {
  // When called using #ajax via ajax_form_callback(), 'export' may
  // not be included so include it here.
  _ctools_export_ui_add_form_files($form, $form_state);

  $form_state['object']->edit_form($form, $form_state);
  return $form;
}

/**
 * Validate handler for ctools_export_ui_edit_item_form.
 */
function ctools_export_ui_edit_item_form_validate(&$form, &$form_state) {
  $form_state['object']->edit_form_validate($form, $form_state);
}

/**
 * Submit handler for ctools_export_ui_edit_item_form.
 */
function ctools_export_ui_edit_item_form_submit(&$form, &$form_state) {
  $form_state['object']->edit_form_submit($form, $form_state);
}

/**
 * Submit handler to delete for ctools_export_ui_edit_item_form
 *
 * @todo Put this on a callback in the object.
 */
function ctools_export_ui_edit_item_form_delete(&$form, &$form_state) {
  _ctools_export_ui_add_form_files($form, $form_state);

  $export_key = $form_state['plugin']['export']['key'];
  $path = $form_state['item']->export_type & EXPORT_IN_CODE ? 'revert' : 'delete';

  drupal_goto(ctools_export_ui_plugin_menu_path($form_state['plugin'], $path, $form_state['item']->{$export_key}), array('cancel_path' => $_GET['q']));
}

/**
 * Validate that an export item name is acceptable and unique during add.
 */
function ctools_export_ui_edit_name_validate($element, &$form_state) {
  $plugin = $form_state['plugin'];
  // Check for string identifier sanity
  if (!preg_match('!^[a-z0-9_]+$!', $element['#value'])) {
    form_error($element, t('The export id can only consist of lowercase letters, underscores, and numbers.'));
    return;
  }

  // Check for name collision
  if (empty($form_state['item']->export_ui_allow_overwrite) && $exists = ctools_export_crud_load($plugin['schema'], $element['#value'])) {
    form_error($element, t('A @plugin with this name already exists. Please choose another name or delete the existing item before creating a new one.', array('@plugin' => $plugin['title singular'])));
  }
}

/**
 * Test for #machine_name type to see if an export exists.
 */
function ctools_export_ui_edit_name_exists($name, $element, &$form_state) {
  $plugin = $form_state['plugin'];

  return (empty($form_state['item']->export_ui_allow_overwrite) && ctools_export_crud_load($plugin['schema'], $name));
}

/**
 * Delete/Revert confirm form.
 *
 * @todo -- call back into the object instead.
 */
function ctools_export_ui_delete_confirm_form($form, &$form_state) {
  _ctools_export_ui_add_form_files($form, $form_state);

  $plugin = $form_state['plugin'];
  $item = $form_state['item'];

  $form = array();

  $export_key = $plugin['export']['key'];
  $question = str_replace('%title', check_plain($item->{$export_key}), $plugin['strings']['confirmation'][$form_state['op']]['question']);
  $path = (!empty($_REQUEST['cancel_path']) && !url_is_external($_REQUEST['cancel_path'])) ? $_REQUEST['cancel_path'] : ctools_export_ui_plugin_base_path($plugin);

  $form = confirm_form($form,
    $question,
    $path,
    $plugin['strings']['confirmation'][$form_state['op']]['information'],
    $plugin['allowed operations'][$form_state['op']]['title'], t('Cancel')
  );
  return $form;
}

// --------------------------------------------------------------------------
// Forms and callbacks for using the edit system with the wizard.

/**
 * Form callback to edit an exportable item using the wizard
 *
 * This simply loads the object defined in the plugin and hands it off.
 */
function ctools_export_ui_edit_item_wizard_form($form, &$form_state) {
  _ctools_export_ui_add_form_files($form, $form_state);

  $method = 'edit_form_' . $form_state['step'];
  if (!method_exists($form_state['object'], $method)) {
    $method = 'edit_form';
  }

  $form_state['object']->$method($form, $form_state);
  return $form;
}

/**
 * Validate handler for ctools_export_ui_edit_item_wizard_form.
 */
function ctools_export_ui_edit_item_wizard_form_validate(&$form, &$form_state) {
  $method = 'edit_form_' . $form_state['step'] . '_validate';
  if (!method_exists($form_state['object'], $method)) {
    $method = 'edit_form_validate';
  }

  $form_state['object']->$method($form, $form_state);

  // Additionally, if there were no errors from that, and we're finishing,
  // perform a final validate to make sure everything is ok.
  if (isset($form_state['clicked_button']['#wizard type']) && $form_state['clicked_button']['#wizard type'] == 'finish' && !form_get_errors()) {
    $form_state['object']->edit_finish_validate($form, $form_state);
  }
}

/**
 * Submit handler for ctools_export_ui_edit_item_wizard_form.
 */
function ctools_export_ui_edit_item_wizard_form_submit(&$form, &$form_state) {
  $method = 'edit_form_' . $form_state['step'] . '_submit';
  if (!method_exists($form_state['object'], $method)) {
    $method = 'edit_form_submit';
  }

  $form_state['object']->$method($form, $form_state);
}

/**
 * Wizard 'back' callback when using a wizard to edit an item.
 */
function ctools_export_ui_wizard_back(&$form_state) {
  $form_state['object']->edit_wizard_back($form_state);
}

/**
 * Wizard 'next' callback when using a wizard to edit an item.
 */
function ctools_export_ui_wizard_next(&$form_state) {
  $form_state['object']->edit_wizard_next($form_state);
}

/**
 * Wizard 'cancel' callback when using a wizard to edit an item.
 */
function ctools_export_ui_wizard_cancel(&$form_state) {
  $form_state['object']->edit_wizard_cancel($form_state);
}

/**
 * Wizard 'finish' callback when using a wizard to edit an item.
 */
function ctools_export_ui_wizard_finish(&$form_state) {
  $form_state['object']->edit_wizard_finish($form_state);
}
