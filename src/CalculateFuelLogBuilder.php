<?php

namespace Drupal\fuel_calculator;

use Drupal\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CalculateFuelLogBuilder {

  private $requestStack;
  private $currentUser;
  
  public function __construct(
      RequestStack $requestStack,
      AccountInterface $currentUser
  ) {
    $this->requestStack = $requestStack;
    $this->currentUser = $currentUser;
  }

  public static function create(ContainerInterface $container): static {
    return new static(
        $container->get('request_stack'),
        $container->get('current_user')
    );
  }

  public function build(float $distance, float $fuelConsumption, float $pricePerLiter, float $fuelSpent, float $fuelCost): array
  {
    return [
        'ip_address' =>  $this->requestStack->getCurrentRequest()->getClientIp(),
        'user' =>$this->currentUser->getAccountName(),
        'distance' => $distance,
        'fuel_consumption' => $fuelConsumption,
        'price_per_liter' => $pricePerLiter,
        'fuel_spent' => $fuelSpent,
        'fuel_cost' => $fuelCost,
    ];
  }
}
