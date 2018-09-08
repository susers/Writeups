/**
 * @file
 * Custom state for handling visibility
 */

/**
 * Add a new state to Drupal #states. We use this to toggle element-invisible
 * to show/hidden #states elements. This allows elements to be visible to
 * screen readers.
 *
 * To use:
 * $form['my_form_field'] = array(
 *   ..
 *   // Only show this field if 'some_other_field' is checked.
 *   '#states => array(
 *     'show' => array(
 *       'some-other-field' => array('checked' => TRUE),
 *     ),
 *   ),
 *   ..
 *   // Required to load the 'show' state handler.
 *   '#attached' => array(
 *     'js' => array(ctools_attach_js('states-show')),
 *   ),
 * );
 */

(function ($) {
  'use strict';

  Drupal.states.State.aliases.hidden = '!show';

  // Show/hide form items by toggling the 'element-invisible' class. This is a
  // more accessible option than the core 'visible' state.
  $(document).bind('state:show', function(e) {
    if (e.trigger) {
      var element = $(e.target).closest('.form-item, .form-submit, .form-wrapper');
      element.toggle(e.value);
      e.value === true ? element.removeClass('element-invisible') : element.addClass('element-invisible');
    }
  });

})(jQuery);
