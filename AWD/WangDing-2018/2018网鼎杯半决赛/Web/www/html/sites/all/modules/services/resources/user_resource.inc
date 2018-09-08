<?php

function _user_resource_definition() {
  $definition = array(
    'user' => array(
      'operations' => array(
        'retrieve' => array(
          'help' => 'Retrieve a user',
          'callback' => '_user_resource_retrieve',
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'resources/user_resource'),
          'access callback' => '_user_resource_access',
          'access arguments' => array('view'),
          'access arguments append' => TRUE,
          'args' => array(
            array(
              'name' => 'uid',
              'type' => 'int',
              'description' => 'The uid of the user to retrieve.',
              'source' => array('path' => 0),
              'optional' => FALSE,
            ),
          ),
        ),

        'create' => array(
          'help' => 'Create a user',
          'callback' => '_user_resource_create',
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'resources/user_resource'),
          'access callback' => '_user_resource_access',
          'access arguments' => array('create'),
          'access arguments append' => FALSE,
          'args' => array(
            array(
              'name' => 'account',
              'type' => 'array',
              'description' => 'The user object',
              'source' => 'data',
              'optional' => FALSE,
            ),
          ),
        ),

        'update' => array(
          'help' => 'Update a user',
          'callback' => '_user_resource_update',
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'resources/user_resource'),
          'access callback' => '_user_resource_access',
          'access arguments' => array('update'),
          'access arguments append' => TRUE,
          'args' => array(
            array(
              'name' => 'uid',
              'type' => 'int',
              'description' => 'Unique identifier for this user',
              'source' => array('path' => 0),
              'optional' => FALSE,
            ),
            array(
              'name' => 'data',
              'type' => 'array',
              'description' => 'The user object with updated information',
              'source' => 'data',
              'optional' => FALSE,
            ),
          ),
        ),

        'delete' => array(
          'help' => 'Delete a user',
          'callback' => '_user_resource_delete',
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'resources/user_resource'),
          'access callback' => '_user_resource_access',
          'access arguments' => array('delete'),
          'access arguments append' => TRUE,
          'args' => array(
            array(
              'name' => 'uid',
              'type' => 'int',
              'description' => 'The id of the user to delete',
              'source' => array('path' => 0),
              'optional' => FALSE,
            ),
          ),
        ),
        'index' => array(
          'help' => 'List all users',
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'resources/user_resource'),
          'callback' => '_user_resource_index',
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
              'default value' => variable_get('services_user_index_page_size', 20),
              'source' => array('param' => 'pagesize'),
            ),
            array(
              'name' => 'options',
              'optional' => TRUE,
              'type' => 'array',
              'description' => 'Additional query options.',
              'default value' => array(
                'orderby' => array(
                  'created' => 'DESC'
                )
              ),
              'source' => array('param' => 'options'),
            ),
          ),
          'access arguments' => array('access user profiles'),
          'access arguments append' => FALSE,
        ),
      ),
      'actions' => array(
        'login' => array(
          'help' => 'Login a user for a new session',
          'callback' => '_user_resource_login',
          'args' => array(
            array(
              'name' => 'username',
              'type' => 'string',
              'description' => 'A valid username',
              'source' => array('data' => 'username'),
              'optional' => FALSE,
            ),
            array(
              'name' => 'password',
              'type' => 'string',
              'description' => 'A valid password',
              'source' => array('data' => 'password'),
              'optional' => FALSE,
            ),
          ),
          'access callback' => 'services_access_menu',
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'resources/user_resource'),
        ),
        'logout' => array(
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'resources/user_resource'),
          'help' => 'Logout a user session',
          'callback' => '_user_resource_logout',
          'access callback' => 'services_access_menu',
        ),
        'token' => array(
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'resources/user_resource'),
          'callback' => '_user_resource_get_token',
          'access callback' => 'services_access_menu',
          'help' => t('Returns the CSRF token.'),
        ),
        'request_new_password' => array(
          'help' => 'Request a new password, given a user name or e-mail address',
          'callback' => '_user_resource_request_new_password',
          'args' => array(
            array(
              'name' => 'name',
              'type' => 'string',
              'description' => 'A valid user name or e-mail address',
              'source' => array('data' => 'name'),
              'optional' => FALSE,
            ),
          ),
          'access callback' => 'services_access_menu',
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'resources/user_resource'),
        ),
        'user_pass_reset' => array(
          'help' => 'Process one time login link and return the pass_reset_{uid} token to be used on user update operation',
          'callback' => '_user_resource_user_pass_reset_login',
          'args' => array(
            array(
              'name' => 'uid',
              'optional' => FALSE,
              'type' => 'int',
              'description' => 'The uid of the user in the operation.',
              'source' => array('data' => 'uid'),
            ),
            array(
              'name' => 'timestamp',
              'optional' => FALSE,
              'type' => 'int',
              'description' => 'The timestamp value from the reset password link.',
              'source' => array('data' => 'timestamp'),
            ),
            array(
              'name' => 'hashed_pass',
              'optional' => FALSE,
              'type' => 'string',
              'description' => 'The hashed pass value from the reset password link.',
              'source' => array('data' => 'hashed_pass'),
            ),
          ),
          'access callback' => 'services_access_menu',
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'resources/user_resource'),
        ),
      ),
      'targeted_actions' => array(
        'cancel' => array(
          'help' => 'Cancel a user',
          'callback' => '_user_resource_cancel',
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'resources/user_resource'),
          'access callback' => '_user_resource_access',
          'access arguments' => array('cancel'),
          'access arguments append' => TRUE,
          'args' => array(
            array(
              'name' => 'uid',
              'optional' => FALSE,
              'source' => array('path' => 0),
              'type' => 'int',
              'description' => 'The id of the user to cancel.',
            ),
          ),
        ),
        'password_reset' => array(
          'access callback' => '_user_resource_access',
          'access arguments' => array('password_reset'),
          'access arguments append' => TRUE,
          'callback' => '_user_resource_password_reset',
          'args' => array(
            array(
              'name' => 'uid',
              'optional' => FALSE,
              'source' => array('path' => 0),
              'type' => 'int',
              'description' => 'The id of the user whose password to reset.',
            ),
          ),
        ),
        'resend_welcome_email' => array(
          'access callback' => '_user_resource_access',
          'access arguments' => array('resend_welcome_email'),
          'access arguments append' => TRUE,
          'callback' => '_user_resource_resend_welcome_email',
          'args' => array(
            array(
              'name' => 'uid',
              'optional' => FALSE,
              'source' => array('path' => 0),
              'type' => 'int',
              'description' => 'The id of the user whose welcome email to resend.',
            ),
          ),
        ),
      ),
    ),
  );

  $definition['user']['actions']['register'] = array_merge($definition['user']['operations']['create'], array(
    'help' => 'Register a user',
  ));

  return $definition;
}

