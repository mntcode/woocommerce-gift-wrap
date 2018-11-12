<?php
/**
 * Created by PhpStorm.
 * User: matthewcroston
 * Date: 12/11/2018
 * Time: 15:03
 */

class WC_GiftWrap {

    public function __construct() {
        // Display on backend product admin pages.
        add_filter('woocommerce_product_data_tabs', array($this, 'add_giftwrap_product_panel'));
    }

    public function add_giftwrap_product_panel($tabs) {
        $tabs['giftwrap'] = array(
            'label'     => __('Gift Wrap', 'woocommerce'),
            'target'    => 'giftwrap_options',
            'class'     => array('show_if_simple', 'show_if_variable')
        );

        return $tabs;
    }

}