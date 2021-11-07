/**
 * @file
 * Bulma scripts for theme.
 */
(function ($) {
  'use strict';

  Drupal.behaviors.bulma = {
    attach: function (context) {
      var $toggle = $('#js-navbar-burger').once('bulma');
      if ($toggle.length) {
        var $menu = $('#js-navbar-menu');

        $toggle.click(function () {
          $(this).toggleClass('is-active');
          $menu.toggleClass('is-active');
        });
      }

      // Dropdowns

      var $dropdowns = getAll('.dropdown:not(.is-hoverable)');

      if ($dropdowns.length > 0) {
        $dropdowns.forEach(function ($el) {
          $el.addEventListener('click', function (event) {
            event.stopPropagation();
            $el.classList.toggle('is-active');
          });
        });

        document.addEventListener('click', function (event) {
          closeDropdowns();
        });
      }

      function closeDropdowns() {
        $dropdowns.forEach(function ($el) {
          $el.classList.remove('is-active');
        });
      }

      // Utils

      function getAll(selector) {
        var parent = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : document;

        return Array.prototype.slice.call(parent.querySelectorAll(selector), 0);
      }
    }
  };

})(jQuery);
