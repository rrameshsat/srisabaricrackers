<?php
return [
    // Global toggle for checkout submission availability
    'enabled' => true,

    // Default delivery charge if a state is not explicitly listed
    'default' => 4.00,

    // State-specific delivery charges (in USD). Extend as needed.
    'charges' => [
        // Example entries; replace with your real state codes and charges
        'CA' => 6.50,
        'NY' => 7.50,
        'TX' => 5.00,
        'FL' => 5.50,
        // Add more states as needed
    ],
];