/**
 * Get user details.
 *
 * @param $uid
 *   UID of the user to be loaded.
 *
 * @return
 *   A user object.
 *
 * @see user_load()
 */
function _user_resource_retrieve($uid) {
  $account = user_load($uid);
  if (empty($account)) {
    return services_error(t('There is no user with ID @uid.', array('@uid' => $uid)), 404);
  }

  services_remove_user_data($account);

  //Lets check field_permissions
  $account = services_field_permissions_clean('view', 'user', $account);

  // Everything went right.
  return $account;
}

/**
 * Create a new user.
 *
 * This function uses drupal_form_submit() and as such expects all input to match
 * the submitting form in question.
 *
 * @param $account
 *   A object containing account information. The $account object should
 *   contain, at minimum, the following properties:
 *     - name (user name)
 *     - mail (email address)
 *     - pass (plain text unencrypted password)
 *
 *   These properties can be passed but are optional
 *     - status (0 for blocked, otherwise will be active by default)
 *     - notify (1 to notify user of new account, will not notify by default)
 *
 *  Roles can be passed in a roles property which is an associative
 *  array formatted with '<role id>' => '<role id>', not including
 *  the authenticated user role, which is given by default.
 *
 * @return
 *   The user object of the newly created user.
 */
function _user_resource_create($account) {
  // Adds backwards compatability with regression fixed in #1083242
  $account = _services_arg_value($account, 'account');

  // Load the required includes for saving profile information
  // with drupal_form_submit().
  module_load_include('inc', 'user', 'user.pages');

  // Register a new user.
  $form_state['values'] = $account;

  // Determine the password(s). Passwords may not be available as this callback
  // is used for registration as well.
  $pass1 = '';
  $pass2 = '';
  if (isset($account['pass'])) {
    // For legacy usage, passwords come in as a single string. To match the
    // actual form state value keys used by Drupal, we also can collect two
    // passwords via an array.
    if (is_array($account['pass'])) {
      $pass1 = $account['pass']['pass1'];
      $pass2 = $account['pass']['pass2'];
    }
    else {
      $pass1 = $account['pass'];
      $pass2 = $account['pass'];
    }
  }
  $form_state['values']['pass'] = array(
    'pass1' => $pass1,
    'pass2' => $pass2
  );

  // Set the form state op.
  $form_state['values']['op'] = variable_get('services_user_create_button_resource_create', t('Create new account'));

  // Execute the register form.
  $form_state['programmed_bypass_access_check'] = FALSE;

  drupal_form_submit('user_register_form', $form_state);
  // find and store the new user into the form_state
  if(isset($form_state['values']['uid'])) {
    $form_state['user'] = user_load($form_state['values']['uid']);
  }

  // Error if needed.
  if ($errors = form_get_errors()) {
    return services_error(implode(" ", $errors), 406, array('form_errors' => $errors));
  }
  else {
    $user = array('uid' => $form_state['user']->uid);
    if ($uri = services_resource_uri(array('user', $user['uid']))) {
      $user['uri'] = $uri;
    }
    _user_resource_update_services_user($user['uid'], time());
    return $user;
  }
}

