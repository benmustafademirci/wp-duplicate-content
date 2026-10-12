<?php
/**
 * CLI regression tests for visibility and safety of the Duplicate row action.
 * WordPress functions are stubbed; full WordPress integration is NOT tested.
 */
declare(strict_types=1);

define('ABSPATH', __DIR__);

$GLOBALS['wdc_options'] = [];
$GLOBALS['wdc_can_edit'] = true;

function get_option($name, $default = false) {
    return array_key_exists($name, $GLOBALS['wdc_options'])
        ? $GLOBALS['wdc_options'][$name] : $default;
}
function current_user_can($cap, $id = null) {
    return $cap === 'edit_post' && $GLOBALS['wdc_can_edit'];
}
function admin_url($path) { return '/wp-admin/' . $path; }
function wp_nonce_url($url, $action) { return $url . '&_wpnonce=test'; }
function esc_url($v) { return $v; }
function esc_attr__($v) { return $v; }
function esc_html__($v) { return $v; }
function add_action(...$args): void {}
function add_filter(...$args): void {}

ob_start();
require dirname(__DIR__) . '/wp-duplicate-content.php';
ob_end_clean();

$cases = [
    ['title' => 'default post link', 'type' => 'post', 'enabled' => null, 'edit' => true, 'visible' => true],
    ['title' => 'default page link', 'type' => 'page', 'enabled' => null, 'edit' => true, 'visible' => true],
    ['title' => 'disabled page link', 'type' => 'page', 'enabled' => ['post'], 'edit' => true, 'visible' => false],
    ['title' => 'no access', 'type' => 'post', 'enabled' => ['post'], 'edit' => false, 'visible' => false],
    ['title' => 'enabled custom post type', 'type' => 'product', 'enabled' => ['product'], 'edit' => true, 'visible' => true],
    ['title' => 'no enabled post types', 'type' => 'post', 'enabled' => [''], 'edit' => true, 'visible' => false],
];
foreach ($cases as $case) {
    $GLOBALS['wdc_options'] = [];
    if ($case['enabled'] !== null) {
        $GLOBALS['wdc_options']['wdc_post_types'] = $case['enabled'];
    }
    $GLOBALS['wdc_can_edit'] = $case['edit'];
    $post = (object)['ID' => 123, 'post_type' => $case['type']];
    $actions = wdc_duplicate_link(['existing' => 'existing'], $post);
    $isVisible = array_key_exists('duplicate', $actions);
    if ($isVisible !== $case['visible'] || $actions['existing'] !== 'existing') {
        fwrite(STDERR, 'FAIL: ' . $case['title'] . PHP_EOL);
        exit(1);
    }
    if ($isVisible && (
        strpos($actions['duplicate'], 'post=123') === false ||
        strpos($actions['duplicate'], '_wpnonce=test') === false
    )) {
        fwrite(STDERR, 'FAIL: missing post id or nonce in ' . $case['title'] . PHP_EOL);
        exit(1);
    }
    echo 'PASS: ' . $case['title'] . PHP_EOL;
}
