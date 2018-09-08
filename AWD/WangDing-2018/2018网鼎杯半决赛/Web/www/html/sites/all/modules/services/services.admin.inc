<?php

function services_security_admin_form($form, &$form_state) {
  // Check to see if anything has been stored.
  if ($form_state['rebuild']) {
    $form_state['input'] = array();
  }
  if (empty($form_state['storage'])) {
    // No step has been set so start with the first.
    $form_state['storage'] = array(
      'step' => 'services_security_form_decision',
    );
  }

   // Return the current form
  $function = $form_state['storage']['step'];
  $form = $function($form, $form_state);
  return $form;
}
function services_security_form_decision($form, &$form_state) {
  $values = '';
  if(!empty($form_state['storage'])) {
      $values = $form_state['storage'];
  }
  $notice = '<div style="color:red;"><strong>A Services security update mitigation step has already been run on this site.</strong></div>';
  $services_security_update = variable_get('services_security_update_1', FALSE);
  //If services security has not run before, lets set the notice to nothing.
  if(!$services_security_update) {
    $notice = '';
  }
  $form['markup'] = array(
    '#markup' => $notice . 'Due to a bug in services, user accounts registered through services\' user_resource have been created with the password "1" since August 2013.

    <p>Services provides the following options to mitigate this vulnerability on your site:
      <ol>
        <li>Invalidate the password of all user accounts that have been registered after this bug was introduced. This will force all users who registered after August 30th, 2013 to reset their password, regardless of how those accounts were created. <strong>This is the safest option</strong>.</li>
        <li>Invalidate the password of all user accounts which currently have their password set to "1". This will require users who attempted to register to reset their password.
        This option will take a long time to run especially if you have a lot of users on your site.
        <strong style="color:red;">This option may not be effective from a security perspective because an attacker may have already changed passwords to something other than "1".</strong></li>
        <li>Do nothing.</li>
      </ol>
    </p>

    <p>There are many reasons why the third option (do nothing) would be suitable to you:
      <ol>
        <li>Services User Resource was never enabled</li>
        <li>Anonymous users did not have permission to register</li>
        <li>A custom/contrib resource was enabled that users used in order to register</li>
        <li>You have an SSO provider and users do not register through Services</li>
        <li>Users were never registered through Services because the API was not public</li>
        <li>You were using a version of Services older than 7.x-3.6 and never used Services 7.x-3.6 on your site.</li>
      </ol>
    </p>

      <p><strong>Things you should do as general best practices:</strong>
      <ol>
        <li>Check all accounts that have administrator access and verify they are accounts you know. If not, its recommended to disable those accounts</li>
        <li>If you choose option 1 or 2 you should let your users know that they will need to request a password reset via the regular form at user/password.</li>
      </ol></p>',
  );
  $form['fieldset'] = array(
   '#type' => 'fieldset',
   '#title' => t('I understand. Let\'s do something about it!'),
   '#collapsible' => TRUE,
   '#collapsed' => TRUE,
  );
  $form['fieldset']['security_options'] = array(
    '#type'     => 'radios',
    '#title'    => t('Please select from the following options'),
    '#options'  => array(
      t('Invalidate password of all user accounts created after August 30th, 2013 (safest)'),
      t('Invalidate password of all user accounts with a password of "1".'),
      t('Do nothing'),
    ),
    '#default_value' => isset($values['security_options']) ? $values['security_options'] : 2,
    '#required' => TRUE,
  );

  $form['fieldset']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
  );
  if(isset($form_state['decided_security_option'])) {
    unset($form['fieldset']);
  }
  return $form;
}
function services_security_admin_form_submit($form, &$form_state) {
  $values = $form_state['values'];
  if (isset($values['back']) && $values['op'] == $values['back']) {
    // Moving back in form.
    $step = $form_state['storage']['step'];
    // Call current step submit handler if it exists to unset step form data.
    if (function_exists($step . '_submit')) {
      $function = $step . '_submit';
      $function($form, $form_state);
    }
    // Remove the last saved step so we use it next.
    $last_step = array_pop($form_state['storage']['steps']);
    $form_state['storage']['step'] = $last_step;
  }
  else {
    // Record step.
    $step = $form_state['storage']['step'];
    $form_state['storage']['steps'][] = $step;
    // Call step submit handler if it exists.
    if (function_exists($step . '_submit')) {
      $function = $step . '_submit';
      $function($form, $form_state);
    }
  }
  return;
}

function services_security_form_confirm($form, &$form_state) {
  $values = array();
  if (!empty($form_state['storage'])) {
    $values = $form_state['storage'];
  }
  switch($values['security_options']) {
    case 0:
      $markup = 'All user account created since August 30th, 2013 will have their password invalidated, this cannot be undone.';
      break;
    case 1:
      $markup = 'All user account which still have their password set to "1" will have their password invalidated, this cannot be undone.';
      break;
    case 2:
      $markup = 'Do nothing.';
      break;
  }

  $form['markup'] = array(
    '#markup' => $markup . '<br>',
  );
  $form['back'] = array(
    '#type' => 'submit',
    '#value' => t('Back'),
    '#limit_validation_errors' => array(),
    '#submit' => array('services_security_form_confirm_submit'),
  );
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Confirm'),
  );
  return $form;
}