/**
 * Update an existing user.
 *
 * This function uses drupal_form_submit() and as such expects all input to match
 * the submitting form in question.
 *
 * @param $uid
 *   Unique identifier for this user
 * @param $account
 *   Fields to modify for this user.
 *
 * @return
 *   The modified user object.
 */
function _user_resource_update($uid, $account) {
  // Adds backwards compatability with regression fixed in #1083242
  $account = _services_arg_value($account, 'data');

  $account['uid'] = $uid;

  $account_loaded = user_load($uid);

  // Load the required includes for saving profile information
  // with drupal_form_submit().
  module_load_include('inc', 'user', 'user.pages');

  // If a profile category was passed in, use it. Otherwise default
  // to 'account' (for saving core user data.)
  $category = 'account';
  if (isset($account['category'])) {
    $category = $account['category'];
    unset($account['category']);
  }

  // Prepare values for the user profile image.
  if (array_key_exists('picture_upload', $account)) {
    if (is_array($account['picture_upload'])) {
      // Check if it's an array and convert to object.
      $account['picture_upload'] = (object) $account['picture_upload'];
    }
    elseif (is_int($account['picture_upload'])) {
      // Check if it's an integer and get the file object.
      $file_validate = file_validate_is_image($file = file_load($account['picture_upload']));
      if (empty($file_validate)) {
        $account['picture_upload'] = $file;
      }
    }
    elseif (is_string($account['picture_upload'])) {
      // Check if it's an string and get the file object.
      $file_validate = file_validate_is_image($file = file_load((int) $account['picture_upload']));
      if (empty($file_validate)) {
        $account['picture_upload'] = $file;
      }
    }
  }

  // Drop any passed in values into the $account var. Anything
  // unused by the form just gets ignored. We handle roles and
  // password separately.
  foreach ($account as $key => $value) {
    if ($key != 'pass' && $key != 'roles') {
      $form_state['values'][$key] = $value;
    }
  }

  // Prepare values of roles. Check user's permission before allowing changes to roles.
  if (!isset($account['roles']) || !user_access('administer users')) {
    $account['roles'] = $account_loaded->roles;
  }
  foreach ($account['roles'] as $key => $value) {
    if (!empty($value)) {
      $form_state['values']['roles'][$key] = $key;
    }
  }
  unset($form_state['values']['roles'][2]);

  // Prepare values for password.
  if (isset($account['pass'])) {
    $form_state['values']['pass']['pass1'] = $account['pass'];
    $form_state['values']['pass']['pass2'] = $account['pass'];
  }

  // If user is changing name, make sure they have permission.
  if (isset($account['name']) && $account['name'] != $account_loaded->name && !(user_access('change own username') || user_access('administer users'))) {
    return services_error(t('You are not allowed to change your username.'), 406);
  }

  $form_state['values']['op'] = variable_get('services_user_save_button_resource_update', t('Save'));
  $form_state['values']['#user_category'] = $category;
  $form_state['values']['#account'] = $account_loaded;
  $form_state['programmed_bypass_access_check'] = FALSE;
  $ret = drupal_form_submit('user_profile_form', $form_state, $account_loaded, $category);

  // Error if needed.
  if ($errors = form_get_errors()) {
    return services_error(implode(" ", $errors), 406, array('form_errors' => $errors));
  }
  else {
    $account = (object) $account;
    services_remove_user_data($account);
    $account = (array) $account;
    _user_resource_update_services_user($uid, time());
    return $account;
  }
}

