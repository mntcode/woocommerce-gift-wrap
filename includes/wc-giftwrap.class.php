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
        add_action('woocommerce_process_product_meta_simple', 'save_giftwrap_fields');
        add_action('woocommerce_process_product_meta_variable', 'save_giftwrap_fields');
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
        ?>

        <div id="giftwrap_options" class="panel woocommerce_options_panel">
            <div class="options_group">
                <?php

                woocommerce_wp_checkbox(
                    array(
                        'id'            => '_giftwrapping_enabled',
                        'label'         => __('Enable Gift Wrapping', 'woocommerce')
                    )
                );

                woocommerce_wp_text_input(
                    array(
                        'id'            => '_giftwrapping_cost',
                        'label'         => __('Gift Wrap Cost', 'woocommerce'),
                        'placeholder'   => '0',
                        'desc_tip'      => true,
                        'description'   => __('Add an additional cost when the gift wrapping option is enabled.')
                    )
                );

                woocommerce_wp_text_input(
                    array(
                        'id'            => '_giftwrapping_message',
                        'label'         => __('Gift Wrap Message', 'woocommerce'),
                        'placeholder'   => $this->default_giftwrap_message,
                        'desc_tip'      => true,
                        'description'   => __('Change the default message that is shown alongside the checkbox on product pages.')
                    )
                );

                ?>
            </div>
        </div>

        <?php
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
}