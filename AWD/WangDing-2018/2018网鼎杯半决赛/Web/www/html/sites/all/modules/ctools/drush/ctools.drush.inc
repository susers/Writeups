<?php

/**
 * @file
 * CTools Drush commands.
 */

/**
 * Implements hook_drush_command().
 */
function ctools_drush_command() {
  $items = array();

  $module_text = 'Filter the list of exportables by module. This will come from the \'export_module\' key on the exportable.';
  $all_text = 'Perform this operation all CTools exportables available on the system (all tables).';

  $items['ctools-export'] = array(
    'aliases' => array('ctex'),
    'callback' => 'ctools_drush_export',
    'description' => 'Export multiple CTools exportable objects directly to code.',
    'arguments' => array(
      'module'    => 'Name of your module.',
    ),
    'options' => array(
      'subdir' => 'The name of the sub directory to create the module in. Defaults to ctools_export which will be placed into sites/all/modules.',
      'remove' => 'Remove existing files before writing, except the .module file.',
      'filter' => 'Filter the list of exportables by status. Available options are enabled, disabled, overridden, database, code and all. Defaults to enabled.',
      'tables' => 'Comma separated list of exportable table names to filter by.',
    ),
    'examples' => array(
      'drush ctex export_module' => 'Export CTools exportables to a module called "export_module".',
      'drush ctex export_module --subdir=exports' => 'Same as above, but into the sites/all/modules/exports directory.',
      'drush ctex export_module --subdir=exports --remove' => 'Same as above, but automatically removing all files, except for the .module file.',
      'drush ctex --filter="views_view"' => 'Filter export selection to the views_view table only.',
    ),
  );

  $items['ctools-export-info'] = array(
    'aliases' => array('ctei'),
    'callback' => 'ctools_drush_export_info',
    'description' => 'Show available CTools exportable objects.',
    'arguments' => array(),
    'options' => array(
      'format' => 'Display exportables info in a different format such as print_r, json, export. The default is to show in a tabular format.',
      'tables-only' => 'Only show list of exportable types/table names and not available objects.',
      'filter' => 'Filter the list of exportables by status. Available options are enabled, disabled, overridden, database, and code.',
      'module' => $module_text,
    ),
    'examples' => array(
      'drush ctools-export-info' => 'View export info on all exportables.',
      'drush ctools-export-info views_view variable' => 'View export info for views_view and variable exportable types only.',
      'drush ctei --filter=enabled' => 'Show all enabled exportables.',
      'drush ctei views_view --filter=disabled' => 'Show all enabled exportables.',
      'drush ctei views_view --module=node' => 'Show all exportables provided by/on behalf of the node module.',
    ),
  );

  $items['ctools-export-view'] = array(
    'aliases' => array('ctev'),
    'callback' => 'ctools_drush_export_op_command',
    'description' => 'View CTools exportable object code output.',
    'arguments' => array(
      'table name' => 'Base table of the exportable you want to view.',
      'machine names' => 'Space separated list of exportables you want to view.',
    ),
    'options' => array(
      'indent' => 'The string to use for indentation when displaying the exportable export code. Defaults to \'\'.',
      'no-colour' => 'Remove any colour formatting from export string output. Ideal if you are sending the output of this command to a file.',
      'module' => $module_text,
      'all' => $all_text,
    ),
    'examples' => array(
      'drush ctools-export-view views_view' => 'View all views exportable objects.',
      'drush ctools-export-view views_view archive' => 'View default views archive view.',
    ),
  );

  $items['ctools-export-revert'] = array(
    'aliases' => array('cter'),
    'callback' => 'ctools_drush_export_op_command',
    'description' => 'Revert CTools exportables from changes overridden in the database.',
    'arguments' => array(
      'table name' => 'Base table of the exportable you want to revert.',
      'machine names' => 'Space separated list of exportables you want to revert.',
    ),
    'options' => array(
      'module' => $module_text,
      'all' => $all_text,
    ),
    'examples' => array(
      'drush ctools-export-revert views_view' => 'Revert all overridden views exportable objects.',
      'drush ctools-export-revert views_view archive' => 'Revert overridden default views archive view.',
      'drush ctools-export-revert --all' => 'Revert all exportables on the system.',
    ),
  );

  $items['ctools-export-enable'] = array(
    'aliases' => array('ctee'),
    'callback' => 'ctools_drush_export_op_command',
    'description' => 'Enable CTools exportables.',
    'arguments' => array(
      'table name' => 'Base table of the exportable you want to enable.',
      'machine names' => 'Space separated list of exportables you want to enable.',
    ),
    'options' => array(
      'module' => $module_text,
      'all' => $all_text,
    ),
    'examples' => array(
      'drush ctools-export-enable views_view' => 'Enable all overridden views exportable objects.',
      'drush ctools-export-enable views_view archive' => 'Enable overridden default views archive view.',
    ),
  );

  $items['ctools-export-disable'] = array(
    'aliases' => array('cted'),
    'callback' => 'ctools_drush_export_op_command',
    'description' => 'Disable CTools exportables.',
    'arguments' => array(
      'table name' => 'Base table of the exportable you want to disable.',
      'machine names' => 'Space separated list of exportables you want to disable.',
    ),
    'options' => array(
      'module' => $module_text,
      'all' => $all_text,
    ),
    'examples' => array(
      'drush ctools-export-disable views_view' => 'Disable all overridden views exportable objects.',
      'drush ctools-export-disable views_view archive' => 'Disable overridden default views archive view.',
    ),
  );

  return $items;
}

