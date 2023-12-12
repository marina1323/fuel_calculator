<?php

namespace Drupal\fuel_calculator\Form;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\fuel_calculator\FuelCalculatorInputFieldsValidator;

class FuelCalculatorDefaultSettingsForm extends ConfigFormBase {

  protected $fuelCalculatorInputFieldsValidator;

  public function __construct(
      ConfigFactoryInterface $config_factory,
      FuelCalculatorInputFieldsValidator $fuelCalculatorInputFieldsValidator
  ) {
    parent::__construct($config_factory);
    $this->fuelCalculatorInputFieldsValidator = $fuelCalculatorInputFieldsValidator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
        $container->get('config.factory'),
        $container->get('fuel_calculator.input_fields_validator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'fuel_calculator_fuel_calculator_default_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['fuel_calculator.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('fuel_calculator.settings');

    $form['distance'] = [
      '#type' => 'number',
      '#step' => '.01',
      '#title' => $this->t('Distance travelled'),
      '#description' => $this->t('km.'),
      '#default_value' => $config->get('distance'),
      '#required' => TRUE,
    ];

    $form['fuel_consumption'] = [
      '#type' => 'number',
      '#step' => '.01',
      '#title' => $this->t('Fuel consumption'),
      '#description' => $this->t('l/100km.'),
      '#default_value' => $config->get('fuel_consumption'),
      '#required' => TRUE,
    ];

    $form['price_per_liter'] = [
      '#type' => 'number',
      '#step' => '.01',
      '#title' => $this->t('Price per Liter'),
      '#description' => $this->t('EUR.'),
      '#default_value' => $config->get('price_per_liter'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $errors = $this->fuelCalculatorInputFieldsValidator->validate(
        [
            'distance' => $form_state->getValue('distance'),
            'fuel_consumption' => $form_state->getValue('fuel_consumption'),
            'price_per_liter' => $form_state->getValue('price_per_liter'),
        ]
    );

    foreach($errors as $error) {
      $form_state->setErrorByName($error['key'], $error['message']);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->config('fuel_calculator.settings')
      ->set('distance', $form_state->getValue('distance'))
      ->set('fuel_consumption', $form_state->getValue('fuel_consumption'))
      ->set('price_per_liter', $form_state->getValue('price_per_liter'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
