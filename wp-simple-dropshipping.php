<?php
/*
Plugin Name: WP Simple Dropshipping
Description: Adds dropshipping functionality for Prestige Import products to your WooCommerce store.
Version: 1.0
Author: Raihan
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include the core plugin class
include_once plugin_dir_path(__FILE__) . 'includes/class-pidropshipping.php';

// Initialize the plugin
function pidropshipping_init() {
    new PIDropshipping();
}
add_action('plugins_loaded', 'pidropshipping_init');