/**
 * Implementation of hook_drush_help().
 */
function ctools_drush_help($section) {
  switch ($section) {
    case 'meta:ctools:title':
      return dt('CTools commands');

    case 'meta:entity:summary':
      return dt('CTools drush commands.');
  }
}

/**
 * Drush callback: export.
 */
function ctools_drush_export($module = 'foo') {
  $error = FALSE;
  if (preg_match('@[^a-z_]+@', $module)) {
    $error = dt('The name of the module must contain only lowercase letters and underscores') . '.';
    drush_log($error, 'error');
    return;
  }

  // Filter by tables.
  $tables = _ctools_drush_explode_options('tables');

  // Check status.
  $filter = drush_get_option('filter', FALSE);
  if (empty($filter)) {
    drush_set_option('filter', 'enabled');
  }

  // Selection.
  $options = array('all' => dt('Export everything'), 'select' => dt('Make selection'));
  $selection = drush_choice($options, dt('Select to proceed'));

  if (!$selection) {
    return;
  }

  // Present the selection screens.
  if ($selection == 'select') {
    $selections = _ctools_drush_selection_screen($tables);
  }
  else {
    $info = _ctools_drush_export_info($tables, TRUE);
    $selections = $info['exportables'];
  }

  // Subdirectory.
  $dest_exists = FALSE;
  $subdir = drush_get_option('subdir', 'ctools_export');
  $dest = 'sites/all/modules/' . $subdir . '/' . $module;

  // Overwriting files can be set with 'remove' argument.
  $remove = drush_get_option('remove', FALSE);

  // Check if folder exists.
  if (file_exists($dest)) {
    $dest_exists = TRUE;
    if ($remove) {
      if (drush_confirm(dt('All files except for the .info and .module files in !module will be removed. You can choose later if you want to overwrite these files as well. Are you sure you want to proceed ?', array('!module' => $module)))) {
        $remove = TRUE;
        drush_log(dt('Files will be removed'), 'success');
      }
      else {
        drush_log(dt('Export aborted.'), 'success');
        return;
      }
    }
  }

  // Remove files (except for the .module file) if the destination folder exists.
  if ($remove && $dest_exists) {
    _ctools_drush_file_delete($dest);
  }

  // Create new dir if needed.
  if (!$dest_exists) {
    if (!file_exists('sites/all/modules/' . $subdir)) {
      drush_mkdir('sites/all/modules/' . $subdir);
    }
  }

  // Create destination directory.
  drush_mkdir($dest);

  // Load bulk export module.
  module_load_include('module', 'bulk_export');

  // Create options and call Bulk export function.
  // We create an array, because maybe in the future we can pass in more
  // options to the export function (pre-selected modules and/or exportables).
  $options = array(
    'name' => $module,
    'selections' => $selections,
  );
  $files = bulk_export_export(TRUE, $options);

  $alter = array(
    'module' => $module,
    'files' => $files,
  );
  // Let other drush commands alter the files.
  drush_command_invoke_all_ref('drush_ctex_files_alter', $alter);
  $files = $alter['files'];

  // Start writing.
  if (is_array($files)) {
    foreach ($files as $base_file => $data) {
      $filename = $dest . '/' . $base_file;
      // Extra check for .module file.
      if ($base_file == ($module . '.module' || $module . '.info') && file_exists($filename)) {
        if (!drush_confirm(dt('Do you want to overwrite !module_file', array('!module_file' => $base_file)))) {
          drush_log(dt('Writing of !filename skipped. This is the code that was supposed to be written:', array('!filename' => $filename)), 'success');
          drush_print('---------');
          drush_print(shellColours::getColouredOutput("\n$data", 'light_green'));
          drush_print('---------');
          continue;
        }
      }
      if (file_put_contents($filename, $data)) {
        drush_log(dt('Succesfully written !filename', array('!filename' => $filename)), 'success');
      }
      else {
        drush_log(dt('Error writing !filename', array('!filename' => $filename)), 'error');
      }
    }
  }
  else {
    drush_log(dt('No files were found to be written.'), 'error');
  }
}

