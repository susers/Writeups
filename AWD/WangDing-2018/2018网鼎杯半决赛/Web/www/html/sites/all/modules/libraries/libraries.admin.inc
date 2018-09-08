<?php

/**
 * @file
 * Provides administrative page and form callbacks for Libraries module.
 */

/**
 * Form generation callback for the libraries overview table.
 *
 * This is a form instead of a page to allow easier extending in contributed
 * modules.
 *
 * @param array $form
 *   An associative array containing the structure of the form.
 * @param array $form_state
 *   A keyed array containing the current state of the form.
 *
 * @return array
 *   The form array for the overview form.
 */
function libraries_admin_overview(array $form, array &$form_state) {
  $header = array(t('Name'), t('Status'), t('Installed version'), t('Provider'), t('Links'));
  $rows = array();

  $libraries = libraries_detect();
  uasort($libraries, 'libraries_admin_sort_title');

  foreach ($libraries as $machine_name => $library) {
    $actions = array();

    if ($library['vendor url']) {
      $actions[] = l('Homepage', $library['vendor url']);
    }
    if ($library['download url']) {
      $actions[] = l('Download', $library['download url']);
    }

    $rows[] = array(
      'data' => array(
        l($library['name'], 'admin/reports/libraries/' . $machine_name),
        ($library['installed'] ? t('OK') : drupal_ucfirst($library['error'])),
        (isset($library['version']) ? $library['version'] : ''),
        libraries_admin_get_provider_with_type($library),
        implode(' | ', $actions),
      ),
      'class' => ($library['installed'] ? array('ok') : array('error')),
    );
  }

  $form['libraries']['list'] = array(
    '#theme' => 'table',
    '#header' => $header,
    '#rows' => $rows,
    '#empty' => t('There are currently no libraries installed'),
  );

  return $form;
}

/**
 * Form generation callback for the status overview for a single library.
 *
 * This is a form instead of a page to allow easier extending in contributed
 * modules.
 *
 * @param array $form
 *   An associative array containing the structure of the form.
 * @param array $form_state
 *   A keyed array containing the current state of the form.
 * @param array $library
 *   A library information array.
 *
 * @return array|null
 *   The form array for the status form or NULL if the library was not found.
 *
 * @todo Add some var_export($library)-style output
 */
function libraries_admin_library_status_form(array $form, array &$form_state, $library) {
  drupal_set_title(t('Status report for library %library', array('%library' => $library['name'])), PASS_THROUGH);

  if ($library['installed']) {
    drupal_set_message(t('The %name library is installed correctly.', array('%name' => $library['name'])));
    $form['status'] = libraries_admin_status_table($library);
  }
  else {
    drupal_set_message($library['error message'], 'error');
    switch ($library['error']) {
      case 'not found':
        $form['instructions'] = libraries_admin_instructions_missing($library);
        break;

      case 'not detected':
        $form['instructions'] = libraries_admin_instructions_undetected($library);;
        break;

      case 'not supported':
        $form['instructions'] = libraries_admin_instructions_unsupported($library);
        break;

      case 'missing dependency':
        $form['instructions']['instruction']['#markup'] = t('There a missing dependency in your configuration that prevent this library to work properly.') . '<br>';
        break;

      case 'incompatible dependency':
        $form['instructions']['instruction']['#markup'] = t('There an incompatible dependency in your configuration that prevent this library to work properly.') . '<br>';
        break;
    }
  }

  return $form;
}


/**
 * Displays a table of status information about a library.
 *
 * @param array $library
 *   A library information array.
 *
 * @return array
 *   A renderable array containing a table with status information.
 */