function _user_resource_update_services_user($uid, $time) {
  //Determine if a row exists, if not that means we are creating the user,
  //If so that means we are updating it.
  $result = db_select('services_user', 'su')
    ->fields('su')
    ->condition('uid', $uid, '=')
    ->execute()
    ->fetchAssoc();
  //check the result
  if (!$result) {
    $id = db_insert('services_user')
      ->fields(array(
        'uid' => $uid,
        'created' => $time,
        'changed' => $time,
      ))
    ->execute();
  } else {
    db_update('services_user')
      ->fields(array(
          'uid' => $uid,
          'changed' => $time,
        )
      )
      ->condition('uid', $uid, '=')
      ->execute();
  }
}


/**
 * Delete a user.
 *
 * @param $uid
 *   UID of the user to be deleted.
 *
 * @see user_delete()
 */
function _user_resource_delete($uid) {
  if ($uid == 1) {
    return services_error(t('The admin user cannot be deleted.'), 403);
  }

  $account = user_load($uid);
  if (empty($account)) {
    return services_error(t('There is no user with ID @uid.', array('@uid' => $uid)), 404);
  }
  user_delete($uid);

  // Everything went right.
  return TRUE;
}

/**
 * Cancel a user.
 *
 * @param $uid
 *   UID of the user to be canceled.
 *
 * @see user_cancel()
 */
function _user_resource_cancel($uid) {
  if ($uid == 1) {
    return services_error(t('The admin user cannot be canceled.'), 403);
  }
  $account = user_load($uid);
  if (empty($account)) {
    return services_error(t('There is no user with ID @uid.', array('@uid' => $uid)), 404);
  }
  $edit = array(
    'user_cancel_notify' => isset($account->data['user_cancel_notify']) ? $account->data['user_cancel_notify'] : variable_get('user_mail_status_canceled_notify', FALSE),
  );
  // This defult setting is defined under "admin/config/people/accounts".
  $default_method = variable_get('user_cancel_method', 'user_cancel_block');

  // Modules use hook_user_delete() to respond to deletion.
  if ($default_method != 'user_cancel_delete') {
    // Allow modules to add further sets to this batch.
    module_invoke_all('user_cancel', $edit, $account, $default_method);
  }

  _user_cancel($edit, $account, $default_method);

  // Everything went right.
  return TRUE;
}

