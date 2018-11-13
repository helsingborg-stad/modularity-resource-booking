<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5beacf4f7895b',
    'title' => __('Product details', 'modularity-resource-booking'),
    'fields' => array(
        0 => array(
            'key' => 'field_5beacf5dac8a7',
            'label' => __('Price', 'modularity-resource-booking'),
            'name' => 'product_price',
            'type' => 'number',
            'instructions' => __('A price for this specific product. This is used as a base to calculate the product package price.', 'modularity-resource-booking'),
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => 0,
            'placeholder' => '',
            'prepend' => '',
            'append' => __(':-', 'modularity-resource-booking'),
            'min' => '',
            'max' => '',
            'step' => '',
        ),
        1 => array(
            'key' => 'field_5bead0faffc0e',
            'label' => __('Product location', 'modularity-resource-booking'),
            'name' => 'product_location',
            'type' => 'google_map',
            'instructions' => __('Where the resource is located', 'modularity-resource-booking'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'center_lat' => '56.0349681',
            'center_lng' => '12.6612786',
            'zoom' => 12,
            'height' => 400,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'product',
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