/**
 * Helper function to select the exportables. By default, all exportables
 * will be selected, so it will be easier to deselect them.
 *
 * @param $tables
 */
function _ctools_drush_selection_screen(array $tables = array()) {
  $selections = $build = array();
  $files = system_rebuild_module_data();

  $selection_number = 0;

  $info = _ctools_drush_export_info($tables, TRUE);
  $exportables = $info['exportables'];
  $schemas = $info['schemas'];

  $export_tables = array();

  foreach (array_keys($exportables) as $table) {
    natcasesort($exportables[$table]);
    $export_tables[$table] = $files[$schemas[$table]['module']]->info['name'] . ' (' . $table . ')';
  }

  foreach ($export_tables as $table => $table_title) {
    if (!empty($exportables[$table])) {
      $table_count = count($exportables[$table]);
      $selection_number += $table_count;
      foreach ($exportables[$table] as $key => $title) {
        $build[$table]['title'] = $table_title;
        $build[$table]['items'][$key] = $title;
        $build[$table]['count'] = $table_count;
        $selections[$table][$key] = $key;
      }
    }
  }

  drush_print(dt('Number of exportables selected: !number', array('!number' => $selection_number)));
  drush_print(dt('By default all exportables are selected. Select a table to deselect exportables. Select "cancel" to start writing the files.'));

  // Let's go into a loop.
  $return = FALSE;
  while (!$return) {

    // Present the tables choice.
    $table_rows = array();
    foreach ($build as $table => $info) {
      $table_rows[$table] = $info['title'] . ' (' . $info['count'] . ')';
    }
    $table_choice = drush_choice($table_rows, dt('Select a table. Select cancel to start writing files.'));

    // Bail out.
    if (!$table_choice) {
      drush_log(dt('Selection mode done, starting to write the files.'), 'notice');
      $return = TRUE;
      return $selections;
    }

    // Present the exportables choice, using the drush_choice_multiple.
    $max = count($build[$table_choice]['items']);
    $exportable_rows = array();
    foreach ($build[$table_choice]['items'] as $key => $title) {
      $exportable_rows[$key] = $title;
    }
    drush_print(dt('Exportables from !table', array('!table' => $build[$table_choice]['title'])));
    $multi_select = drush_choice_multiple($exportable_rows, $selections[$table_choice], dt('Select exportables.'), '!value', '!value (selected)', 0, $max);

    // Update selections.
    if (is_array($multi_select)) {
      $build[$table_choice]['count'] = count($multi_select);
      $selections[$table_choice] = array();
      foreach ($multi_select as $key) {
        $selections[$table_choice][$key] = $key;
      }
    }
  }
}

