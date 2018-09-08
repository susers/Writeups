<?php

/**
 * @file
 * Ctools context type plugin to hold the current language context.
 */

/**
 * Plugins are described by creating a $plugin array which will be used
 * by the system that includes this file.
 */
$plugin = array(
  'title' => t('Language'),
  'description' => t('Language object.'),
  'context' => 'ctools_context_language_create',
  'context name' => 'language',
  'keyword' => 'language',

  // Provides a list of items which are exposed as keywords.
  'convert list' => 'ctools_language_context_convert_list',
  // Convert keywords into data.
  'convert' => 'ctools_language_context_convert',

  'placeholder form' => array(
    '#type' => 'textfield',
    '#description' => t('Enter a valid langcode.'),
    '#value' => $GLOBALS['language']->language,
  ),

  // Provide settings for the context.
  'edit form' => 'ctools_context_language_settings_form',
  'settings' => ctools_context_language_conf_defaults(),
);

/**
 * Ensures a full populated settings array with sane defaults.
 *
 * @param mixed $conf
 *   Array with the user defined settings, or a string identifying a language.
 *
 * @return array
 *   Array with all available settings.
 */
function ctools_context_language_conf_defaults($conf = array()) {
  if (!is_array($conf)) {
    $conf = array(
      'preset_langcode' => (string) $conf,
    );
  }

  return $conf + array(
    'enable_cache_argument' => TRUE,
    'language_type' => 'language',
    'preset_langcode' => $GLOBALS['language']->language,
  );
}

/**
 * Create a context, either from manual configuration or the current language.
 */
function ctools_context_language_create($empty, $data = NULL, $conf = FALSE) {
  $context = new ctools_context('language');
  $context->plugin = 'language';
  if ($empty) {
    return $context;
  }
  $context->title = t('Language');

  $settings = ctools_context_language_conf_defaults($data);
  if ($settings['language_type'] != 'preset') {
    $language_object = $GLOBALS[$settings['language_type']];
  }
  else {
    // Fetch the enabled language objects.
    $languages = language_list('enabled');
    $languages = $languages[1];

    // Set the custom language, but fallback to the interface language.
    $language_object = $GLOBALS['language'];
    if (isset($languages[$settings['preset_langcode']])) {
      $language_object = $languages[$settings['preset_langcode']];
    }
  }
  // If enabled set the argument ot use in the cid.
  if ($settings['enable_cache_argument']) {
    $context->argument = $language_object->language;
  }
  $context->data = $language_object;
  return $context;
}

/**
 * Provide a list of sub-keywords.
 *
 * This is used to provide keywords from the context for use in a content type,
 * pane, etc.
 */
function ctools_language_context_convert_list() {
  $context = new stdClass();
  $context->data = $GLOBALS['language'];
  return array(
    'language' => t('Langcode. E.g. !example', array('!example' => ctools_language_context_convert($context, 'language'))),
    'name' => t('Name. E.g. !example', array('!example' => ctools_language_context_convert($context, 'name'))),
    'native' => t('Native name of the language. E.g. !example', array('!example' => ctools_language_context_convert($context, 'native'))),
    'direction' => t('Text direction 0=LRT, 1=RTL. E.g. !example', array('!example' => ctools_language_context_convert($context, 'direction'))),
    'enabled' => t('Status. E.g. !example', array('!example' => ctools_language_context_convert($context, 'enabled'))),
    'plurals' => t('Number of plural forms. E.g. !example', array('!example' => ctools_language_context_convert($context, 'plurals'))),
    'formula' => t('Plural formula. E.g. !example', array('!example' => ctools_language_context_convert($context, 'formula'))),
    'domain' => t('Domain prefix. E.g. !example', array('!example' => ctools_language_context_convert($context, 'domain'))),
    'prefix' => t('Url prefix . E.g. !example', array('!example' => ctools_language_context_convert($context, 'prefix'))),
    'weight' => t('The weight. E.g. !example', array('!example' => ctools_language_context_convert($context, 'weight'))),
    'javascript' => t('Key of the javascript file with the translations. E.g. !example', array('!example' => ctools_language_context_convert($context, 'javascript'))),
    'provider' => t('Negotiation method that defined the language. E.g. !example', array('!example' => ctools_language_context_convert($context, 'provider'))),
  );
}

/**
 * Convert a context property into a string to be used as a keyword.
 */
function ctools_language_context_convert($context, $type) {
  if (isset($context->data->$type)) {
    return $context->data->$type;
  }
}

/**
 * Settings form.
 */
function ctools_context_language_settings_form($form, &$form_state) {
  $conf = ctools_context_language_conf_defaults($form_state['conf']);

  $form['enable_cache_argument'] = array(
    '#title' => t('Add language to cache id'),
    '#description' => t('If enabled the langcode will be part of context aware caches.'),
    '#type' => 'checkbox',
    '#default_value' => $conf['enable_cache_argument'],
  );

  // Prepare language type options.
  $language_type_options = drupal_map_assoc(language_types());
  $language_type_options['preset'] = t('Custom');

  $form['language_type'] = array(
    '#title' => t('The language type to use'),
    '#type' => 'radios',
    '#required' => TRUE,
    '#options' => $language_type_options,
    '#default_value' => $conf['language_type'],
  );

  ctools_include('language');
  $language_options = ctools_language_list();
  $form['preset_langcode'] = array(
    '#title' => t('Language'),
    '#type' => 'select',
    '#options' => $language_options,
    '#default_value' => $conf['preset_langcode'],
    '#states' => array(
      'visible' => array(
        ':input[name="language_type"]' => array('value' => 'preset'),
      ),
    ),
  );

  if (!empty($conf['preset_langcode']) && !isset($language_options[$conf['preset_langcode']])) {
    drupal_set_message(t('The currently selected language %langcode is no longer available.', array('%langcode' => $conf['preset_langcode'])), 'error', FALSE);
  }
  return $form;
}
