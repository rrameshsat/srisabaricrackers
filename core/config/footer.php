<?php

return [
    'show_faq' => env('FOOTER_SHOW_FAQ', true),
    'show_how_it_works' => env('FOOTER_SHOW_HOW_IT_WORKS', false),

    /*
    |----------------------------------------------------------------------
    | Footer Route Placeholders
    |----------------------------------------------------------------------
    |
    | Update these route names when the corresponding pages exist.
    | "front.how_it_works" is currently a placeholder and is guarded in Blade
    | with Route::has(...) so no broken link is rendered before the route exists.
    |
    */
    'faq_route' => 'front.faq',
    'how_it_works_route' => 'front.how_it_works',
];
