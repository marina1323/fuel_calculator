<?php

namespace Drupal\fuel_calculator\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\fuel_calculator\CalculateFuelHandler;
use Drupal\fuel_calculator\FuelCalculatorInputFieldsValidator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class FuelCalculatorForm extends FormBase {

  protected $calculateFuelHandler;
  protected $requestStack;
  protected $fuelCalculatorInputFieldsValidator;

  /**
   * {@inheritdoc}
   */
  public function __construct(
      CalculateFuelHandler $calculateFuelHandler,
      RequestStack $requestStack,
      FuelCalculatorInputFieldsValidator $fuelCalculatorInputFieldsValidator
  ) {
    $this->calculateFuelHandler = $calculateFuelHandler;
    $this->requestStack = $requestStack;
    $this->fuelCalculatorInputFieldsValidator = $fuelCalculatorInputFieldsValidator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
        $container->get('fuel_calculator.calculate_fuel_handler'),
        $container->get('request_stack'),
        $container->get('fuel_calculator.input_fields_validator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'fuel_calculator_fuel_calculator';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $query = $this->requestStack->getCurrentRequest()->query;
    $config = $this->config('fuel_calculator.settings');
    
    $form['distance'] = [
      '#type' => 'number',
      '#step' => '.01',
      '#min' => 0,
      '#title' => $this->t('Distance travelled'),
      '#description' => $this->t('km'),
      '#default_value' => $query->get('distance') ?? $config->get('distance'),
      '#required' => TRUE,
    ];

    $form['fuel_consumption'] = [
      '#type' => 'number',
      '#step' => '.01',
      '#min' => 0,
      '#title' => $this->t('Fuel consumption'),
      '#description' => $this->t('l/100km'),
      '#default_value' => $query->get('fuel_consumption') ?? $config->get('fuel_consumption'),
      '#required' => TRUE,
    ];

    $form['price_per_liter'] = [
      '#type' => 'number',
      '#step' => '.01',
      '#min' => 0,
      '#title' => $this->t('Price per liter'),
      '#description' => $this->t('EUR'),
      '#default_value' => $query->get('price_per_liter') ?? $config->get('price_per_liter'),
      '#required' => TRUE,
    ];

    $form['fuel_spent'] = [
        '#type' => 'number',
        '#step' => 'any',
        '#disabled' => TRUE,
        '#title' => $this->t('Fuel spent'),
        '#default_value' =>  $form_state->get('fuel_spent') ?? 0,
        '#field_suffix' => 'liters',
    ];

    $form['fuel_cost'] = [
        '#type' => 'number',
        '#step' => 'any',
        '#disabled' => TRUE,
        '#title' => $this->t('Fuel cost'),
        '#default_value' => $form_state->get('fuel_cost') ?? 0,
        '#field_suffix' => 'EUR.',
    ];


    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['reset_button'] = [
      '#type' => 'button',
      '#value' => $this->t('Reset'),
      '#attributes' => [
        'id' => 'reset-button',
      ],
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Calculate'),
    ];

    $form['#attached']['library'][] = 'fuel_calculator/fuel_calculator_form';

    return $form;
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
    $distance = $form_state->getValue('distance');
    $fuelConsumption = $form_state->getValue('fuel_consumption');
    $pricePerLiter = $form_state->getValue('price_per_liter');

    $result = $this->calculateFuelHandler->handle($distance, $fuelConsumption, $pricePerLiter);

    $form_state->set('fuel_spent', $result['fuel_spent']);
    $form_state->set('fuel_cost', $result['fuel_cost']);

    $form_state->setRebuild();
  }
}
