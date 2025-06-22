<?php
/**
 * Auto Alt Text for Images
 * 
 * Automatically sets alt text for images based on post titles
 * 
 * @package WordPress
 * @subpackage Media Utilities
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Auto set image alt text based on post title
 * 
 * @param int $post_id Post ID
 */
function auto_set_image_alt_text($post_id = null) {
    // If triggered by 'save_post', get the post ID
    if ($post_id) {
        $post_type = get_post_type($post_id);

        // Check if it's a valid post type
        if (!in_array($post_type, array('post', 'page', 'product'))) {
            return;
        }

        // Get the post title
        $post_title = get_the_title($post_id);

        // Update attached images
        update_images_with_alt_text($post_id, $post_title);
    }
}

/**
 * Helper function to update images' alt text
 * 
 * @param int $post_id Post ID
 * @param string $alt_text Alt text to set
 */
function update_images_with_alt_text($post_id, $alt_text) {
    // Get all attached images for the post
    $attachments = get_attached_media('image', $post_id);

    // Loop through each attachment and set alt text if empty
    foreach ($attachments as $attachment) {
        $current_alt = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);

        if (empty($current_alt)) {
            update_post_meta($attachment->ID, '_wp_attachment_image_alt', sanitize_text_field($alt_text));
        }
    }
}

/**
 * Update alt text for all existing posts
 */
function auto_update_alt_text_for_all_posts() {
    $post_types = array('post', 'page', 'product');

    // Loop through each post type
    foreach ($post_types as $post_type) {
        $posts = get_posts(array(
            'post_type'      => $post_type,
            'posts_per_page' => -1, // Get all posts
            'post_status'    => 'publish', // Only published posts
        ));

        // Loop through each post
        foreach ($posts as $post) {
            $post_title = get_the_title($post->ID);

            // Update attached images for this post
            update_images_with_alt_text($post->ID, $post_title);
        }
    }
}

/**
 * Add admin notice with update button
 */
function add_admin_alt_text_update_button() {
    if (is_admin() && current_user_can('manage_options')) {
        if (isset($_GET['auto_update_alt_text']) && $_GET['auto_update_alt_text'] === '1') {
            auto_update_alt_text_for_all_posts();
            echo '<div class="updated"><p>Alt text updated for all old images.</p></div>';
        }
    }
}

/**
 * Add manual update button in admin dashboard
 */
function add_manual_update_alt_text_button() {
    if (current_user_can('manage_options')) {
        $url = add_query_arg('auto_update_alt_text', '1', admin_url());
        echo '<div class="notice notice-info"><p>';
        echo '<a class="button button-primary" href="' . esc_url($url) . '">Update Alt Text for Old Images</a>';
        echo '</p></div>';
    }
}

// Hook functions
add_action('save_post', 'auto_set_image_alt_text');
add_action('admin_notices', 'add_admin_alt_text_update_button');
add_action('admin_notices', 'add_manual_update_alt_text_button');