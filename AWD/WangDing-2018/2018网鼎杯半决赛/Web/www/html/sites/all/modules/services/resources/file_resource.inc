<?php

/**
 * @file
 * File resource.
 */

/**
 * THERE SHOULD BE NO UPDATE!!!
 * Drupal doesn't allow updating or replacing a file in the files table.
 * If you need to, create a new file and remove the old file.
 */
function _file_resource_definition() {
  return array(
    'file' => array(
      'operations' => array(
        'create' => array(
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'resources/file_resource'),
          'help' => 'Create a file with base64 encoded data',
          'callback' => '_file_resource_create',
          'access callback' => '_file_resource_access',
          'access arguments' => array('create'),
          'access arguments append' => TRUE,
          'args' => array(
            array(
              'name' => 'file',
              'type' => 'array',
              'description'    => t('An array representing a file.'),
              'source' => 'data',
              'optional' => FALSE,
            ),
          ),
        ),
        'retrieve' => array(
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'resources/file_resource'),
          'help' => 'Retrieve a file',
          'callback' => '_file_resource_retrieve',
          'access callback' => '_file_resource_access',
          'access arguments' => array('view'),
          'access arguments append' => TRUE,
          'args' => array(
            array(
              'name' => 'fid',
              'type' => 'int',
              'description' => 'The fid of the file to retrieve.',
              'source' => array('path' => '0'),
              'optional' => FALSE,
            ),
            array(
              'name'         => 'file_contents',
              'type'         => 'int',
              'description'  => t('To return file contents or not.'),
              'source'       => array('param' => 'file_contents'),
              'default value' => TRUE,
              'optional' => TRUE,
            ),
            array(
              'name'         => 'image_styles',
              'type'         => 'int',
              'description'  => t('To return image styles or not.'),
              'source'       => array('param' => 'image_styles'),
              'default value' => FALSE,
              'optional' => TRUE,
            ),
          ),
        ),
        'delete' => array(
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'resources/file_resource'),
          'help' => 'Delete a file',
          'callback' => '_file_resource_delete',
          'access callback' => '_file_resource_access',
          'access arguments' => array('delete'),
          'access arguments append' => TRUE,
          'args' => array(
            array(
              'name' => 'fid',
              'type' => 'int',
              'description' => 'The id of the file to delete',
              'source' => array('path' => '0'),
              'optional' => FALSE,
            ),
          ),
        ),
        'index' => array(
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'resources/file_resource'),
          'callback' => '_file_resource_index',
          'help' => 'List all files',
          'args' => array(
            array(
              'name' => 'page',
              'optional' => TRUE,
              'type' => 'int',
              'description' => 'The zero-based index of the page to get, defaults to 0.',
              'default value' => 0,
              'source' => array('param' => 'page'),
            ),
            array(
              'name' => 'fields',
              'optional' => TRUE,
              'type' => 'string',
              'description' => 'The fields to get.',
              'default value' => '*',
              'source' => array('param' => 'fields'),
            ),
            array(
              'name' => 'parameters',
              'optional' => TRUE,
              'type' => 'array',
              'description' => 'Parameters',
              'default value' => array(),
              'source' => array('param' => 'parameters'),
            ),
            array(
              'name' => 'pagesize',
              'optional' => TRUE,
              'type' => 'int',
              'description' => 'Number of records to get per page.',
              'default value' => variable_get('services_file_index_page_size', 20),
              'source' => array('param' => 'pagesize'),
            ),
            array(
              'name' => 'options',
              'optional' => TRUE,
              'type' => 'array',
              'description' => 'Additional query options.',
              'default value' => array(
                'orderby' => array(
                  'timestamp' => 'DESC'
                )
              ),
              'source' => array('param' => 'options'),
            ),
          ),
          'access callback' => '_file_resource_access',
          'access arguments' => array('index'),
          'access arguments append' => TRUE,
        ),
      ),
      'actions' => array(
        'create_raw' => array(
          'help' => 'Create a file with raw data.',
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'resources/file_resource'),
          'callback' => '_file_resource_create_raw',
          'access callback' => '_file_resource_access',
          'access arguments' => array('create_raw'),
          'access arguments append' => TRUE,
        ),
      ),
    ),
  );
}

/**
 * Adds a new file and returns the fid.
 *
 * @param $file
 *   An array as representing the file with a base64 encoded $file['file']
 * @return
 *   Unique identifier for the file (fid) or errors if there was a problem.
 */
