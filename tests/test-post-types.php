<?php
/**
 * Small CLI regression tests for the post-type settings helper.
 *
 * Run with: php tests/test-post-types.php
 * These tests use WordPress function stubs and do not boot WordPress itself.
 */
declare(strict_types=1);

define('ABSPATH', __DIR__);

$GLOBALS['wdc_test_options'] = [];

function get_option($name, $default = false) {
    return array_key_exists($name, $GLOBALS['wdc_test_options'])
        ? $GLOBALS['wdc_test_options'][$name]
        : $default;
}

// Registering hooks has no effect in these isolated CLI tests.
function add_action(...$args): void {}
function add_filter(...$args): void {}

ob_start();
require dirname(__DIR__) . '/wp-duplicate-content.php';
ob_end_clean();

$cases = [
    [
        'name' => 'fresh installation enables posts and pages',
        'has_saved_option' => false,
        'value' => null,
        'expected' => ['post', 'page'],
    ],
    [
        'name' => 'only posts enabled',
        'has_saved_option' => true,
        'value' => ['post'],
        'expected' => ['post'],
    ],
    [
        'name' => 'empty checkbox sentinel is ignored',
        'has_saved_option' => true,
        'value' => ['', 'post', 'page'],
        'expected' => ['post', 'page'],
    ],
    [
        'name' => 'all checkboxes cleared',
        'has_saved_option' => true,
        'value' => [''],
        'expected' => [],
    ],
    [
        'name' => 'intentionally empty option',
        'has_saved_option' => true,
        'value' => [],
        'expected' => [],
    ],
    [
        'name' => 'unexpected scalar option disables duplication',
        'has_saved_option' => true,
        'value' => 'post',
        'expected' => [],
    ],
];

$failures = 0;

foreach ($cases as $case) {
    $GLOBALS['wdc_test_options'] = [];
    if ($case['has_saved_option']) {
        $GLOBALS['wdc_test_options']['wdc_post_types'] = $case['value'];
    }

    $actual = wdc_enabled_post_types();
    if ($actual !== $case['expected']) {
        fwrite(STDERR, 'FAIL: ' . $case['name'] . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($case['expected'], true) . PHP_EOL);
        fwrite(STDERR, 'Actual: ' . var_export($actual, true) . PHP_EOL);
        $failures++;
    } else {
        echo 'PASS: ' . $case['name'] . PHP_EOL;
    }
}

if ($failures > 0) {
    exit(1);
}

echo 'All ' . count($cases) . ' post-type settings tests passed.' . PHP_EOL;
