<?php
/**
 * @file
 * Drush integration for Libraries API.
 */

/**
 * Implements hook_drush_command().
 */
function libraries_drush_command() {
  $items = array();

  $items['libraries-list'] = array(
    'description' => dt('Show a list of registered libraries.'),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUPAL_FULL,
    'aliases' => array('lls', 'lib-list'),
  );

  $items['libraries-download'] = array(
    'description' => dt('Download library files of registered libraries.'),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUPAL_FULL,
    'aliases' => array('ldl', 'lib-download'),
    'arguments' => array(
      'libraries' => 'A comma delimited list of library machine names.',
    ),
    'required-arguments' => TRUE,
  );

  return $items;
}

/**
 * Implements hook_drush_cache_clear().
 *
 * @see drush_cache_clear_types()
 */
function libraries_drush_cache_clear(array &$types) {
  $types['libraries'] = 'libraries_drush_invalidate_cache';
}

/**
 * Clears the library cache.
 */
function libraries_drush_invalidate_cache() {
  // @see drupal_flush_all_caches()
  foreach (libraries_flush_caches() as $table) {
    cache_clear_all('*', $table, TRUE);
  }
}

/**
 * Command callback. Show a list of registered libraries.
 */
function drush_libraries_list() {
  $libraries = libraries_detect();
  ksort($libraries);

  if (empty($libraries)) {
    drush_print('There are no registered libraries.');
  }
  else {
    module_load_include('inc', 'libraries', 'libraries.admin');

    $rows = array();
    // drush_print_table() automatically treats the first row as the header, if
    // $header is TRUE.
    $rows[] = array(
      dt('Name'),
      dt('Status'),
      dt('Version'),
      dt('Variants'),
      dt('Dependencies'),
      dt('Provider'),
    );
    foreach ($libraries as $name => $library) {
      // Only list installed variants.
      $variants = array();
      foreach ($library['variants'] as $variant_name => $variant) {
        if ($variant['installed']) {
          $variants[] = $variant_name;
        }
      }

      $rows[] = array(
        $name,
        $library['installed'] ? dt('OK') : drupal_ucfirst($library['error']),
        ($library['installed'] && $library['version']) ? '-' : $library['version'],
        $variants ? implode(', ', $variants) : '-',
        $library['dependencies'] ? implode(', ', $library['dependencies']) : '-',
        libraries_admin_get_provider($library),
      );
    }

    // Make the possible values for the 'Status' column and the 'Version' header
    // wrap nicely.
    $widths = array(0, 12, 7, 0, 0, 0);
    drush_print_table($rows, TRUE, $widths);
  }
}

/**
 * Command callback. Downloads a library.
 *
 * Only libraries that provide a download file URL can be downloaded.
 *
 * @see hook_libraries_info()
 * @see drush_pm_download()
 */
function drush_libraries_download() {
  drush_command_include('pm-download');

  $libraries = libraries_info();

  // @todo Consider supporting downloading all downloadable libraries.
  // @todo Consider offering a selection if no library is specified.
  foreach (pm_parse_arguments(func_get_args(), FALSE) as $machine_name) {
    if (!isset($libraries[$machine_name])) {
      $message = dt("The !library library is not registered with Libraries API.\n", array('!library' => $machine_name));
      $message .= dt("Provide an info file for it or implement hook_libraries_info().\n");
      $message .= dt("See hook_libraries_info() for more information.\n");
      drush_set_error('DRUSH_LIBRARY_UKNOWN', $message);
      continue;
    }
    $library = $libraries[$machine_name];

    if (empty($library['download file url'])) {
      $message = dt("The !library library cannot be downloaded.\n", array('!library' => $machine_name));
      $message .= dt("Libraries need to specify a download file URL to support being downloaded via Drush.\n");
      $message .= dt("See hook_libraries_info() for more information.\n");
      drush_set_error('DRUSH_LIBRARY_NOT_DOWNLOADABLE', $message);
      continue;
    }
    $download_url = $library['download file url'];

    drush_log(dt('Downloading library !name ...', array('!name' => $machine_name)));

    // @see package_handler_download_project() in wget.inc
    // It cannot be used directly because it will always try to extract the
    // archive which fails when downloading a single file.
    // @todo Modify upstream to be able to use
    //   package_handler_download_project() directly.
    // Prepare download path. On Windows file name cannot contain '?'.
    // See http://drupal.org/node/1782444
    $filename = str_replace('?', '_', basename($download_url));
    $download_path = drush_tempdir() . '/' . $filename;

    // Download the tarball.
    // Never cache the downloaded file. The downloading relies on the fact that
    // different versions of the library are available under the same URL as new
    // versions are released.
    $download_path = drush_download_file($download_url, $download_path, 0);
    if ($download_path || drush_get_context('DRUSH_SIMULATE')) {
      drush_log(dt('Downloading !filename was successful.', array('!filename' => $filename)));
    }
    else {
      drush_set_error('DRUSH_PM_DOWNLOAD_FAILED', dt('Unable to download !project to !path from !url.', array('!project' => $machine_name, '!path' => $download_path, '!url' => $download_url)));
      drush_log(dt('Error downloading !name', array('!name' => $machine_name)), 'error');
      continue;
    }

    // @todo Suport MD5 file hashing.

    // Extract the tarball in place and return the full path to the untarred directory.
    $download_base = dirname($download_path);
    if (drush_file_is_tarball($download_path)) {
      if (!$tar_file_list = drush_tarball_extract($download_path, $download_base, TRUE)) {
        // An error has been logged.
        return FALSE;
      }
      $tar_directory = drush_trim_path($tar_file_list[0]);
      $download_path = $download_base . '/' . $tar_directory;
    }
    else {
      $download_path = $download_base;
    }

    // Determine the install location for the project.  User provided
    // --destination has preference.
    $destination = drush_get_option('destination');
    if (!empty($destination)) {
      if (!file_exists($destination)) {
        drush_mkdir($destination);
      }
      $install_location = realpath($destination);
    }
    else {
      /** @see _pm_download_destination_lookup() */
      // _pm_download_destination_lookup() pluralizes the passed type by
      // appending an s.
      // This relies on the fact that there is no library named 'contrib'.
      // @todo Request that this be turned into a proper API upstream.
      $install_location = _pm_download_destination('librarie');
    }

    // @todo Consider invoking a hook similar to
    //   hook_drush_pm_download_destination_alter().

    // @todo Consider adding version-control support similar to pm-download.

    $install_location .= '/' . $machine_name;

    // Check if install location already exists.
    if (is_dir($install_location)) {
      if (drush_confirm(dt('Install location !location already exists. Do you want to overwrite it?', array('!location' => $install_location)))) {
        drush_delete_dir($install_location, TRUE);
      }
      else {
        drush_log(dt("Skip installation of !project to !dest.", array('!project' => $library['machine name'], '!dest' => $install_location)), 'warning');
        continue;
      }
    }

    // Copy the project to the install location.
    if (drush_op('_drush_recursive_copy', $download_path, $install_location)) {
      drush_log(dt("Library !project downloaded to !dest.", array('!project' => $machine_name, '!dest' => $install_location)), 'success');

      // @todo Consider invoking a hook similar to
      //   hook_drush_pm_post_download().

      // @todo Support printing release notes.
    }
    else {
      // We don't `return` here in order to proceed with downloading additional projects.
      drush_set_error('DRUSH_PM_DOWNLOAD_FAILED', dt("Project !project could not be downloaded to !dest.", array('!project' => $machine_name, '!dest' => $install_location)));
    }

    // @todo Consider adding notify support.
  }
}
