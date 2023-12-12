<?php

namespace Drupal\fuel_calculator\Plugin\rest\resource;

use Drupal\fuel_calculator\CalculateFuelHandler;
use Drupal\fuel_calculator\FuelCalculatorInputFieldsValidator;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * @RestResource (
 *   id = "fuel_calculator_resource",
 *   label = @Translation("Fuel calculator Resource"),
 *   uri_paths = {
 *     "canonical" = "/api/fuel-calculator",
 *     "create" = "/api/fuel-calculator"
 *   }
 * )
 */
class FuelCalculatorResource extends ResourceBase {

  private $calculateFuelHandler;
  private $fuelCalculatorInputFieldsValidator;

  /**
   * {@inheritdoc}
   */
  public function __construct(
      array $configuration,
      $plugin_id,
      $plugin_definition,
      array $serializer_formats,
      LoggerInterface $logger,
      CalculateFuelHandler $calculateFuelHandler,
      FuelCalculatorInputFieldsValidator $fuelCalculatorInputFieldsValidator
  ) {
    parent::__construct(
        $configuration,
        $plugin_id,
        $plugin_definition,
        $serializer_formats,
        $logger
    );
    $this->calculateFuelHandler = $calculateFuelHandler;
    $this->fuelCalculatorInputFieldsValidator = $fuelCalculatorInputFieldsValidator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('fuel_calculator.calculate_fuel_handler'),
      $container->get('fuel_calculator.input_fields_validator')
    );
  }


  public function post(Request $request) {
    $postData = json_decode($request->getContent(), true);

    $errors = $this->fuelCalculatorInputFieldsValidator->validate($postData);
    if (!empty($errors)) {
      return new ResourceResponse(json_encode($errors), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    return new ResourceResponse(
        $this->calculateFuelHandler->handle(
            $postData['distance'],
            $postData['fuel_consumption'],
            $postData['price_per_liter'])
    );
  }
}