/**
 * Delete files in a directory, keeping the .module  and .info files.
 *
 * @param $path
 *   Path to directory in which to remove files.
 */
function _ctools_drush_file_delete($path) {
  if (is_dir($path)) {
    $files = new DirectoryIterator($path);
    foreach ($files as $fileInfo) {
      if (!$fileInfo->isDot() && !in_array($fileInfo->getExtension(), array('module', 'info'))) {
        unlink($fileInfo->getPathname());
      }
    }
  }
}

/**
 * Drush callback: Export info.
 *
 * @params $table_names
 *   Each argument will be taken as a CTools exportable table name.
 */
function ctools_drush_export_info() {
  // Collect array of table names from args.
  $table_names = func_get_args();

  // Get format option to allow for alternative output.
  $format = drush_get_option('format', FALSE);
  $tables_only = drush_get_option('tables-only', FALSE);
  $filter = drush_get_option('filter', FALSE);
  $export_module = drush_get_option('module', FALSE);

  $load = (bool) $filter || $export_module;

  // Get info on these tables, or all tables if none specified.
  $info = _ctools_drush_export_info($table_names, $load);
  $schemas = $info['schemas'];
  $exportables = $info['exportables'];

  if (empty($exportables)) {
    drush_log(dt('There are no exportables available.'), 'warning');
    return;
  }

  // Filter by export module.
  if (is_string($export_module)) {
    $exportables = _ctools_drush_export_module_filter($exportables, $export_module);
  }

  if (empty($exportables)) {
    drush_log(dt('There are no exportables matching this criteria.'), 'notice');
    return;
  }

  $exportable_counts = _ctools_drush_count_exportables($exportables);

  // Only use array keys if --tables-only option is set.
  if ($tables_only) {
    $exportables = array_keys($exportables);
  }

  // Use format from --format option if it's present, and send to drush_format.
  if ($format) {
    // Create array with all exportable info and counts in one.
    $output = array(
      'exportables' => $exportables,
      'count' => $exportable_counts,
    );
    drush_print(drush_format($output, NULL, $format));
  }
  // Build a tabular output as default.
  else {
    $header = $tables_only ? array() : array(dt('Module'), dt('Base table'), dt('Exportables'));
    $rows = array();
    foreach ($exportables as $table => $info) {
      if (is_array($info)) {
        $row = array(
          $schemas[$table]['module'],
          $table,
          // Machine name is better for this?
          shellColours::getColouredOutput(implode("\n", array_keys($info)), 'light_green') . "\n",
        );
        $rows[] = $row;
      }
      else {
        $rows[] = array($info);
      }
    }
    if (!empty($rows)) {
      drush_print("\n");
      array_unshift($rows, $header);
      drush_print_table($rows, TRUE, array(20, 20));
      drush_print(dt('Total exportables found: !total', array('!total' => $exportable_counts['total'])));
      foreach ($exportable_counts['exportables'] as $table_name => $count) {
        drush_print(dt('!table_name (!count)', array('!table_name' => $table_name, '!count' => $count)), 2);
      }
      drush_print("\n");
    }
  }
}

/**
 * Drush callback: Acts as the hub for all op commands to keep
 * all arg handling etc in one place.
 */
