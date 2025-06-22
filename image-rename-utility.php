<?php
/**
 * Image Rename Utility
 * 
 * Automatically renames uploaded images with site name and random number
 * 
 * @package WordPress
 * @subpackage Media Utilities
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generate random number for filename
 * 
 * @param int $length Length of random number (default: 6)
 * @return string Random number string
 */
function generate_random_number($length = 6) {
    $chars = '0123456789';
    $result = '';
    for ($i = 0; $i < $length; $i++) {
        $result .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $result;
}

/**
 * Rename uploaded images with site name and random number
 * 
 * @param array $file File data array
 * @return array Modified file data array
 */
function rename_uploaded_images($file) {
    $site_name = get_bloginfo('name');
    
    // Generate a random number
    $random_number = generate_random_number();
    
    // Create new filename: sitename-randomnumber.extension
    $file['name'] = $site_name . '-' . $random_number . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    
    return $file;
}

// Hook the rename function to file uploads
add_filter('wp_handle_upload_prefilter', 'rename_uploaded_images');