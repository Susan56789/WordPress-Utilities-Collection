# WordPress Utilities Collection

A collection of useful WordPress PHP utilities for enhancing functionality across various aspects of WordPress, WooCommerce, and SEO optimization.

## 📁 File Structure

```
wordpress-utilities/
├── rankmath-title-to-keyword.php
├── woocommerce-stock-status.php
├── image-rename-utility.php
├── auto-alt-text.php
├── woocommerce-related-products.php
└── README.md
```

## 🚀 Features Overview

### 1. RankMath Title to Focus Keyword (`rankmath-title-to-keyword.php`)

- **Purpose**: Bulk set post titles as RankMath focus keywords
- **Features**:
  - Adds a "Set Title as Focus Keyword" button to post list pages
  - Supports bulk operations for posts, pages, and products
  - AJAX-powered with security nonces
  - Real-time progress feedback

### 2. WooCommerce Stock Status (`woocommerce-stock-status.php`)

- **Purpose**: Display product availability status on single product pages
- **Features**:
  - Shows "In Stock!" or "Limited Stock" messages
  - Customizable CSS classes for styling
  - Integrates seamlessly with WooCommerce product pages

### 3. Image Rename Utility (`image-rename-utility.php`)

- **Purpose**: Automatically rename uploaded images for better SEO
- **Features**:
  - Renames images to: `sitename-randomnumber.extension`
  - Uses site name from WordPress settings
  - Generates 6-digit random numbers
  - Maintains original file extensions

### 4. Auto Alt Text (`auto-alt-text.php`)

- **Purpose**: Automatically set alt text for images based on post titles
- **Features**:
  - Sets alt text when posts are saved
  - Works with posts, pages, and products
  - Bulk update functionality for existing content
  - Admin interface with one-click update button
  - Only updates empty alt text fields

### 5. WooCommerce Related Products (`woocommerce-related-products.php`)

- **Purpose**: Enhanced related products logic for WooCommerce
- **Features**:
  - Prioritizes main product category
  - Falls back to other assigned categories
  - Ensures minimum of 4 related products
  - Improves product discovery and cross-selling

## 📋 Requirements

- WordPress 5.0+
- PHP 7.4+
- **Optional Dependencies**:
  - RankMath SEO plugin (for `rankmath-title-to-keyword.php`)
  - WooCommerce plugin (for `woocommerce-*.php` files)

## 🔧 Installation

### Method 1: Individual Files

1. Download the specific PHP files you need
2. Upload to your theme's `functions.php` file or create a custom plugin
3. Activate the functionality

### Method 2: As a Plugin

1. Create a new folder in `/wp-content/plugins/`
2. Add all files to the folder
3. Create a main plugin file that includes all utilities
4. Activate through WordPress admin

### Method 3: In functions.php

```php
// Add to your theme's functions.php
require_once get_template_directory() . '/utilities/rankmath-title-to-keyword.php';
require_once get_template_directory() . '/utilities/woocommerce-stock-status.php';
// ... add other files as needed
```

## 🎯 Usage Instructions

### RankMath Title to Keyword

1. Go to Posts/Pages/Products admin list
2. Select items using checkboxes
3. Click "Set Title as Focus Keyword" button
4. Confirmation message will appear

### Stock Status Display

- Automatically displays on WooCommerce single product pages
- Customize styling with CSS classes: `.in-stock` and `.out-of-stock`

### Image Renaming

- Works automatically on all new image uploads
- No configuration needed

### Auto Alt Text

- **Automatic**: Alt text set when saving posts
- **Manual**: Use admin button to update all existing images

### Related Products

- Automatically enhances WooCommerce related products
- No configuration required

## 🎨 Customization

### CSS Styling for Stock Status

```css
.status {
    margin: 10px 0;
    font-size: 16px;
}

.availability.in-stock {
    color: #28a745;
}

.availability.out-of-stock {
    color: #dc3545;
}
```

### Modify Random Number Length

```php
// In image-rename-utility.php, change the default parameter
function generate_random_number($length = 8) { // Changed from 6 to 8
    // ... rest of function
}
```

### Adjust Related Products Count

```php
// In woocommerce-related-products.php
$needed_posts = 6; // Change from 4 to 6
```

## 🛡️ Security Features

- **Nonce verification** for AJAX requests
- **Input sanitization** for all user inputs
- **Capability checks** for admin functions
- **Direct access prevention** on all files

## 🐛 Troubleshooting

### Common Issues

**RankMath button not appearing:**

- Ensure RankMath plugin is installed and active
- Check if you're on a supported post type (post, page, product)

**Images not renaming:**

- Verify the file has proper write permissions
- Check if the function is properly hooked

**Alt text not updating:**

- Ensure images are properly attached to posts
- Check admin permissions for bulk update

**Related products not showing:**

- Verify WooCommerce is active
- Ensure products have assigned categories

## 📝 Changelog

### Version 1.0.0

- Initial release with all utilities
- Separated functions into individual files
- Added comprehensive documentation

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is licensed under the GPL v2 or later - see the WordPress licensing for details.

## ⚠️ Important Notes

- **Backup your site** before implementing any utilities
- Test on a staging environment first
- Some utilities require specific plugins (RankMath, WooCommerce)
- Monitor performance impact on large sites

## 🆘 Support

For issues, questions, or feature requests:

1. Check the troubleshooting section
2. Review the code comments
3. Open an issue on GitHub
4. Provide detailed information about your setup

---

**Made with ❤️ for the WordPress community**