function ctools_drush_export_op_command() {
  // Get all info for the current drush command.
  $command = drush_get_command();
  $op = '';

  switch ($command['command']) {
    case 'ctools-export-view':
      $op = 'view';
      break;

    case 'ctools-export-revert':
      // Revert is same as deleting. As any objects in the db are deleted.
      $op = 'delete';
      break;

    case 'ctools-export-enable':
      $op = 'enable';
      break;

    case 'ctools-export-disable':
      $op = 'disable';
      break;
  }

  if (!$op) {
    return;
  }

  if (drush_get_option('all', FALSE)) {
    $info = _ctools_drush_export_info(array(), TRUE);
    $exportable_info = $info['exportables'];

    $all = drush_confirm(dt('Are you sure you would like to !op all exportables on the system?',
      array('!op' => _ctools_drush_export_op_alias($op))));

    if ($all && $exportable_info) {
      foreach ($exportable_info as $table => $exportables) {
        if (!empty($exportables)) {
          ctools_drush_export_op($op, $table, $exportables);
        }
      }
    }
  }
  else {
    $args = func_get_args();
    // Table name should always be first arg...
    $table_name = array_shift($args);
    // Any additional args are assumed to be exportable names.
    $object_names = $args;

    // Return any exportables based on table name, object names, options.
    $exportables = _ctools_drush_export_op_command_logic($op, $table_name, $object_names);

    if ($exportables) {
      ctools_drush_export_op($op, $table_name, $exportables);
    }
  }
}

/**
 * Iterate through exportable object names, load them, and pass each
 * object to the correct op function.
 *
 * @param $op
 * @param $table_name
 * @param $exportables
 */
function ctools_drush_export_op($op = '', $table_name = '', $exportables = NULL) {
  $objects = ctools_export_crud_load_multiple($table_name, array_keys($exportables));

  $function = '_ctools_drush_export_' . $op;
  if (function_exists($function)) {
    foreach ($objects as $object) {
      $function($table_name, $object);
    }
  }
  else {
    drush_log(dt('CTools CRUD function !function doesn\'t exist.',
      array('!function' => $function)), 'error');
  }
}

/**
 * Helper function to abstract logic for selecting exportable types/objects
 * from individual commands as they will all share this same error
 * handling/arguments for returning list of exportables.
 *
 * @param $table_name
 * @param $object_names
 *
 * @return
 *   Array of exportable objects (filtered if necessary, by name etc..) or FALSE if not.
 */
function _ctools_drush_export_op_command_logic($op = '', $table_name = NULL, array $object_names = array()) {
  if (!$table_name) {
    drush_log(dt('Exportable table name empty. Use the --all command if you want to perform this operation on all tables.'), 'error');
    return FALSE;
  }

  // Get export info based on table name.
  $info = _ctools_drush_export_info(array($table_name), TRUE);

  if (!isset($info['exportables'][$table_name])) {
    drush_log(dt('Exportable table name not found.'), 'error');
    return FALSE;
  }

  $exportables = &$info['exportables'];

  if (empty($object_names)) {
    $all = drush_confirm(dt('No object names entered. Would you like to try and !op all exportables of type !type',
      array('!op' => _ctools_drush_export_op_alias($op), '!type' => $table_name)));
    if (!$all) {
      drush_log(dt('Command cancelled'), 'success');
      return FALSE;
    }
  }
  else {
    // Iterate through object names and check they exist in exportables array.
    // Log error and unset them if they don't.
    foreach ($object_names as $object_name) {
      if (!isset($exportables[$table_name][$object_name])) {
        drush_log(dt('Invalid exportable: !exportable', array('!exportable' => $object_name)), 'error');
        unset($object_names[$table_name][$object_name]);
      }
    }
    // Iterate through exportables to get just a list of selected ones.
    foreach (array_keys($exportables[$table_name]) as $exportable) {
      if (!in_array($exportable, $object_names)) {
        unset($exportables[$table_name][$exportable]);
      }
    }
  }

  $export_module = drush_get_option('module', FALSE);

  if (is_string($export_module)) {
    $exportables = _ctools_drush_export_module_filter($exportables, $export_module);
  }

  return $exportables[$table_name];
}

