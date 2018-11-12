<?php
/*
Plugin Name: WooCommerce Gift Wrap
Plugin URI: https://github.com/mntcode/woocommerce-gift-wrap
Description: Add a customisation option to your products, this can be free or charged.
Version: 1.0
Author: Matthew Croston
Author URI: https://mtcode.co.uk/
Requires at least: 3.5
Tested up to: 4.9.8
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Include the main gift wrap class.
if (!class_exists('WC_GiftWrap')) {
    include_once dirname(__FILE__) . '/includes/wc-giftwrap.class.php';
}

new WC_GiftWrap();