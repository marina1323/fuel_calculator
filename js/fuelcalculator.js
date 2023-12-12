(function ($, Drupal) {
  Drupal.behaviors.fuelCalculatorFormBehaviour = {
    attach: function (context, settings) {
      $('#reset-button').on('click', function (event) {
        event.preventDefault();
        $('#fuel-calculator-fuel-calculator').find('input[type=number]').val('');
      });
    },
  };
})(jQuery, Drupal);
