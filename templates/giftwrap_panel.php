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
                'placeholder'   => $default_giftwrap_message,
                'desc_tip'      => true,
                'description'   => __('Change the default message that is shown alongside the checkbox on product pages.')
            )
        );

        ?>
    </div>
</div>