function libraries_admin_status_table(array $library) {
  $header = array(array(
    // @todo The title implies that other type of information is displayed, as
    //   well, but this is currently not the case.
    // @todo Use CSS instead of a <strong> element.
    'data' => '<strong>' . t('General information') . '</strong>',
    'colspan' => 2,
    'class' => 'table-heading',
    'no_striping' => TRUE,
  ));

  $rows = array();
  // @todo Use CSS instead of <strong> elements.
  $rows['name'] = array('<strong>' . t('Name') . '</strong>', check_plain($library['name']));
  $rows['machine_name'] = array('<strong>' . t('Machine name') . '</strong>', check_plain($library['machine name']));
  if ($library['vendor url']) {
    $rows['vendor_url'] = array('<strong>' . t('Vendor URL') . '</strong>', l($library['vendor url'], $library['vendor url']));
  }
  if ($library['download url']) {
    $rows['download_url'] = array('<strong>' . t('Download URL') . '</strong>', l($library['download url'], $library['download url']));
  }
  $rows['provider'] = array('<strong>' . t('Provider') . '</strong>', libraries_admin_get_provider_with_type($library));
  $rows['library_path'] = array('<strong>' . t('Library path') . '</strong>', $library['library path']);
  $rows['version'] = array('<strong>' . t('Version') . '</strong>', $library['version']);
  if (!empty($library['variants'])) {
    $rows['variants'] = array('<strong>' . t('Variants') . '</strong>', implode(', ', array_keys($library['variants'])));
  }

  return array(
    '#theme' => 'table',
    '#header' => $header,
    '#rows' => $rows,
  );
}

/**
 * Returns instructions for dealing with a missing library.
 *
 * @param array $library
 *   A library information array.
 *
 * @return array
 *   A renderable array containing the instructions.
 */
function libraries_admin_instructions_missing(array $library) {
  $build = array();

  $build['instruction']['#markup'] = t('Follow these steps to install the library:');

  $items = array();
  // 1. Download the library.
  // If no supported versions are specified, the latest version is
  // recommended.
  if (empty($library['versions'])) {
    $items[] = t('Download the latest version of the library <a href="@download-url">here</a>.', array(
      '@download-url' => $library['download url'],
    ));
  }
  // Otherwise, the latest supported version is recommended.
  else {
    $versions = array_keys($library['versions']);
    usort($versions, 'version_compare');
    $versions = array_reverse($versions);
    $version = $versions[0];
    $items[] = t('Download version %version of the library <a href="@download-url">here</a>.', array(
      '%version' => $version,
      '@download-url' => $library['download url'],
    ));
  }
  // 2. Unpack it.
  $items[] = t('If the library is an archive, i.e. if the file ending is for example <em>.tar.gz</em> or <em>.zip</em>, unpack it.');
  // 3. Create the libraries folder.
  $items[] = t('In the %library-directory directory of your Drupal installation create a %library directory.', array(
    '%library-directory' => 'sites/all/libraries',
    '%library' => $library['machine name'],
  ));
  // 4. Upload it.
  // If the library has variant-independent files, give the user the
  // location of an example file to check his filesystem against.
  if ($directory_layout = libraries_admin_directory_layout($library)) {
    $items[] = t('Upload the whole library (which can consist of multiple directories) into the newly created %library-path directory. The following files and directories should be contained in that directory: !directory-layout', array(
      '%library-path' => 'sites/all/libraries/' . $library['machine name'],
      '!directory-layout' => drupal_render($directory_layout),
    ));
  }
  else {
    $items[] = t('Upload the whole library (which can consist of multiple directories) into the newly created %library-path directory.', array(
      '%library-path' => 'sites/all/libraries/' . $library['machine name'],
    ));
  }
  // 5. Reload.
  $items[] = t('<a href="">Reload</a> the page. If successful, you should see status information about this library.');

  $build['steps'] = array(
    '#theme' => 'item_list',
    '#items' => $items,
    '#type' => 'ol'
  );

  return $build;
}


/**
 * Returns instructions for dealing with an undetected library.
 *
 * @param array $library
 *   A library information array.
 *
 * @return array
 *   A renderable array containing the instructions.
 */
