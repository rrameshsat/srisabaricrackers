<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$item = App\Models\Item::where('name', 'Red Bijili')->first();
if ($item) {
    echo 'ITEM_ID=' . $item->id . PHP_EOL;
    echo 'DISCOUNT=' . $item->discount_price . PHP_EOL;
    echo 'PREVIOUS=' . $item->previous_price . PHP_EOL;
    echo 'TAX_ID=' . $item->tax_id . PHP_EOL;
}
$curr = App\Models\Currency::where('is_default', 1)->first();
if ($curr) {
    echo 'CURR_VALUE=' . $curr->value . PHP_EOL;
    echo 'CURR_SIGN=' . $curr->sign . PHP_EOL;
}
$setting = App\Models\Setting::first();
if ($setting) {
    echo 'CURRENCY_DIRECTION=' . $setting->currency_direction . PHP_EOL;
}