/**
 * Login a user using the specified credentials.
 *
 * Note this will transfer a plaintext password.
 *
 * @param $username
 *   Username to be logged in.
 * @param $password
 *   Password, must be plain text and not hashed.
 *
 * @return
 *   A valid session object.
 */
function _user_resource_login($username, $password) {
  global $user;

  if ($user->uid) {
    // user is already logged in
    return services_error(t('Already logged in as @user.', array('@user' => $user->name)), 406);
  }

  // Check if account is active.
  if (user_is_blocked($username)) {
    return services_error(t('The username %name has not been activated or is blocked.', array('%name' => $username)), 403);
  }

  // Emulate drupal native flood control: check for flood condition.
  $flood_state = array();
  if (variable_get('services_flood_control_enabled', TRUE)) {
    $flood_state = _user_resource_flood_control_precheck($username);
  }

  // Only authenticate if a flood condition was not detected.
  if (empty($flood_state['flood_control_triggered'])) {
    $uid = user_authenticate($username, $password);
  }
  else {
    $uid = FALSE;
  }

  // Emulate drupal native flood control: register flood event, and throw error
  // if a flood condition was previously detected
  if (variable_get('services_flood_control_enabled', TRUE)) {
    $flood_state['uid'] = $uid;
    _user_resource_flood_control_postcheck($flood_state);
  }

  if ($uid) {
    $user = user_load($uid);
    if ($user->uid) {
      user_login_finalize();

      $return = new stdClass();
      $return->sessid = session_id();
      $return->session_name = session_name();
      $return->token = drupal_get_token('services');

      $account = clone $user;

      services_remove_user_data($account);

      $return->user = $account;

      return $return;
    }
  }
  watchdog('user', 'Invalid login attempt for %username.', array('%username' => $username));
  return services_error(t('Wrong username or password.'), 401);
}

/**
 * Logout the current user.
 */
function _user_resource_logout() {
  global $user;

  if (!$user->uid) {
    // User is not logged in
    return services_error(t('User is not logged in.'), 406);
  }

  watchdog('user', 'Session closed for %name.', array('%name' => $user->name));

  $original_session_state = drupal_save_session();
  drupal_save_session(TRUE);

  // Destroy the current session.
  module_invoke_all('user_logout', $user);
  session_destroy();

  // Load the anonymous user.
  $user = drupal_anonymous_user();

  drupal_save_session($original_session_state);

  return TRUE;
}

/**
 *  Update the current user logout callback to the new callback with a better return value.
 */
function _user_resource_logout_update_1_1() {
  $new_set = array(
    'callback' => '_user_resource_logout_1_1',
  );
  return $new_set;
}

/**
 * Logs out the currently logged in user and returns the new user object.
 */
function _user_resource_logout_1_1() {
  global $user;

  if (!$user->uid) {
    // User is not logged in
    return services_error(t('User is not logged in.'), 406);
  }

  watchdog('user', 'Session closed for %name.', array('%name' => $user->name));

  // Destroy the current session.
  module_invoke_all('user_logout', $user);
  session_destroy();

  // Load the anonymous user.
  $user = drupal_anonymous_user();

  return $user;
}

/**
 * Request a new password given a user name or e-mail address.
 *
 * @param $name
 *   The username or e-mail address of the requesting account.
 *
 * @see https://api.drupal.org/api/drupal/modules!user!user.pages.inc/function/user_pass_validate/7
 * @see https://api.drupal.org/api/drupal/modules!user!user.pages.inc/function/user_pass_submit/7
 */
