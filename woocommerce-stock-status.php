<?php
/**
 * WooCommerce Stock Status Display
 * 
 * Displays product stock status on single product pages
 * 
 * @package WordPress
 * @subpackage WooCommerce Extensions
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Display product stock status
 * 
 * Shows availability status for WooCommerce products
 */
add_action('woocommerce_single_product_summary', 'show_product_stock_status', 9);

function show_product_stock_status() {
    global $product;
    
    if (!$product) {
        return;
    }
    
    if ($product->is_in_stock()) {
        echo '<p class="status">Availability: <strong class="availability in-stock">In Stock!</strong></p>';
    } else {
        echo '<p class="status">Availability: <strong class="availability out-of-stock">Limited Stock</strong></p>';
    }
}