function libraries_admin_instructions_undetected($library) {
  $build = array();
  // Re-check location.
  // @todo Avoid usage of <br> elements.
  $build['instruction']['#markup'] = t('Check that the whole library is located at %library-path.', array(
      '%library-path' => $library['library path'],
    )) . '<br>';
  // If the library has variant-independent files, give the user the
  // exact location of the files to check against.
  // @todo It should be possible to display even variant-specific files
  //   in case the variant is installed, but libraries_detect() does not
  //   detect variants if the library version cannot be detected.
  if ($directory_layout = libraries_admin_directory_layout($library)) {
    $build['directory_layout'] = $directory_layout;
    $build['directory_layout']['#prefix'] = t('The following files and directories should be contained in that directory:');
  }

  // If the library is placed correctly the library information is
  // incorrect.
  // This switch could be avoided by using $library['info type'], but that would
  // hinder properly translating these strings.
  $build['reload']['#markup'] = t('If you have moved any files, <a href="">reload</a> the page. If successful, you should see status information about this library.') . '<br>';
  $build['notice']['#markup'] = t('If the files are placed correctly and the version can still not be detected, the library information is incorrect.') . '<br>';

  $provider = libraries_admin_get_provider($library);
  switch ($library['info type']) {
    case 'module':
      $build['contact']['#markup'] = t('Contact the maintainer of the %module module to correct this.', array(
          '%module' => $provider,
        )) . '<br>';
      break;

    case 'theme':
      $build['contact']['#markup'] = t('Contact the maintainer of the %theme theme to correct this.', array(
          '%theme' => $provider,
        )) . '<br>';
      break;

    case 'info file':
      $build['contact']['#markup'] = t('Contact the maintainer of the %info-file info file to correct this.', array(
          '%info-file' => $provider,
        )) . '<br>';
      break;
  }
  return $build;
}


/**
 * Returns instructions for dealing with an unsupported library.
 *
 * @param array $library
 *   A library information array.
 *
 * @return array
 *   A renderable array containing the instructions.
 */
function libraries_admin_instructions_unsupported($library) {
  $build = array();
  $items = array();

  // Either download a different version of the library...
  $versions = array_keys($library['versions']);
  usort($versions, 'version_compare');
  $versions = array_reverse($versions);
  $version = $versions[0];
  $build['instruction']['#markup'] = t('Please install version %version of the library by following the following steps:',
    array(
      '%version' => $version,
    ));
  // 1. Delete the old library.
  $items[] = t('Delete the entire contents of the %library-path directory.',
    array(
      '%library-path' => $library['library path'],
    ));
  // 2. Download the new library.
  $items[] = t('Download version %version of the library <a href="@download-url">here</a>.',
    array(
      '%version' => $version,
      '@download-url' => $library['download url'],
    ));
  // 3. Unpack it.
  $items[] = t('If the library is an archive, i.e. if the file ending is for example <em>.tar.gz</em> or <em>.zip</em>, unpack it.');
  // 4. Upload the new library.
  // If the library has variant-independent files, give the user the
  // location of an example file to check his filesystem against.
  if ($directory_layout = libraries_admin_directory_layout($library)) {
    $items[] = t('Upload the new files into the %library-path directory. The following files and directories should be contained in that directory: !directory-layout',
      array(
        '%library-path' => $library['library path'],
        '!directory-layout' => drupal_render($directory_layout),
      ));
  }
  else {
    $items[] = t('Upload the new files into the %library-path directory.',
      array(
        '%library-path' => $library['library path'],
      ));
  }
  // 5. Reload.
  $items[] = t('<a href="">Reload</a> the page. If successful, you should see status information about this library.');
  $build['steps'] = array(
    '#theme' => 'item_list',
    '#items' => $items,
    '#type' => 'ol',
  );
  // ...or contact the maintainer of the library information.
  $provider = libraries_admin_get_provider($library);
  switch ($library['info type']) {
    case 'module':
      $build['contact']['#markup'] = t('If you are bound to version @version of the library, ask the maintainer of the %module module to provide support for it.', array(
          '@version' => $library['version'],
          '%module' => $provider,
        )) . '<br>';
      break;

    case 'theme':
      $build['contact']['#markup'] = t('If you are bound to version @version of the library, ask the maintainer of the %theme theme to provide support for it.', array(
          '@version' => $library['version'],
          '%theme' => $provider,
        )) . '<br>';
      break;

    case 'info file':
      $build['contact']['#markup'] = t('If you are bound to version @version of the library, ask the maintainer of the %info-file info file to provide support for it.', array(
          '@version' => $library['version'],
          '%info-file' => $provider,
        )) . '<br>';
      break;
  }
  return $build;
}

/**
 * Returns the directory layout of the library, if possible.
 *
 * The result of this function can help users to verify that they have uploaded
 * the library to the correct location.
 *
 * @param array $library
 *   A library information array.
 *
 * @return array|false
 *   A renderable array containing the directory layout of the library or FALSE
 *   if a directory layout could not be generated.
 */