function _user_resource_request_new_password($name) {
  $name = trim($name);
  // Try to load by email.
  $users = user_load_multiple(array(), array('mail' => $name, 'status' => '1'));
  $account = reset($users);
  if (!$account) {
    // No success, try to load by name.
    $users = user_load_multiple(array(), array('name' => $name, 'status' => '1'));
    $account = reset($users);
  }
  if (!isset($account->uid)) {
    return services_error(t('Sorry, %name is not recognized as a user name or an e-mail address.', array('%name' => $name)), 406);
  }
  // Mail one time login URL and instructions using current language.
  global $language;
  $mail = _user_mail_notify('password_reset', $account, $language);
  if (!empty($mail)) {
    watchdog('user', 'Password reset instructions mailed to %name at %email.', array('%name' => $account->name, '%email' => $account->mail));
    return TRUE;
  }
  else {
    return FALSE;
  }
}

function _user_resource_user_pass_reset_login($uid, $timestamp, $hashed_pass) {
  global $user;

  // When processing the one-time login link, we have to make sure that a user
  // isn't already logged in.
  if ($user->uid) {
    // The existing user is already logged in.
    if ($user->uid == $uid) {
      return services_error(t('You are logged in as %user. Please edit your profile to update your password.', array('%user' => $user->name)), 403);
    }
    // A different user is already logged in on the computer.
    else {
      $reset_link_account = user_load($uid);
      if (!empty($reset_link_account)) {
        return services_error(t('Another user (%other_user) is already logged into the site on this computer, but you tried to use a one-time link for user %resetting_user. Please logout and try using the link again.', array('%other_user' => $user->name, '%resetting_user' => $reset_link_account->name)), 405);
      }
      else {
        // Invalid one-time link specifies an unknown user.
       return services_error(t('The one-time login link you clicked is invalid.'), 406);
      }
    }
  }
  else {
    // Time out, in seconds, until login URL expires. Defaults to 24 hours =
    // 86400 seconds.
    $timeout = variable_get('user_password_reset_timeout', 86400);
    $current = REQUEST_TIME;
    // Some redundant checks for extra security ?
    $users = user_load_multiple(array($uid), array('status' => '1'));
    if ($timestamp <= $current && $account = reset($users)) {
      // No time out for first time login.
      if ($account->login && $current - $timestamp > $timeout) {
        return services_error(t('You have tried to use a one-time login link that has expired. Please request a new one.'), 406);
      }
      elseif ($account->uid && $timestamp >= $account->login && $timestamp <= $current && $hashed_pass == user_pass_rehash($account->pass, $timestamp, $account->login, $account->uid)) {
        // Set the new user.
        $user = $account;
        // user_login_finalize() also updates the login timestamp of the
        // user, which invalidates further use of the one-time login link.
        user_login_finalize();
        watchdog('user', 'User %name used one-time login link at time %timestamp.', array('%name' => $account->name, '%timestamp' => $timestamp));
        // Let the user's password be changed without the current password check.
        $token = drupal_random_key();
        $_SESSION ['pass_reset_' . $user->uid] = $token;
        return array('pass_reset_token' => $token, 'message' => t('You have just used your one-time login link. It is no longer necessary to use this link to log in. <strong>Please change your password</strong>.'));
      }
      else {
        return services_error(t('You have tried to use a one-time login link that has either been used or is no longer valid. Please request a new one.'), 406);
      }
    }
    else {
      // Deny access, no more clues.
      // Everything will be in the watchdog's URL for the administrator to check.
      return services_error(t('Access denied.'), 403);
    }
  }
}

/**
 * Send a password reset email for the specified user.
 */
function _user_resource_password_reset($uid) {
  global $language;

  $account = user_load($uid);
  if (empty($account)) {
    return services_error(t('There is no user with ID @uid.', array('@uid' => $uid)), 404);
  }

  // Mail one time login URL and instructions using current language.
  $mail = _user_mail_notify('password_reset', $account, $language);
  if (!empty($mail)) {
    watchdog('user', 'Password reset instructions mailed to %name at %email.', array('%name' => $account->name, '%email' => $account->mail));
  }
  else {
    watchdog('user', 'There was an error re-sending password reset instructions mailed to %name at %email', array('%name' => $account->name, '%email' => $account->mail));
  }
  // Everything went right.
  return TRUE;
}

