<?php

namespace Drupal\fuel_calculator;


class FuelCalculatorInputFieldsValidator {

  private $requiredFields = [
      'distance',
      'fuel_consumption',
      'price_per_liter',
  ];

  public function validate(array $data): array
  {
    $errors = [];

    foreach ($this->requiredFields as $field) {
        if (!isset($data[$field])) {
            $errors[] = [
                'key' => $field,
                'message' => 'This field is required.'
            ];
            continue;
        }
        if (!is_numeric($data[$field]) || (float) $data[$field] < 0) {
            $errors[] = [
                'key' => $field,
                'message' => 'The value should be a positive number',
            ];
        }
    }

      return $errors;
  }
}
