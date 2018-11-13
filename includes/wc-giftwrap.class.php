<?php
/**
 * Created by PhpStorm.
 * User: matthewcroston
 * Date: 12/11/2018
 * Time: 15:03
 */

class WC_GiftWrap {

    private $default_giftwrap_cost = 0;
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

        // Add meta to order.
        add_filter('woocommerce_add_cart_item_data', array($this, 'add_cart_item_data'), 10, 2);
        add_filter('woocommerce_add_cart_item', array($this, 'add_cart_item'), 10, 1);
        add_filter('woocommerce_get_item_data', array($this, 'get_item_data'), 10, 2);
        add_filter('woocommerce_get_cart_item_from_session', array($this, 'get_cart_item_from_session'), 10, 2);
        add_action('woocommerce_add_order_item_meta', array($this, 'add_order_item_meta'), 10, 2);
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

            if ($giftwrapping_cost == '') { $giftwrapping_cost = $this->default_giftwrap_cost; }
            if ($giftwrapping_message == '') { $giftwrapping_message = $this->default_giftwrap_message; }

            wc_get_template('product_page_checkbox.php', array(
                'is_checked'                => $is_checked,
                'giftwrapping_cost'         => $giftwrapping_cost,
                'giftwrapping_message'      => $giftwrapping_message
            ), 'woocommerce-gift-wrap', WC_GIFTWRAP_PATH . '/templates/');
        }
    }

    public function add_cart_item_data($cart_item_data, $product_id) {
        $is_wrappable = get_post_meta($product_id, '_giftwrapping_enabled', true) == 'yes';

        if (!empty($_POST['gift_wrap_item']) && $is_wrappable) {
            $cart_item_data['gift_wrap_item'] = true;
        }

        return $cart_item_data;
    }

    public function add_cart_item($cart_item_data) {
        if (!empty($cart_item_data['gift_wrap_item'])) {
            $current_price = $cart_item_data['data']->get_price();
            $giftwrapping_cost = get_post_meta($cart_item_data['product_id'], '_giftwrapping_cost', true);

            if ($giftwrapping_cost == '') {
                $giftwrapping_cost = $this->default_giftwrap_cost;
            }

            $cart_item_data['data']->set_price($current_price + $giftwrapping_cost);
        }

        return $cart_item_data;
    }

    public function get_item_data($item_data, $cart_item) {
        if (!empty($cart_item['gift_wrap_item'])) {
            $item_data[] = array(
                'name'      => __('Gift Wrapped', 'woocommerce'),
                'value'     => __('Yes', 'woocommerce'),
                'display'   => __('Yes', 'woocommerce')
            );
        }

        return $item_data;
    }

    public function get_cart_item_from_session($cart_item, $values) {
        if (!empty($values['gift_wrap_item'])) {
            $cart_item['gift_wrap_item'] = true;
            $current_price = $cart_item['data']->get_price();
            $giftwrapping_cost = get_post_meta($cart_item['data']->id, '_giftwrapping_cost', true);

            if ($giftwrapping_cost == '') {
                $giftwrapping_cost = $this->default_giftwrap_cost;
            }

            $cart_item['data']->set_price($current_price + $giftwrapping_cost);
        }

        return $cart_item;
    }

    public function add_order_item_meta($item_id, $cart_item) {
        if (!empty($cart_item['gift_wrap_item'])) {
            wc_add_order_item_meta($item_id, __('Gift Wrapped', 'woocommerce'), __('Yes', 'woocommerce') );
        }
    }
}