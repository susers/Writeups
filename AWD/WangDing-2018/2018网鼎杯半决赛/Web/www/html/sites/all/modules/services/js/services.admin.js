(function ($) {
  /**
   * Add the table collapsing on the methoding overview page.
   */
  Drupal.behaviors.resourceMenuCollapse = {
    attach: function (context, settings) {
      var timeout = null,
        arrowImageHTML = function(collapsed) {
          return settings.services.images[collapsed ? 'collapsed' : 'expanded'];
        },
        setRowsCollapsedState = function(toggle, $rows, collapsed) {
          if (collapsed) {
            $rows.addClass('js-hide');
          }
          else {
            $rows.removeClass('js-hide');
          }
          $(toggle).html(arrowImageHTML(collapsed));
        };

      $('td.resource-select-all').each(function() {
        var resourceName = this.id,
          resource = settings.services.resources[this.id],
          $rowElements = $('.' + resourceName + '-method'),
          $row = $(this.parentElement);
        $('div.resource-image', $row)
          // Adds group toggling functionality to arrow images.
          .bind('click', function () {
            resource.collapsed = !resource.collapsed;
            setRowsCollapsedState(this, $rowElements, resource.collapsed);
          })
          // Set up initial toggle state
          .each(function() {
            setRowsCollapsedState(this, $rowElements, resource.collapsed);
          });
      });
    }
  };

  /**
   * Select/deselect all the inner checkboxes when the outer checkboxes are
   * selected/deselected.
   */
  Drupal.behaviors.resourceSelectAll = {
    attach: function (context, settings) {
      $('td.resource-select-all').each(function () {
        var resourceName = this.id,
          methodCheckboxes = $('.' + resourceName + '-method .resource-method-select input[type=checkbox]'),
          groupCheckbox = $('<input type="checkbox" class="form-checkbox" />').attr('id', this.id + '-select-all'),
          // Each time a single-method checkbox is checked or unchecked, make sure
          // that the associated group checkbox gets the right state too.
          updateGroupCheckbox = function () {
            $(groupCheckbox).attr('checked', (methodCheckboxes.filter('[checked]').length == methodCheckboxes.length));
          };

        // Have the single-method checkboxes follow the group checkbox.
        groupCheckbox.bind('change', function () {
          methodCheckboxes.attr('checked', $(this).attr('checked'));
        });

        // Have the group checkbox follow the single-method checkboxes.
        methodCheckboxes.bind('change', function () {
          updateGroupCheckbox();
        });

        // Initialize status for the group checkbox correctly.
        updateGroupCheckbox();

        $(this).append(groupCheckbox);
      });
    }
  };
})(jQuery);