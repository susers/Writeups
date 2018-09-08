<?php

function services_drush_command() {
  $items  = array();
  $items['services-security-update-1'] = array(
    'description' => dt('Run the updates for services 7.x-3.9 security update.'),
    'arguments'   => array(
      'op'        => 'op',
    ),
    'examples' => array(
      'drush services-security-update-1 reset-users-since-date' => 'Reset password of users that have registered since August 30th, 2013.',
      'drush services-security-update-1 reset-users-with-password_one' => 'Reset password of users which are set to "1".',
      'drush services-security-update-1 do-nothing' => "Don't reset any user password.",
    ),
  );
  return $items;
}

function services_drush_help($section) {
  switch ($section) {
    case 'drush:services_security_update_1':
      return dt("services_security_update_1 op");
  }
}

function drush_services_security_update_1($op) {
  // Convert $op to match functions.
  $allowed_ops = array(
    'reset-users-since-date' => 'services_security_update_reset_users_since_date',
    'reset-users-with-password_one' => 'services_security_update_reset_users_with_password_one',
    'do-nothing' => 'services_security_update_do_nothing',
  );
  $op = $allowed_ops[$op];

  if (!$op) {
    drush_print(dt('This operation does not exist or is not allowed, see drush help services-security-update-1.'));
  }
  else {
    drush_print(dt('Running operation: ') . $op);
    $drush = TRUE;
    services_security_setup_batch($op, $drush);
  }
}