/**
 * Send a welcome email for the specified user.
 */
function _user_resource_resend_welcome_email($uid) {
  global $language;

  $account = user_load($uid);
  if (empty($account)) {
    return services_error(t('There is no user with ID @uid.', array('@uid' => $uid)), 404);
  }

  $user_register = variable_get('user_register', 2);
  switch ($user_register) {
    case USER_REGISTER_ADMINISTRATORS_ONLY:
      $op = 'register_admin_created';
      break;
    case USER_REGISTER_VISITORS:
      $op = 'register_no_approval_required';
      break;
    case USER_REGISTER_VISITORS_ADMINISTRATIVE_APPROVAL:
      $op = 'register_pending_approval';
  }

  // Mail the welcome emaiil using current language.
  $mail = _user_mail_notify($op, $account, $language);
  if (!empty($mail)) {
    watchdog('user', 'Welcome message has been re-sent to %name at %email.', array('%name' => $account->name, '%email' => $account->mail));
  }
  else {
    watchdog('user', 'There was an error re-sending welcome message to %name at %email', array('%name' => $account->name, '%email' => $account->mail));
  }
  // Everything went right.
  return TRUE;
}

/**
 * Return an array of optionally paged uids baed on a set of criteria.
 *
 * An example request might look like
 *
 * http://domain/endpoint/user?fields=uid,name,mail&parameters[uid]=1
 *
 * This would return an array of objects with only uid, name and mail defined,
 * where uid = 1.
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
 * @return
 *   An array of user objects.
 *
 * @see _node_resource_index() for more notes
 */
function _user_resource_index($page, $fields, $parameters, $page_size, $options = array()) {
  $user_select = db_select('users', 't');

  services_resource_build_index_query($user_select, $page, $fields, $parameters, $page_size, 'user', $options);

  $results = services_resource_execute_index_query($user_select);

  return services_resource_build_index_list($results, 'user', 'uid');
}

/**
 * Access check callback for user resource.
 */
function _user_resource_access($op = 'view', $args = array()) {
  // Adds backwards compatability with regression fixed in #1083242
  if (isset($args[0])) {
    $args[0] = _services_access_value($args[0], array('account', 'data'));
  }

  // Check if the user exists if appropriate.
  if ($op != 'create' && $op != 'register' ) {
    $account = user_load($args[0]);
    if (!$account) {
      return services_error(t('There is no user with ID @uid.', array('@uid' => $args[0])), 404);
    }
  }

  global $user;
  switch ($op) {
    case 'view':
      return user_view_access($account);
    case 'update':
      return ($user->uid == $account->uid || user_access('administer users'));
    case 'create':
    case 'register':
      if (!$user->uid && variable_get('user_register', USER_REGISTER_VISITORS_ADMINISTRATIVE_APPROVAL) != USER_REGISTER_ADMINISTRATORS_ONLY) {
        return TRUE;
      }
      else {
        return user_access('administer users');
      }
    case 'password_reset':
      return TRUE;
    case 'delete':
    case 'cancel':
    case 'resend_welcome_email':
      return user_access('administer users');
  }
}

/**
 * Changes the user/login endpoint to accept the same parameters as the user/register endpoint, namely
 * "name" instead of "username" and "pass" instead of "password"
 */
function _user_resource_login_update_1_1() {
  $new_set = array(
    'args' => array(
      array(
        'name' => 'name',
        'type' => 'string',
        'description' => 'A valid username',
        'source' => array('data' => 'name'),
        'optional' => FALSE,
      ),
      array(
        'name' => 'pass',
        'type' => 'string',
        'description' => 'A valid password',
        'source' => array('data' => 'pass'),
        'optional' => FALSE,
      ),
    ),
  );
  return $new_set;
}

function _user_resource_get_token() {
  return array('token' => drupal_get_token('services'));
}



