<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5c52a6c8c0db5',
    'title' => __('Customer group tax setting', 'modularity-resource-booking'),
    'fields' => array(
        0 => array(
            'key' => 'field_5c52a6d1658bf',
            'label' => __('Show incl. vat', 'modularity-resource-booking'),
            'name' => 'mod_rb_include_tax_in_price',
            'type' => 'true_false',
            'instructions' => __('Toggles the indicator to show if the price is with or without tax. This setting will not affect or calculate the sum of products to reflect a value with tax.', 'modularity-resource-booking'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => '',
            'default_value' => 0,
            'ui' => 1,
            'ui_on_text' => __('incl. vat', 'modularity-resource-booking'),
            'ui_off_text' => __('excl. vat', 'modularity-resource-booking'),
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'taxonomy',
                'operator' => '==',
                'value' => 'customer_group',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
));
}