/**
 * Return array of CTools exportable info based on available tables returned from
 * ctools_export_get_schemas().
 *
 * @param $table_names
 *   Array of table names to return.
 * @param $load
 *   (bool) should ctools exportable objects be loaded for each type.
 *   The default behaviour will load just a list of exportable names.
 *
 * @return
 *   Nested arrays of available exportables, keyed by table name.
 */
function _ctools_drush_export_info(array $table_names = array(), $load = FALSE) {
  ctools_include('export');
  // Get available schemas that declare exports.
  $schemas = ctools_export_get_schemas(TRUE);
  $exportables = array();

  if (!empty($schemas)) {
    // Remove types we don't want, if any.
    if (!empty($table_names)) {
      foreach (array_keys($schemas) as $table_name) {
        if (!in_array($table_name, $table_names)) {
          unset($schemas[$table_name]);
        }
      }
    }
    // Load array of available exportables for each schema.
    foreach ($schemas as $table_name => $schema) {
      // Load all objects.
      if ($load) {
        $exportables[$table_name] = ctools_export_crud_load_all($table_name);
      }
      // Get a list of exportable names.
      else {
        if (!empty($schema['export']['list callback']) && function_exists($schema['export']['list callback'])) {
          $exportables[$table_name] = $schema['export']['list callback']();
        }
        else {
          $exportables[$table_name] = ctools_export_default_list($table_name, $schema);
        }
      }
    }
  }

  if ($load) {
    $filter = drush_get_option('filter', FALSE);
    $exportables = _ctools_drush_filter_exportables($exportables, $filter);
  }

  return array('exportables' => $exportables, 'schemas' => $schemas);
}

/**
 * View a single object.
 *
 * @param $table_name
 * @param $object
 */
function _ctools_drush_export_view($table_name, $object) {
  $indent = drush_get_option('indent', '');
  $no_colour = drush_get_option('no-colour', FALSE);
  $export = ctools_export_crud_export($table_name, $object, $indent);
  if ($no_colour) {
    drush_print("\n$export");
  }
  else {
    drush_print(shellColours::getColouredOutput("\n$export", 'light_green'));
  }
}

/**
 * Revert a single object.
 *
 * @param $table_name
 * @param $object
 */
function _ctools_drush_export_delete($table_name, $object) {
  $name = _ctools_drush_get_export_name($table_name, $object);

  if (_ctools_drush_object_is_overridden($object)) {
    // Remove from db.
    ctools_export_crud_delete($table_name, $object);
    drush_log("Reverted object: $name", 'success');
  }
  else {
    drush_log("Nothing to revert for: $name", 'notice');
  }
}

/**
 * Enable a single object.
 *
 * @param $table_name
 * @param $object
 */
function _ctools_drush_export_enable($table_name, $object) {
  $name = _ctools_drush_get_export_name($table_name, $object);

  if (_ctools_drush_object_is_disabled($object)) {

    // Enable object.
    ctools_export_crud_enable($table_name, $object);
    drush_log("Enabled object: $name", 'success');
  }
  else {
    drush_log("$name is already Enabled", 'notice');
  }
}

/**
 * Disable a single object.
 *
 * @param $table_name
 * @param $object
 */
function _ctools_drush_export_disable($table_name, $object) {
  $name = _ctools_drush_get_export_name($table_name, $object);

  if (!_ctools_drush_object_is_disabled($object)) {
    // Disable object.
    ctools_export_crud_disable($table_name, $object);
    drush_log("Disabled object: $name", 'success');
  }
  else {
    drush_log("$name is already disabled", 'notice');
  }
}

