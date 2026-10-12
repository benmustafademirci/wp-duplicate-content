<?php
declare(strict_types=1);
define('ABSPATH', __DIR__);
$GLOBALS['wdc_test_status'] = 'draft';
$GLOBALS['wdc_can_publish'] = false;
$GLOBALS['wdc_post_type_valid'] = true;
function get_option($key, $default = false) {
    return $key === 'wdc_post_status' ? $GLOBALS['wdc_test_status'] : $default;
}
function get_post_type_object($type) {
    return $GLOBALS['wdc_post_type_valid'] ? (object)['cap' => (object)['publish_posts' => 'publish_posts']] : null;
}
function current_user_can($cap) {
    return $cap === 'publish_posts' && $GLOBALS['wdc_can_publish'];
}
function add_action(...$args): void {}
function add_filter(...$args): void {}
ob_start(); require dirname(__DIR__) . '/wp-duplicate-content.php'; ob_end_clean();
$cases = [
    ['status'=>'draft','publish'=>false,'type'=>true,'expected'=>'draft'],
    ['status'=>'pending','publish'=>false,'type'=>true,'expected'=>'pending'],
    ['status'=>'publish','publish'=>false,'type'=>true,'expected'=>'draft'],
    ['status'=>'private','publish'=>false,'type'=>true,'expected'=>'draft'],
    ['status'=>'publish','publish'=>true,'type'=>true,'expected'=>'publish'],
    ['status'=>'private','publish'=>true,'type'=>true,'expected'=>'private'],
    ['status'=>'publish','publish'=>true,'type'=>false,'expected'=>'draft'],
];
foreach ($cases as $index=>$case) {
    $GLOBALS['wdc_test_status']=$case['status'];
    $GLOBALS['wdc_can_publish']=$case['publish'];
    $GLOBALS['wdc_post_type_valid']=$case['type'];
    $actual=wdc_permitted_duplicate_status('post');
    if($actual!==$case['expected']) {
        fwrite(STDERR, "FAIL case {$index}: expected {$case['expected']}, got {$actual}\n");
        exit(1);
    }
    echo "PASS case {$index}\n";
}
