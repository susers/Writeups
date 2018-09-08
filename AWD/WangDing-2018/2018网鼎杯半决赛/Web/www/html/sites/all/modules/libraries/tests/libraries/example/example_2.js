
/**
 * @file
 * Test JavaScript file for Libraries loading.
 *
 * Replace the text in the 'libraries-test-module-js' div. See README.txt for
 * more information.
 */

(function ($) {

Drupal.behaviors.librariesTest = {
  attach: function(context, settings) {
    $('.libraries-test-module-js').text('If this text shows up, example_2.js was loaded successfully.')
  }
};

})(jQuery);