/**
 * Filter a nested array of exportables by export module.
 *
 * @param array $exportables
 *   Passed by reference. A nested array of exportables, keyed by table name.
 * @param string $export_module
 *   The name of the export module providing the exportable.
 */
function _ctools_drush_export_module_filter($exportables, $export_module) {
  $module_list = module_list();

  if (!isset($module_list[$export_module])) {
    drush_log(dt('Invalid export module: !export_module', array('!export_module' => $export_module)), 'error');
  }

  foreach ($exportables as $table => $objects) {
    foreach ($objects as $key => $object) {
      if (empty($object->export_module) || ($object->export_module !== $export_module)) {
        unset($exportables[$table][$key]);
      }
    }
  }

  return array_filter($exportables);
}

/**
 * Gets the key for an exportable type.
 *
 * @param $table_name
 *   The exportable table name.
 * @param $object
 *   The exportable object.
 *
 * @return string
 *   The key defined in the export schema data.
 */
function _ctools_drush_get_export_name($table_name, $object) {
  $info = _ctools_drush_export_info(array($table_name));
  $key = $info['schemas'][$table_name]['export']['key'];
  return $object->{$key};
}

/**
 * Determine if an object is disabled.
 *
 * @param $object
 *   Loaded CTools exportable object.
 *
 * @return TRUE or FALSE
 */
function _ctools_drush_object_is_disabled($object) {
  return (isset($object->disabled) && ($object->disabled == TRUE)) ? TRUE : FALSE;
}

/**
 * Determine if an object is enabled.
 *
 * @see _ctools_drush_object_is_disabled()
 */
function _ctools_drush_object_is_enabled($object) {
  return (empty($object->disabled)) ? TRUE : FALSE;
}

/**
 * Determine if an object is overridden.
 */
function _ctools_drush_object_is_overridden($object) {
  $status = EXPORT_IN_CODE + EXPORT_IN_DATABASE;
  return ($object->export_type == $status) ? TRUE : FALSE;
}

/**
 * Determine if an object is not overridden.
 */
function _ctools_drush_object_is_not_overridden($object) {
  $status = EXPORT_IN_CODE + EXPORT_IN_DATABASE;
  return ($object->export_type == $status) ? FALSE : TRUE;
}

/**
 * Determine if an object is only in the db.
 */
function _ctools_drush_object_is_db_only($object) {
  return ($object->export_type == EXPORT_IN_DATABASE) ? TRUE : FALSE;
}

/**
 * Determine if an object is not in the db.
 */
function _ctools_drush_object_is_not_db_only($object) {
  return ($object->export_type == EXPORT_IN_DATABASE) ? FALSE : TRUE;
}

/**
 * Determine if an object is a code only default.
 */
function _ctools_drush_object_is_code_only($object) {
  return ($object->export_type == EXPORT_IN_CODE) ? TRUE : FALSE;
}

/**
 * Determine if an object is not a code only default.
 */
function _ctools_drush_object_is_not_code_only($object) {
  return ($object->export_type == EXPORT_IN_CODE) ? FALSE : TRUE;
}

/**
 * Return an array of count information based on exportables array.
 *
 * @param $exportables
 *   Array of exportables to count.
 *
 * @return
 *   Array of count data containing the following:
 *     'total' - A total count of all exportables.
 *     'exportables' - An array of exportable counts per table.
 */
function _ctools_drush_count_exportables($exportables) {
  $count = array('exportables' => array());

  foreach ($exportables as $table => $objects) {
    // Add the object count for each table.
    $count['exportables'][$table] = count($objects);
  }

  // Once all tables have been counted, total these up.
  $count['total'] = array_sum($count['exportables']);

  return $count;
}

/**
 * Filters a collection of exportables based on filters.
 *
 * @param $exportables
 * @param $filter
 */
