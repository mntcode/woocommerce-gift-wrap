<?php
/**
 * Created by PhpStorm.
 * User: matthewcroston
 * Date: 12/11/2018
 * Time: 15:03
 */

class WC_GiftWrap {

    private $default_giftwrap_message = 'Gift wrap this product';

    public function __construct() {
        // Display on backend product admin pages.
        add_filter('woocommerce_product_data_tabs', array($this, 'add_giftwrap_product_tab'));
        add_filter('woocommerce_product_data_panels', array($this, 'giftwrap_product_panel_content')); // Requires WC 2.6+

        // Save the product meta options.
        add_action('woocommerce_process_product_meta_simple', array($this, 'save_giftwrap_fields'));
        add_action('woocommerce_process_product_meta_variable', array($this, 'save_giftwrap_fields'));

        // Display checkbox on product pages.
        add_action('woocommerce_after_add_to_cart_button', array($this, 'product_page_html'));
    }

    public function add_giftwrap_product_tab($tabs) {
        $tabs['giftwrap'] = array(
            'label'     => __('Gift Wrap', 'woocommerce'),
            'target'    => 'giftwrap_options',
            'class'     => array('show_if_simple', 'show_if_variable')
        );

        return $tabs;
    }

    public function giftwrap_product_panel_content() {
        wc_get_template('giftwrap_panel.php', array(
                'default_giftwrap_message' => $this->default_giftwrap_message
        ), 'woocommerce-gift-wrap', WC_GIFTWRAP_PATH . '/templates/');
    }

    public function save_giftwrap_fields($post_id) {
        $giftwrap_enabled = isset($_POST['_giftwrapping_enabled']) ? 'yes' : 'no';
        update_post_meta($post_id, '_giftwrapping_enabled', $giftwrap_enabled);

        if (isset($_POST['_giftwrapping_cost'])) {
            update_post_meta($post_id, '_giftwrapping_cost', wc_clean($_POST['_giftwrapping_cost']));
        }

        if (isset($_POST['_giftwrapping_message'])) {
            update_post_meta($post_id, '_giftwrapping_message', wc_clean($_POST['_giftwrapping_message']));
        }
    }

    public function product_page_html() {
        $is_wrappable = get_post_meta(get_the_ID(), '_giftwrapping_enabled', true) == 'yes';

        if ($is_wrappable) {
            $is_checked = !empty($_REQUEST['gift_wrap_item']); // Allows choice to persist back/forward navigation.
            $giftwrapping_cost = get_post_meta(get_the_ID(), '_giftwrapping_cost', true);
            $giftwrapping_message = get_post_meta(get_the_ID(), '_giftwrapping_message', true);

            if ($giftwrapping_cost == '') { $giftwrapping_cost = 0; }
            if ($giftwrapping_message == '') { $giftwrapping_message = $this->default_giftwrap_message; }

            wc_get_template('product_page_checkbox.php', array(
                'is_checked'                => $is_checked,
                'giftwrapping_cost'         => $giftwrapping_cost,
                'giftwrapping_message'      => $giftwrapping_message
            ), 'woocommerce-gift-wrap', WC_GIFTWRAP_PATH . '/templates/');
        }
    }
}