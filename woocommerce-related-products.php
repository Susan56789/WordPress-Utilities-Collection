<?php
/**
 * WooCommerce Related Products Enhancement
 * 
 * Override WooCommerce related products to prioritize main category
 * but fill from other selected categories
 * 
 * @package WordPress
 * @subpackage WooCommerce Extensions
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get related products by multiple categories
 * 
 * Prioritizes main category but fills from other selected categories
 * 
 * @param array $related_posts Existing related posts
 * @param int $product_id Current product ID
 * @param array $args Query arguments
 * @return array Modified related posts array
 */
function related_products_by_multiple_categories($related_posts, $product_id, $args) {
    $product = wc_get_product($product_id);

    // Get all categories assigned to the product
    $categories = wp_get_post_terms($product_id, 'product_cat', array('orderby' => 'parent', 'order' => 'DESC'));

    if ($categories && !is_wp_error($categories)) {
        $main_category = null;
        $other_categories = [];

        foreach ($categories as $category) {
            if ($main_category === null) {
                $main_category = $category; // First category found (treat as main)
            } else {
                $other_categories[] = $category; // Store other selected categories
            }
        }

        $related_posts = [];
        $needed_posts = 4; // Ensure at least 4 related products are displayed

        if ($main_category) {
            // Fetch related products from the main category
            $main_args = array(
                'post_type'      => 'product',
                'posts_per_page' => $needed_posts,
                'post__not_in'   => array($product_id),
                'fields'         => 'ids',
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'term_id',
                        'terms'    => $main_category->term_id,
                    ),
                ),
            );

            $main_query = new WP_Query($main_args);

            if ($main_query->have_posts()) {
                $related_posts = $main_query->posts;
            }
        }

        // If less than 4 products found, fill from other selected categories
        if (count($related_posts) < $needed_posts && !empty($other_categories)) {
            $remaining_needed = $needed_posts - count($related_posts);
            $other_category_ids = wp_list_pluck($other_categories, 'term_id'); // Get IDs of other categories

            $other_args = array(
                'post_type'      => 'product',
                'posts_per_page' => $remaining_needed,
                'post__not_in'   => array_merge($related_posts, array($product_id)), // Exclude already selected products
                'fields'         => 'ids',
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'term_id',
                        'terms'    => $other_category_ids,
                    ),
                ),
            );

            $other_query = new WP_Query($other_args);

            if ($other_query->have_posts()) {
                $related_posts = array_merge($related_posts, $other_query->posts);
            }
        }
    }

    return $related_posts;
}

// Hook the function to override WooCommerce related products
add_filter('woocommerce_related_products', 'related_products_by_multiple_categories', 10, 3);