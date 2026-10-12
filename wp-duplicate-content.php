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

// Respect the post types selected in Settings → Duplicate Content.
// Fresh installations allow posts and pages until an admin saves a selection.
function wdc_enabled_post_types() {
    $types = get_option('wdc_post_types', ['post', 'page']);
    // The hidden checkbox sentinel means "no post types selected".
    return is_array($types) ? array_values(array_filter($types, 'strlen')) : [];
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
    $selected = wdc_enabled_post_types();
    // When every checkbox is unchecked, browsers otherwise submit no value.
    // Persist an empty selection instead of silently keeping the previous one.
    echo '<input type="hidden" name="wdc_post_types[]" value="">';
    foreach ($post_types as $type) {
        echo '<input type="checkbox" name="wdc_post_types[]" value="' . esc_attr($type->name) . '" ' . (in_array($type->name, $selected, true) ? 'checked' : '') . '> ' . esc_html($type->label) . '<br>';
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
    $post_id = isset($_GET['post']) ? absint($_GET['post']) : 0;

    if (!$post_id || !current_user_can('edit_post', $post_id)) {
        wp_die(__('You do not have permission to duplicate this post.'));
    }

    // Prevent another site from triggering a duplication through an admin link.
    check_admin_referer('wdc_duplicate_' . $post_id);

    $post = get_post($post_id);
    if (!$post) {
        wp_die(__('Post not found.'));
    }

    if (!in_array($post->post_type, wdc_enabled_post_types(), true)) {
        wp_die(__('Duplication is disabled for this post type.'));
    }

    $new_post = [
        'post_title'   => 'Copy - ' . $post->post_title,
        'post_content' => $post->post_content,
        'post_excerpt' => $post->post_excerpt,
        'post_status'  => get_option('wdc_post_status', 'draft'),
        'post_type'    => $post->post_type,
        'post_author'  => get_current_user_id()
    ];

    $new_post_id = wp_insert_post($new_post, true);
    if (is_wp_error($new_post_id) || !$new_post_id) {
        wp_die(__('Duplication failed.'));
    }

    // Keep existing categories, tags, and custom taxonomy terms on the copy.
    foreach (get_object_taxonomies($post->post_type) as $taxonomy) {
        $term_ids = wp_get_object_terms($post_id, $taxonomy, ['fields' => 'ids']);

        if (is_wp_error($term_ids)) {
            wp_delete_post($new_post_id, true);
            wp_die(__('Could not read the original post taxonomy terms.'));
        }

        if (empty($term_ids)) {
            continue;
        }

        $copied_terms = wp_set_object_terms(
            $new_post_id,
            array_map('intval', $term_ids),
            $taxonomy,
            false
        );

        if (is_wp_error($copied_terms)) {
            wp_delete_post($new_post_id, true);
            wp_die(__('Could not copy the post taxonomy terms.'));
        }
    }

    // Copy the featured image reference without duplicating media attachments.
    $thumbnail_id = get_post_thumbnail_id($post_id);
    if ($thumbnail_id) {
        set_post_thumbnail($new_post_id, $thumbnail_id);
    }

    wp_safe_redirect(admin_url('edit.php?post_type=' . urlencode($post->post_type)));
    exit;
}
add_action('admin_action_wdc_duplicate', 'wdc_duplicate_post');

// Add Duplicate Link
function wdc_duplicate_link($actions, $post) {
    if (current_user_can('edit_post', $post->ID) && in_array($post->post_type, wdc_enabled_post_types(), true)) {
        $url = wp_nonce_url(
            admin_url('admin.php?action=wdc_duplicate&post=' . absint($post->ID)),
            'wdc_duplicate_' . absint($post->ID)
        );
        $actions['duplicate'] = '<a href="' . esc_url($url) . '" title="' . esc_attr__('Duplicate this item') . '">' . esc_html__('Duplicate') . '</a>';
    }
    return $actions;
}
add_filter('post_row_actions', 'wdc_duplicate_link', 10, 2);
add_filter('page_row_actions', 'wdc_duplicate_link', 10, 2);
?>