function _file_resource_create($file) {
  // Adds backwards compatability with regression fixed in #1083242
  // $file['file'] can be base64 encoded file so we check whether it is
  // file array or file data.
  $file = _services_arg_value($file, 'file');

  // If the file data or filename is empty then bail.
  if (!isset($file['file']) || empty($file['filename'])) {
    return services_error(t("Missing data the file upload can not be completed"), 500);
  }

  // Sanitize the file extension, name, path and scheme provided by the user.
  $destination = empty($file['filepath'])
    ? file_default_scheme() . '://' . _services_file_check_destination($file['filename'])
    : _services_file_check_destination_uri($file['filepath']);

  $dir = drupal_dirname($destination);
  // Build the destination folder tree if it doesn't already exists.
  if (!file_prepare_directory($dir, FILE_CREATE_DIRECTORY)) {
    return services_error(t("Could not create destination directory for file."), 500);
  }

  // Write the file
  if (!$file_saved = file_save_data(base64_decode($file['file']), $destination)) {
    return services_error(t("Could not write file to destination"), 500);
  }

  if (isset($file['status']) && $file['status'] == 0) {
    // Save as temporary file.
    $file_saved->status = 0;
    file_save($file_saved);
  }
  else {
    // Required to be able to reference this file.
    file_usage_add($file_saved, 'services', 'files', $file_saved->fid);
  }

  return array(
    'fid' => $file_saved->fid,
    'uri' => services_resource_uri(array('file', $file_saved->fid)),
  );
}

/**
 * Adds new files and returns the files array.
 *
 * @return
 *   Array of file objects with URIS to access them
 */
function _file_resource_create_raw() {
  $files = array();
  foreach ($_FILES['files']['name'] as $field_name => $file_name) {
    // Sanitize the user-input file name before saving to the file system.
    $_FILES['files']['name'][$field_name] = _services_file_check_name_extension($file_name);

    // file_save_upload() validates the file extension and name's length. File
    // size is limited by the upload_max_filesize directive in php.ini.
    $scheme = file_default_scheme();
    // Set file validators: allowed extension
    $validators = array();
    if ($extensions = variable_get('services_allowed_extensions', SERVICES_ALLOWED_EXTENSIONS)) {
      $validators['file_validate_extensions'] = $extensions;
    }
    $file = file_save_upload($field_name, $validators, "$scheme://");

    if (!empty($file->fid)) {
      // Change the file status from temporary to permanent.
      $file->status = FILE_STATUS_PERMANENT;
      file_save($file);

      // Required to be able to reference this file.
      file_usage_add($file, 'services', 'files', $file->fid);

      $files[] = array(
        'fid' => $file->fid,
        'uri' => services_resource_uri(array('file', $file->fid)),
      );
    }
    else {
      return services_error(t('An unknown error occured'), 500);
    }
  }
  return $files;
}
/**
 * Get a given file
 *
 * @param $fid
 *   Number. File ID
 * @param $include_file_contents
 *   Bool Whether or not to include the base64_encoded version of the file.
 * @param $get_image_style
 *   Bool Whether or not to provide image style paths.
 * @return
 *   The file
 */
function _file_resource_retrieve($fid, $include_file_contents, $get_image_style) {
  if ($file = file_load($fid)) {
    $filepath = $file->uri;

    // Convert the uri to the external url path provided by the stream wrapper.
    $file->uri_full = file_create_url($file->uri);

    // Provide a path in the form sample/test.txt.
    $file->target_uri = file_uri_target($file->uri);

    if ($include_file_contents) {
      $file->file = base64_encode(file_get_contents(drupal_realpath($filepath)));
    }

    $file->image_styles = array();
    // Add image style information if available.
    if ($get_image_style && !empty($file->uri) && strpos($file->filemime, 'image') === 0) {
      foreach (image_styles() as $style) {
        $style_name = $style['name'];
        $file->image_styles[$style_name] = image_style_url($style_name, $file->uri);
      }
    }
    return $file;
  }
}

/**
 * Delete a file.
 *
 * @param $fid
 *   Unique identifier of the file to delete.
 * @return bool
 *   Whether or not the delete was successful.
 */
function _file_resource_delete($fid) {
  if ($file = file_load($fid)) {
    file_usage_delete($file, 'services');
    return file_delete($file);
  }
  return FALSE;
}

/**
 * Return an array of optionally paged fids baed on a set of criteria.
 *
 * An example request might look like
 *
 * http://domain/endpoint/file?fields=fid,filename&parameters[fid]=7&parameters[uid]=1
 *
 * This would return an array of objects with only fid and filename defined, where
 * fid = 7 and uid = 1.
 *
 * @param $page
 *   Page number of results to return (in pages of 20).
 * @param $fields
 *   The fields you want returned.
 * @param $parameters
 *   An array containing fields and values used to build a sql WHERE clause
 *   indicating items to retrieve.
 * @param $page_size
 *   Integer number of items to be returned.
 * @param $options
 *   Additional query options.
 * @return
 *   An array of file objects.
 *
 * @see _node_resource_index() for more notes
 **/
function _file_resource_index($page, $fields, $parameters, $page_size, $options = array()) {
  $file_select = db_select('file_managed', 't');

  services_resource_build_index_query($file_select, $page, $fields, $parameters, $page_size, 'file', $options);

  $results = services_resource_execute_index_query($file_select);

  // Put together array of matching files to return.
  return services_resource_build_index_list($results, 'file', 'fid');
}

/**
 * Access check callback for file controllers.
 */
