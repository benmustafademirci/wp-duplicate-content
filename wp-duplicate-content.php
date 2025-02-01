<?php
/**
 * Plugin Name: WP Duplicate Content
 * Description: Duplicate posts, pages, and custom post types with a single click.
 * Version: 1.0.1
 * Author: Mustafa Demirci
 * Author URI: https://demircimedya.com.tr
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Admin Menu
function wdc_admin_menu() {
    add_options_page('WP Duplicate Content', 'Duplicate Content', 'manage_options', 'wp-duplicate-content', 'wdc_settings_page');
}
add_action('admin_menu', 'wdc_admin_menu');

// Settings Page
function wdc_settings_page() {
    ?>
    <div class="wrap">
        <h2>WP Duplicate Content Settings</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('wdc_settings_group');
            do_settings_sections('wp-duplicate-content');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register Settings
function wdc_register_settings() {
    register_setting('wdc_settings_group', 'wdc_post_types');
    register_setting('wdc_settings_group', 'wdc_post_status');

    add_settings_section('wdc_main_section', 'General Settings', null, 'wp-duplicate-content');

    add_settings_field('wdc_post_types', 'Post Types to Duplicate', 'wdc_post_types_callback', 'wp-duplicate-content', 'wdc_main_section');
    add_settings_field('wdc_post_status', 'Default Status of Duplicated Content', 'wdc_post_status_callback', 'wp-duplicate-content', 'wdc_main_section');
}
add_action('admin_init', 'wdc_register_settings');

// Callbacks
function wdc_post_types_callback() {
    $post_types = get_post_types(['public' => true], 'objects');
    $selected = get_option('wdc_post_types', []);
    foreach ($post_types as $type) {
        echo '<input type="checkbox" name="wdc_post_types[]" value="' . esc_attr($type->name) . '" ' . (in_array($type->name, $selected) ? 'checked' : '') . '> ' . esc_html($type->label) . '<br>';
    }
}

function wdc_post_status_callback() {
    $statuses = ['draft' => 'Draft', 'publish' => 'Published', 'private' => 'Private', 'pending' => 'Pending'];
    $selected = get_option('wdc_post_status', 'draft');
    echo '<select name="wdc_post_status">';
    foreach ($statuses as $key => $label) {
        echo '<option value="' . esc_attr($key) . '" ' . selected($selected, $key, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
}

// Duplicate Function
function wdc_duplicate_post() {
    if (!isset($_GET['post']) || !current_user_can('edit_posts')) {
        wp_die(__('You do not have permission to duplicate this post.'));
    }
    
    $post_id = absint($_GET['post']);
    $post = get_post($post_id);
    if (!$post) {
        wp_die(__('Post not found.'));
    }
    
    $new_post = [
        'post_title' => 'Copy - ' . $post->post_title,
        'post_content' => $post->post_content,
        'post_status' => get_option('wdc_post_status', 'draft'),
        'post_type' => $post->post_type,
        'post_author' => get_current_user_id()
    ];
    
    $new_post_id = wp_insert_post($new_post);
    
    if ($new_post_id) {
        wp_redirect(admin_url('edit.php?post_type=' . $post->post_type));
        exit;
    } else {
        wp_die(__('Duplication failed.'));
    }
}
add_action('admin_action_wdc_duplicate', 'wdc_duplicate_post');

// Add Duplicate Link
function wdc_duplicate_link($actions, $post) {
    if (current_user_can('edit_posts')) {
        $actions['duplicate'] = '<a href="' . admin_url('admin.php?action=wdc_duplicate&post=' . $post->ID) . '" title="Duplicate this item">Duplicate</a>';
    }
    return $actions;
}
add_filter('post_row_actions', 'wdc_duplicate_link', 10, 2);
add_filter('page_row_actions', 'wdc_duplicate_link', 10, 2);
?>

