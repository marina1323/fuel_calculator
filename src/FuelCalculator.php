<?php

namespace Drupal\fuel_calculator;

class FuelCalculator {

  public function calculateFuelSpent(float $fuelConsumption, float $distance): float
  {
    return ($fuelConsumption/100) * $distance;
  }

  public function calculateFuelCost(float $fuelSpent, float $pricePerLiter): float
  {
    return $fuelSpent * $pricePerLiter;
  }
}
