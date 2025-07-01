<?php
/**
 * Initialize RankMath bulk action functionality
 */
function rankmath_bulk_action_init() {
    // Only run on admin area and if RankMath exists
    if (!is_admin() || !class_exists('RankMath')) {
        return;
    }
    
    // Add bulk action for posts
    add_filter('bulk_actions-edit-post', 'add_rankmath_bulk_action');
    add_filter('handle_bulk_actions-edit-post', 'handle_rankmath_bulk_action', 10, 3);
    
    // Add bulk action for pages
    add_filter('bulk_actions-edit-page', 'add_rankmath_bulk_action');
    add_filter('handle_bulk_actions-edit-page', 'handle_rankmath_bulk_action', 10, 3);
    
    // Add bulk action for products (if WooCommerce is active)
    if (class_exists('WooCommerce')) {
        add_filter('bulk_actions-edit-product', 'add_rankmath_bulk_action');
        add_filter('handle_bulk_actions-edit-product', 'handle_rankmath_bulk_action', 10, 3);
    }
    
    // Show admin notices
    add_action('admin_notices', 'rankmath_bulk_action_notices');
}
add_action('admin_init', 'rankmath_bulk_action_init');

/**
 * Add custom bulk action to the dropdown
 * 
 * @param array $bulk_actions Existing bulk actions
 * @return array Modified bulk actions
 */
function add_rankmath_bulk_action($bulk_actions) {
    // Check if RankMath is still active
    if (!class_exists('RankMath')) {
        return $bulk_actions;
    }
    
    $bulk_actions['set_rankmath_focus_keyword'] = 'Set Title as Focus Keyword (RankMath)';
    return $bulk_actions;
}

/**
 * Handle the custom bulk action
 * 
 * @param string $redirect_to Redirect URL
 * @param string $doaction Action being performed
 * @param array $post_ids Array of post IDs
 * @return string Modified redirect URL
 */
function handle_rankmath_bulk_action($redirect_to, $doaction, $post_ids) {
    // Return early if not our action
    if ($doaction !== 'set_rankmath_focus_keyword') {
        return $redirect_to;
    }
    
    // Check user capabilities
    if (!current_user_can('edit_posts')) {
        $redirect_to = add_query_arg('rankmath_error', 'insufficient_permissions', $redirect_to);
        return $redirect_to;
    }
    
    // Validate post IDs
    if (empty($post_ids) || !is_array($post_ids)) {
        $redirect_to = add_query_arg('rankmath_error', 'no_posts', $redirect_to);
        return $redirect_to;
    }
    
    $updated = 0;
    $errors = array();
    
    foreach ($post_ids as $post_id) {
        $post_id = intval($post_id);
        
        if ($post_id <= 0) {
            continue;
        }
        
        $post = get_post($post_id);
        
        if (!$post) {
            $errors[] = "Post ID {$post_id} not found";
            continue;
        }
        
        // Check if user can edit this specific post
        if (!current_user_can('edit_post', $post_id)) {
            $errors[] = "Cannot edit: " . $post->post_title;
            continue;
        }
        
        // Clean and prepare the title for use as focus keyword
        $focus_keyword = sanitize_text_field($post->post_title);
        
        if (empty($focus_keyword)) {
            $errors[] = "Empty title for: " . ($post->post_title ?: "Post ID {$post_id}");
            continue;
        }
        
        // Update RankMath focus keyword
        $result = update_post_meta($post_id, 'rank_math_focus_keyword', $focus_keyword);
        
        if ($result !== false) {
            $updated++;
        } else {
            $errors[] = "Failed to update: " . $post->post_title;
        }
    }
    
    // Add results to redirect URL
    $redirect_to = add_query_arg('rankmath_updated', $updated, $redirect_to);
    
    if (!empty($errors)) {
        // Store errors in transient for display (limit to prevent URL length issues)
        $error_summary = array_slice($errors, 0, 5);
        if (count($errors) > 5) {
            $error_summary[] = '... and ' . (count($errors) - 5) . ' more errors';
        }
        set_transient('rankmath_bulk_errors_' . get_current_user_id(), $error_summary, 30);
        $redirect_to = add_query_arg('rankmath_has_errors', '1', $redirect_to);
    }
    
    return $redirect_to;
}

/**
 * Display admin notices for bulk action results
 */
function rankmath_bulk_action_notices() {
    // Success notice
    if (isset($_REQUEST['rankmath_updated'])) {
        $updated = intval($_REQUEST['rankmath_updated']);
        
        if ($updated > 0) {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p><strong>RankMath:</strong> Successfully set focus keywords for ' . $updated . ' item(s).</p>';
            echo '</div>';
        }
    }
    
    // Error notices
    if (isset($_REQUEST['rankmath_error'])) {
        $error = sanitize_text_field($_REQUEST['rankmath_error']);
        $message = '';
        
        switch ($error) {
            case 'insufficient_permissions':
                $message = 'You do not have permission to edit posts.';
                break;
            case 'no_posts':
                $message = 'No posts were selected for the action.';
                break;
            default:
                $message = 'An unknown error occurred.';
                break;
        }
        
        echo '<div class="notice notice-error is-dismissible">';
        echo '<p><strong>RankMath Error:</strong> ' . esc_html($message) . '</p>';
        echo '</div>';
    }
    
    // Display detailed errors if any
    if (isset($_REQUEST['rankmath_has_errors'])) {
        $errors = get_transient('rankmath_bulk_errors_' . get_current_user_id());
        if ($errors && is_array($errors)) {
            echo '<div class="notice notice-warning is-dismissible">';
            echo '<p><strong>RankMath:</strong> Some items could not be updated:</p>';
            echo '<ul style="margin-left: 20px;">';
            foreach ($errors as $error) {
                echo '<li>' . esc_html($error) . '</li>';
            }
            echo '</ul>';
            echo '</div>';
            
            // Clean up transient
            delete_transient('rankmath_bulk_errors_' . get_current_user_id());
        }
    }
}

/**
 * Add admin notice if RankMath is not active
 */
function rankmath_bulk_admin_notice() {
    if (!class_exists('RankMath') && current_user_can('manage_options')) {
        $screen = get_current_screen();
        
        // Only show on post list pages
        if ($screen && in_array($screen->id, array('edit-post', 'edit-page', 'edit-product'))) {
            echo '<div class="notice notice-warning">';
            echo '<p><strong>RankMath Bulk Action:</strong> RankMath SEO plugin is required for the "Set Title as Focus Keyword" bulk action.</p>';
            echo '</div>';
        }
    }
}
add_action('admin_notices', 'rankmath_bulk_admin_notice');