function _ctools_drush_filter_exportables($exportables, $filter) {
  $eval = FALSE;

  if (is_string($filter)) {
    switch ($filter) {
      // Show enabled exportables only.
      case 'enabled':
        $eval = '_ctools_drush_object_is_disabled';
        break;

      // Show disabled exportables only.
      case 'disabled':
        $eval = '_ctools_drush_object_is_enabled';
        break;

      // Show overridden exportables only.
      case 'overridden':
        $eval = '_ctools_drush_object_is_not_overridden';
        break;

      // Show database only exportables.
      case 'database':
        $eval = '_ctools_drush_object_is_not_db_only';
        break;

      // Show code only exportables.
      case 'code':
        $eval = '_ctools_drush_object_is_not_code_only';
        break;

      // Do nothing.
      case 'all':
        break;

      default:
        drush_log(dt('Invalid filter option. Available options are: enabled, disabled, overridden, database, and code.'), 'error');
        return;
    }

    if ($eval) {
      foreach ($exportables as $table => $objects) {
        foreach ($objects as $key => $object) {
          if ($eval($object)) {
            unset($exportables[$table][$key]);
          }
        }
      }
    }
  }

  return array_filter($exportables);
}

/**
 * Return an alias for an op, that will be used to show as output.
 * For now, this is mainly necessary for delete => revert alias.
 *
 * @param $op
 *   The op name. Such as 'enable', 'disable', or 'delete'.
 *
 * @return
 *   The matched alias value or the original $op passed in if not found.
 */
function _ctools_drush_export_op_alias($op) {
  $aliases = array(
    'delete' => 'revert',
  );

  if (isset($aliases[$op])) {
    return $aliases[$op];
  }

  return $op;
}

/**
 * Convert the drush options from a csv list into an array.
 *
 * @param $drush_option
 *   The drush option name to invoke.
 *
 * @return
 *   Exploded array of options.
 */
function _ctools_drush_explode_options($drush_option) {
  $options = drush_get_option($drush_option, array());
  if (!empty($options)) {
    $options = explode(',', $options);
    return array_map('trim', $options);
  }

  return $options;
}

/**
 * Class to deal with wrapping output strings with
 * colour formatting for the shell.
 */
class shellColours {

  private static $foreground_colours = array(
    'black' => '0;30',
    'dark_gray' => '1;30',
    'blue' => '0;34',
    'light_blue' => '1;34',
    'green' => '0;32',
    'light_green' => '1;32',
    'cyan' => '0;36',
    'light_cyan' => '1;36',
    'red' => '0;31',
    'light_red' => '1;31',
    'purple' => '0;35',
    'light_purple' => '1;35',
    'brown' => '0;33',
    'yellow' => '1;33',
    'light_gray' => '0;37',
    'white' => '1;37',
  );

  private static $background_colours = array(
    'black' => '40',
    'red' => '41',
    'green' => '42',
    'yellow' => '43',
    'blue' => '44',
    'magenta' => '45',
    'cyan' => '46',
    'light_gray' => '47',
  );

  /**
   * shellColours constructor.
   */
  private function __construct() {}

  /**
   * Returns coloured string.
   */
  public static function getColouredOutput($string, $foreground_colour = NULL, $background_colour = NULL) {
    $coloured_string = "";

    // Check if given foreground colour found.
    if ($foreground_colour) {
      $coloured_string .= "\033[" . self::$foreground_colours[$foreground_colour] . "m";
    }
    // Check if given background colour found.
    if ($background_colour) {
      $coloured_string .= "\033[" . self::$background_colours[$background_colour] . "m";
    }

    // Add string and end colouring.
    $coloured_string .= $string . "\033[0m";

    return $coloured_string;
  }

  /**
   * Returns all foreground colour names.
   */
  public static function getForegroundColours() {
    return array_keys(self::$foreground_colours);
  }

  /**
   * Returns all background colour names.
   */
  public static function getBackgroundColours() {
    return array_keys(self::$background_colours);
  }

} // shellColours
