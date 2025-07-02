// Add admin menu for emergency deletion
add_action('admin_menu', 'add_emergency_delete_menu');

function add_emergency_delete_menu() {
    add_management_page(
        'Emergency Delete Posts',
        'Delete Hacked Posts',
        'manage_options',
        'emergency-delete',
        'emergency_delete_posts_page'
    );
}

// Handle the deletion
add_action('wp_ajax_emergency_delete_posts', 'handle_emergency_delete');

function handle_emergency_delete() {
    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    global $wpdb;
    
    // Delete all posts from June 2025
    $posts = $wpdb->get_results("
        SELECT ID, post_title, post_date 
        FROM {$wpdb->posts} 
        WHERE post_date >= '2025-06-01' 
        AND post_date < '2025-07-01' 
        AND post_type = 'post'
    ");
    
    $deleted_count = 0;
    $deleted_posts = array();
    
    foreach ($posts as $post) {
        if (wp_delete_post($post->ID, true)) {
            $deleted_count++;
            $deleted_posts[] = $post->post_title;
        }
    }
    
    wp_send_json_success(array(
        'deleted_count' => $deleted_count,
        'total_found' => count($posts),
        'deleted_posts' => $deleted_posts
    ));
}

// Admin page
function emergency_delete_posts_page() {
    ?>
    <div class="wrap">
        <h1>🚨 Emergency Delete Hacked Posts</h1>
        
        <div class="notice notice-warning">
            <p><strong>WARNING:</strong> This will permanently delete ALL posts from June 2025!</p>
        </div>
        
        <div id="delete-results" style="display: none;"></div>
        
        <button id="emergency-delete-btn" class="button button-primary button-large" 
                style="background: #dc3545; border-color: #dc3545; font-size: 16px; padding: 10px 20px;">
            🗑️ DELETE ALL JUNE 2025 POSTS NOW
        </button>
        
        <script>
        jQuery(document).ready(function($) {
            $('#emergency-delete-btn').click(function() {
                if (!confirm('Are you absolutely sure? This will permanently delete all posts from June 2025!')) {
                    return;
                }
                
                $(this).prop('disabled', true).text('Deleting...');
                
                $.post(ajaxurl, {
                    action: 'emergency_delete_posts'
                }, function(response) {
                    if (response.success) {
                        $('#delete-results').html(
                            '<div class="notice notice-success"><p>' +
                            '<strong>Deletion Complete!</strong><br>' +
                            'Posts deleted: ' + response.data.deleted_count + '<br>' +
                            'Posts found: ' + response.data.total_found +
                            '</p></div>'
                        ).show();
                        $('#emergency-delete-btn').hide();
                    } else {
                        $('#delete-results').html(
                            '<div class="notice notice-error"><p>Error occurred during deletion.</p></div>'
                        ).show();
                        $('#emergency-delete-btn').prop('disabled', false).text('🗑️ DELETE ALL JUNE 2025 POSTS NOW');
                    }
                });
            });
        });
        </script>
    </div>
    <?php
}