function services_security_form_confirm_submit($form, &$form_state) {
  $values = $form_state['values'];
  $form_state['rebuild'] = TRUE;

  //If they hit back, lets send them back.
  if (isset($values['back']) && $values['op'] == $values['back']) {
    $form_state['storage']['step'] = 'services_security_form_decision';
  } else {
    //Setup the batch processing.
    $security_options = services_security_get_update_options();
    $op = $security_options[$form_state['storage']['security_options']];
    services_security_setup_batch($op, FALSE);
  }
}
//
function services_security_form_decision_submit($form, &$form_state) {
  $values = $form_state['values'];
  $form_state['rebuild'] = TRUE;
  $form_state['storage']['security_options'] = check_plain($form_state['values']['security_options']);
  $form_state['storage']['step'] = 'services_security_form_confirm';
}

function theme_services_resource_table($variables) {
  $table = $variables['table'];

  drupal_add_css(drupal_get_path('module', 'services') . '/css/services.admin.css');
  drupal_add_js(drupal_get_path('module', 'services') . '/js/services.admin.js');
  drupal_add_js('misc/tableselect.js');

  // Create header for resource selection table.
  $header = array(
    array('class' => array('select-all')),
    array('data' => t('Resource'), 'class' => array('resource_method')),
    array('data' => t('Settings'), 'class' => array('resource_settings')),
    array('data' => t('Alias'), 'class' => array('resource_alias')),
  );

  // Define the images used to expand/collapse the method groups.
  $js = array(
    'images' => array(
      'collapsed' => theme('image', array('path' => 'misc/menu-collapsed.png', 'alt' => t('Expand'), 'title' => t('Expand'))) . ' <a href="#" class="resource-collapse">(' . t('Expand') . ')</a>',
      'expanded' => theme('image', array('path' => 'misc/menu-expanded.png', 'alt' => t('Collapse'), 'title' => t('Collapse'))) . ' <a href="#" class="resource-collapse">(' . t('Collapse') . ')</a>',
    ),
  );

  // Cycle through each method group and create a row.
  $rows = array();
  foreach (element_children($table) as $key) {
    $element = &$table[$key];
    $row = array();

    // Make the class name safe for output on the page by replacing all
    // non-word/decimal characters with a dash (-).
    $method_class = 'services-' . strtolower(trim(preg_replace("/[^\w\d]/", "-", $key)));

    // Select the right "expand"/"collapse" image, depending on whether the
    // category is expanded (at least one method selected) or not.
    $collapsed = !empty($element['#collapsed']);

    // Place-holder for checkboxes to select group of methods.
    $row[] = array('id' => $method_class, 'class' => array('resource-select-all'));

    // Expand/collapse image and group title.
    $row[] = array(
      'data' => '<div class="resource-image" id="resource-method-group-' . $method_class . '" data-resource="' . $method_class . '"></div>' .
        '<label for="' . $method_class . '-select-all" class="resource-group-label">' . $key . '</label>',
      'class' => array('resource-group-label'),
    );

    $row[] = array(
      'data' => '&nbsp;',
      'class' => array('resource-group-description'),
    );
    $row[] = array(
      'data' => drupal_render($element['alias']),
      'class' => array('resource-group-alias'),
    );
    $rows[] = array('data' => $row, 'class' => array('resource-group'));

    // Add individual methods to group.
    $current_js = array(
      'methodClass' => $method_class . '-method',
      'collapsed' => $collapsed,
      'clickActive' => FALSE,
    );

    // Cycle through each method within the current group.
    foreach (element_children($element) as $class) {
      if($class != 'alias') {
        $class_element = $element[$class];

        // Add group (class) header row.
        $rows[] = array('data' => array(NULL, array(
          'data' => '<label>' . $class_element['#title'] . '</label>',
          'class' => array('resource-operation-class'),
        ), NULL, NULL), 'class' => array($method_class . '-method', 'resource-operation-class'));

        foreach (element_children($class_element) as $op_name) {
          $row = array();
          $method = $class_element[$op_name];

          // Store method title and description so that checkbox won't render them.
          $title = $method['#title'];
          $description = $method['#description'];

          $method['#title_display'] = 'invisible';
          $method['enabled']['#title_display'] = 'invisible';
          unset($method['#description']);

          // Test name is used to determine what methods to run.
          $method['#name'] = $class;

          $row[] = array(
            'data' => drupal_render($method['enabled']),
            'class' => array('resource-method-select'),
          );
          $row[] = array(
            'data' => '<label for="' . $method['#id'] . '">' . $title . '</label>' . '<div class="description">' . $description . '</div>',
            'class' => array('resource-method-description'),
          );
          $row[] = array(
            'data' => drupal_render($method['settings']),
            'class' => array('resource-method-settings'),
          );
          $row[] = array(
            'data' => '<div class="alias">&nbsp;</div>',
            'class' => array('resource-method-alias'),
          );
          $rows[] = array('data' => $row, 'class' => array($method_class . '-method', 'resource-method'));
        }
      }

    }
    $js['resources'][$method_class] = $current_js;
    unset($table[$key]);
  }

  // Add js array of settings.
  drupal_add_js(array('services' => $js), 'setting');

  if (empty($rows)) {
    return '<strong>' . t('No resourcess to display.') . '</strong>';
  }
  else {
    return theme('table', array('header' => $header, 'rows' => $rows, 'attributes' => array('id' => 'resource-form-table')));
  }
}