function libraries_admin_directory_layout(array $library) {
  $build = array(
    '#theme' => 'item_list',
    '#type' => 'ul',
    '#items' => array(),
  );

  $items = &$build['#items'];
  if ($library['path']) {
    $items = &libraries_admin_path_to_tree($items, $library['path']);
  }
  foreach (array('js', 'css', 'php') as $type) {
    if (!empty($library['files'][$type])) {
      $files = array_keys($library['files'][$type]);
      foreach ($files as $file) {
        // Skip JavaScript settings.
        if (is_int($file)) {
          continue;
        }

        $children = &$items;
        libraries_admin_path_to_tree($children, $file);
      }
    }
  }
  return $build['#items'] ? $build : FALSE;
}

/**
 * Converts a file path into a tree structure for use in an item list.
 *
 * For example, the path 'foo/bar/baz' will be converted into the tree structure
 * represented by the following list:
 * - foo
 *   - bar
 *     - baz
 *
 * The $items array that is modified by reference or returned (see below) can
 * be used as the 'items' variable for theme_item_list().
 *
 * This function modifies passed-in $items array, so that multiple paths can
 * be placed into the same tree structure easily.
 *
 * @code
 *   $items = array();
 *   foreach ($paths as $path) {
 *     libraries_admin_path_to_tree($items, $path);
 *   }
 * @endcode
 *
 * It also returns the last item by reference, so that it can also be used to
 * traverse into a sub-structure and add further children there.
 *
 * @code
 *   $items = array();
 *   $children = &libraries_admin_path_to_tree($items, $path);
 *   foreach ($sub_paths as $sub_path) {
 *     libraries_admin_path_to_tree($children, $sub_path);
 *   }
 * @endcode
 *
 * @param array $items
 * @param string $path
 *
 * @return array
 */
function &libraries_admin_path_to_tree(array &$items, $path) {
  $part = strtok($path, '/');
  while ($part) {
    if (!isset($items[$part])) {
      $items[$part] = array(
        'data' => $part,
        'children' => array(),
      );
    }
    $items = &$items[$part]['children'];
    $part = strtok('/');
  }

  return $items;
}

/**
 * Sorts libraries by name.
 *
 * This function can be used as a callback for usort() or uasort().
 *
 * @param array $a
 *   The first library information array.
 * @param array $b
 *   The second library information array.
 *
 * @return int
 *   Returns -1 if $a is considered smaller than $b, 1 if $a considered greater
 *   than $b and 0 if $a and $b are considered equal.
 *
 * @see strnatcasecmp()
 * @see usort()
 * @see uasort()
 */
function libraries_admin_sort_title(array $a, array $b) {
  return strnatcasecmp($a['name'], $b['name']);
}

/**
 * Returns the library's provider.
 *
 * The provider can be a module, a theme, or an info file.
 *
 * @param array $library
 *   A library information array.
 *
 * @return string
 *   The provider.
 */
function libraries_admin_get_provider($library) {
  $provider = '';

  switch ($library['info type']) {
    case 'module':
    case 'theme':
      $info = system_get_info($library['info type'], $library[$library['info type']]);
      $provider = $info['name'];
      break;

    case 'info file':
      $provider = basename($library['info file']);
      break;
  }

  return $provider;
}

/**
 * Returns the library's provider and provider type.
 *
 * The provider type is either 'module', 'theme', or 'info file'.
 *
 * @param array $library
 *   A library information array.
 *
 * @return string
 *   The provider and provider type.
 */
function libraries_admin_get_provider_with_type($library) {
  $provider = libraries_admin_get_provider($library);
  $provider_with_type = '';

  // This switch could be avoided by using $library['info type'], but that would
  // hinder properly translating these strings.
  switch ($library['info type']) {
    case 'module':
      $provider_with_type = t('%module module', array('%module' => $provider));
      break;

    case 'theme':
      $provider_with_type = t('%theme theme', array('%theme' => $provider));
      break;

    case 'info file':
      $provider_with_type = t('%info-file info file', array('%info-file' => $provider));
      break;
  }

  return $provider_with_type;
}