/**
 * Emulate native Drupal flood control, phase 1.
 *
 * This function checks for a flood condition, and determines the identifier
 * for user based flood checks. This is done prior to user authentication.
 *
 * @param string $username
 *   The name of the user who is attempting to log in.
 * @return array
 *   An array containing zero or more of the following keys:
 *   - flood_control_triggered: either 'user' or 'ip' if a flood condition
 *     was detected.
 *   - flood_control_user_identifier: the identifier to use to register
 *     user-based flood events.
 *
 * @see _user_resource_flood_control_postcheck().
 * @see user_login_authenticate_validate().
 */
function _user_resource_flood_control_precheck($username) {
  $flood_state = array();
  // Do not allow any login from the current user's IP if the limit has been
  // reached. Default is 50 failed attempts allowed in one hour. This is
  // independent of the per-user limit to catch attempts from one IP to log
  // in to many different user accounts.  We have a reasonably high limit
  // since there may be only one apparent IP for all users at an institution.
   if (!flood_is_allowed('failed_login_attempt_ip', variable_get('user_failed_login_ip_limit', 50), variable_get('user_failed_login_ip_window', 3600))) {
    $flood_state['flood_control_triggered'] = 'ip';
   }
  else {
    $account = db_query("SELECT * FROM {users} WHERE name = :name AND status = 1", array(':name' => $username))->fetchObject();
    if ($account) {
      if (variable_get('user_failed_login_identifier_uid_only', FALSE)) {
        // Register flood events based on the uid only, so they apply for any
        // IP address. This is the most secure option.
        $identifier = $account->uid;
      }
      else {
        // The default identifier is a combination of uid and IP address. This
        // is less secure but more resistant to denial-of-service attacks that
        // could lock out all users with public user names.
        $identifier = $account->uid . '-' . ip_address();
      }
      $flood_state['flood_control_user_identifier'] = $identifier;

      // Don't allow login if the limit for this user has been reached.
      // Default is to allow 5 failed attempts every 6 hours.
      if (!flood_is_allowed('failed_login_attempt_user', variable_get('user_failed_login_user_limit', 5), variable_get('user_failed_login_user_window', 21600), $identifier)) {
        $flood_state['flood_control_triggered'] = 'user';
      }
     }
  }
  return $flood_state;
}

/**
+ * Emulate native Drupal flood control, phase 2.
+ *
+ * This function records a failed login attempt, and triggers an error if a
+ * flood condition was previously detected.
+ *
+ * @param array $flood_state
+ *   An array of flood information as returned by
+ *   _user_resource_flood_control_precheck().
+ *
+ * @throws ServicesException
+ *   If a flood condition was previously detected.
+ *
+ * @see _user_resource_flood_control_precheck().
+ * @see user_login_final_validate().
+ */
function _user_resource_flood_control_postcheck($flood_state) {
  if (empty($flood_state['uid'])) {
    // Always register an IP-based failed login event.
    flood_register_event('failed_login_attempt_ip', variable_get('user_failed_login_ip_window', 3600));
    // Register a per-user failed login event.
    if (isset($flood_state['flood_control_user_identifier'])) {
      flood_register_event('failed_login_attempt_user', variable_get('user_failed_login_user_window', 21600), $flood_state['flood_control_user_identifier']);
    }
    if (isset($flood_state['flood_control_triggered'])) {
      if ($flood_state['flood_control_triggered'] == 'user') {
        services_error(t('Account is temporarily blocked.'), 406);
      }
      else {
        // We did not find a uid, so the limit is IP-based.
        services_error(t('This IP address is temporarily blocked.'), 406);
      }
    }
  }
  elseif (isset($flood_state['flood_control_user_identifier'])) {
    // Clear past failures for this user so as not to block a user who might
    // log in and out more than once in an hour.
    flood_clear_event('failed_login_attempt_user', $flood_state['flood_control_user_identifier']);
  }
}
