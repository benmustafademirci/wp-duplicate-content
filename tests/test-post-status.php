<?php
declare(strict_types=1);
define('ABSPATH', __DIR__);
$GLOBALS['status_option_exists'] = false;
$GLOBALS['status_option_value'] = null;
function get_option($key, $default = false) {
    return $GLOBALS['status_option_exists'] ? $GLOBALS['status_option_value'] : $default;
}
function add_action(...$args): void {}
function add_filter(...$args): void {}
ob_start();
require dirname(__DIR__) . '/wp-duplicate-content.php';
ob_end_clean();
$cases = [
    ['label' => 'missing setting', 'exists' => false, 'value' => null, 'expected' => 'draft'],
    ['label' => 'draft', 'exists' => true, 'value' => 'draft', 'expected' => 'draft'],
    ['label' => 'pending', 'exists' => true, 'value' => 'pending', 'expected' => 'pending'],
    ['label' => 'private', 'exists' => true, 'value' => 'private', 'expected' => 'private'],
    ['label' => 'publish', 'exists' => true, 'value' => 'publish', 'expected' => 'publish'],
    ['label' => 'invalid', 'exists' => true, 'value' => 'future', 'expected' => 'draft'],
    ['label' => 'array injection', 'exists' => true, 'value' => ['publish'], 'expected' => 'draft'],
];
foreach ($cases as $case) {
    $GLOBALS['status_option_exists'] = $case['exists'];
    $GLOBALS['status_option_value'] = $case['value'];
    if (wdc_duplicate_post_status() !== $case['expected']) {
        fwrite(STDERR, 'FAIL: ' . $case['label'] . PHP_EOL);
        exit(1);
    }
    echo 'PASS: ' . $case['label'] . PHP_EOL;
}
