# Fuel calculator module

1. The URL parameters to prefill the calculator form example: /fuel-calculator?distance=240&fuel_consumption=5&price_per_liter=2
2. The calculator API endpoint: /api/fuel-calculator, the "Fuel calculator Resource" should be enabled. Request method - POST, JSON request body example:
{
    "distance": 240,
    "fuel_consumption": 16,
    "price_per_liter": 7
}

