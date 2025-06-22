<?php
/**
 * RankMath Title to Focus Keyword
 * 
 * Adds functionality to set post titles as RankMath focus keywords in bulk
 * 
 * @package WordPress
 * @subpackage RankMath Extensions
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RankMath Title to Focus Keyword - Simple, effective solution
 */
function rankmath_title_to_keyword() {
    // Only run on admin area and if RankMath exists
    if (!is_admin() || !class_exists('RankMath')) {
        return;
    }
    
    // Add our button to the edit screens
    add_action('restrict_manage_posts', 'add_rankmath_button', 999);
    
    // Handle AJAX request
    add_action('wp_ajax_set_titles_as_keywords', 'process_titles_as_keywords');
}
add_action('init', 'rankmath_title_to_keyword');

/**
 * Add button to the filters area - guaranteed to appear only once
 */
function add_rankmath_button() {
    global $typenow;
    
    // Only for supported post types
    if (!in_array($typenow, array('post', 'page', 'product'))) {
        return;
    }
    
    echo '<input type="button" id="rankmath_keyword_btn" class="button" value="Set Title as Focus Keyword">';
    
    // Add the JavaScript right after the button
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#rankmath_keyword_btn').click(function() {
            var selectedPosts = $('input[name="post[]"]:checked').map(function() {
                return $(this).val();
            }).get();
            
            if (selectedPosts.length === 0) {
                alert('Please select at least one item');
                return;
            }
            
            $(this).val('Processing...').prop('disabled', true);
            
            $.post(ajaxurl, {
                action: 'set_titles_as_keywords',
                posts: selectedPosts,
                security: '<?php echo wp_create_nonce("rankmath_set_keywords"); ?>'
            }, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                    $('#rankmath_keyword_btn').val('Set Title as Focus Keyword').prop('disabled', false);
                }
            }).fail(function() {
                alert('Request failed');
                $('#rankmath_keyword_btn').val('Set Title as Focus Keyword').prop('disabled', false);
            });
        });
    });
    </script>
    <?php
}

/**
 * Process the AJAX request
 */
function process_titles_as_keywords() {
    // Verify security
    check_ajax_referer('rankmath_set_keywords', 'security');
    
    if (!isset($_POST['posts']) || !is_array($_POST['posts'])) {
        wp_send_json_error('No posts selected');
        exit;
    }
    
    $post_ids = array_map('intval', $_POST['posts']);
    $updated = 0;
    
    foreach ($post_ids as $post_id) {
        $post = get_post($post_id);
        
        if (!$post) {
            continue;
        }
        
        // Simple update of focus keyword
        update_post_meta($post_id, 'rank_math_focus_keyword', $post->post_title);
        $updated++;
    }
    
    wp_send_json_success(array(
        'message' => sprintf('Successfully updated %d items.', $updated),
        'count' => $updated
    ));
}