function _file_resource_access($op = 'view', $args = array()) {
  // Adds backwards compatability with regression fixed in #1083242
  if (isset($args[0])) {
    $args[0] = _services_access_value($args[0], 'file');
  }

  global $user;

  if (($op != 'create' && $op != 'create_raw') && $op != 'index') {
    $file = file_load($args[0]);
  } else if ($op == 'create' && $op != 'create_raw') {
    $file = (object)$args[0];
  }
  if (empty($file) && $op != 'index' && ($op != 'create' && $op != 'create_raw')) {
    return services_error(t('There is no file with ID @fid', array('@fid' => $args[0])), 406);
  }
  switch ($op) {
    case 'view':
      if (user_access('get any binary files')) {
        return TRUE;
      }
      return $file->uid == $user->uid && user_access('get own binary files');
      break;
    case 'create':
    case 'create_raw':
      return user_access('save file information');
    case 'delete':
      return $file->uid == $user->uid && user_access('save file information');
      break;
    case 'index':
      if (user_access('get any binary files')) {
        return TRUE;
      }
  }

  return FALSE;
}

function _file_resource_node_access($op = 'view', $args = array()) {
  global $user;
  if (user_access('get any binary files')) {
    return TRUE;
  }
  elseif ($node = node_load($args[0])) {
    return $node->uid == $user->uid && user_access('get own binary files');
  }
  return FALSE;
}

/**
 * Sanitizes a user-input file URI.
 *
 * @param string $uri
 *   The file URI to sanitize, including the extension, name, path and scheme.
 *
 * @return string
 *   A safe destination URI to save the file.
 */
function _services_file_check_destination_uri($uri) {
  $scheme = strstr($uri, '://', TRUE);
  $path = $scheme ? substr($uri, strlen("$scheme://")) : $uri;

  // Sanitize the file extension, name, path and scheme provided by the user.
  $scheme = _services_file_check_destination_scheme($scheme);
  $path = _services_file_check_destination($path);
  return "$scheme://$path";
}

/**
 * Sanitizes a user-input file path, name and extension.
 *
 * @param string $destination
 *   The file path, name and extension. The path is optional. Exclude the scheme.
 *
 * @return string
 *   A safe file path, name and extension.
 */
function _services_file_check_destination($destination) {
  // Split the path by directory separators for both windows and unix.
  $directories = preg_split('![\\/]+!', trim($destination));

  // Sanitize the filename.
  $name = _services_file_check_name_extension(array_pop($directories));

  // Sanitize the names of each directory.
  $directories = array_filter(array_map('_services_file_check_name', $directories), 'strlen');

  // Join the directory and file names back together.
  $directories[] = $name;
  return implode(DIRECTORY_SEPARATOR, $directories);
}

/**
 * Sanitizes a user-input file name and extension.
 *
 * @param string $name
 *   The file name and extension.
 *
 * @return string
 *   A safe file name and extension.
 */
function _services_file_check_name_extension($name) {
  //Fetch the file extensions set in the variable at the time module is enabled
  $extensions = variable_get('services_allowed_extensions', SERVICES_ALLOWED_EXTENSIONS);

  // Get the part of the name after the last period (".").
  $name = explode('.', $name);
  $last = array_pop($name);

  // Make it lowercase for consistency as much as security.
  $extension = strtolower($last);

  // Is this a whitelisted extension?
  if (!in_array($extension, explode(' ', $extensions))) {
    // No.  Restore it to the name and use the default extension, 'txt'.
    $name[] = $last;
    $extension = 'txt';
  }

  // Sanitize the name, apart from the extension.
  $name = _services_file_check_name(implode('.', $name));

  // Is there still a valid name?
  if (0 === strlen($name)) {
    // No. Use the default file name of 'file'.
    $name = 'file';
  }

  // Munge the non-whitelisted secondary file extensions.
  return file_munge_filename("$name.$extension", $extensions);
}

/**
 * Sanitizes a user-input file or directory name.
 *
 * @param string $name
 *   The file or directory name.
 *
 * @return string
 *   A safe name.
 */
function _services_file_check_name($name) {
  // Punctuation characters that are allowed, but not as first/last character.
  $punctuation = '-_. ';

  $map = array(
    // Replace (groups of) whitespace characters.
    '!\s+!' => ' ',
    // Replace multiple dots.
    '!\.+!' => '.',
    // Remove characters that are not alphanumeric or the allowed punctuation.
    "![^0-9A-Za-z$punctuation]!" => '',
  );

  // Apply the regex replacements. Remove any leading or hanging punctuation.
  return trim(preg_replace(array_keys($map), array_values($map), $name), $punctuation);
}

/**
 * Sanitizes a user-input file scheme.
 *
 * @param string $scheme
 *   The user-provided file scheme.
 *
 * @return string
 *   The user-provided scheme if it is valid and a safe destination to save
 *   files. Otherwise the default scheme, usually "public://".
 */
function _services_file_check_destination_scheme($scheme) {
  // Untrusted users should not be able to write to certain schemes.
  // @todo Use a white list instead?
  // @todo Make this list configurable?
  $unsafe = array('temporary', 'file', 'http', 'https', 'ftp');
  if (!empty($scheme) && !in_array($scheme, $unsafe) && file_stream_wrapper_valid_scheme($scheme)) {
    return $scheme;
  }
  return file_default_scheme();
}
