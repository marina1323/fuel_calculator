<?php

namespace Drupal\fuel_calculator;

use Drupal\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

class CalculateFuelHandler {

  private $fuelCalculator;
  private $calculateFuelLogBuilder;
  private $loggerChannelFactory;

  public function __construct(
      FuelCalculator $fuelCalculator,
      CalculateFuelLogBuilder $calculateFuelLogBuilder,
      LoggerChannelFactoryInterface $loggerChannelFactory
  ) {
    $this->fuelCalculator = $fuelCalculator;
    $this->calculateFuelLogBuilder = $calculateFuelLogBuilder;
    $this->loggerChannelFactory = $loggerChannelFactory;
  }

  public static function create(ContainerInterface $container) {
    return new static(
        $container->get('fuel_calculator.calculator'),
        $container->get('fuel_calculator.calculate_fuel_log_builder'),
        $container->get('logger.factory')
    );
  }

  public function handle(float $distance, float $fuelConsumption, float $pricePerLiter): array
  {
    $fuelSpent = round($this->fuelCalculator->calculateFuelSpent($fuelConsumption, $distance), 1);
    $fuelCost = round($this->fuelCalculator->calculateFuelCost($fuelSpent, $pricePerLiter), 1);

    $this->loggerChannelFactory->get('fuel_calculations')->info(
        json_encode($this->calculateFuelLogBuilder->build($distance, $fuelConsumption, $pricePerLiter, $fuelSpent, $fuelCost))
    );

    return [
        'fuel_spent' => $fuelSpent,
        'fuel_cost' => $fuelCost,
    ];
  }
}
