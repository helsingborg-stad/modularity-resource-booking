<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5bead7869a8ed',
    'title' => __('Package details', 'modularity-resource-booking'),
    'fields' => array(
        0 => array(
            'key' => 'field_5bead790418bf',
            'label' => __('Package price', 'modularity-resource-booking'),
            'name' => 'package_price',
            'type' => 'number',
            'instructions' => __('We are automatically calculating a price from the procuct prices, but you may override this by entering a package price in this field.', 'modularity-resource-booking'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => __(':-', 'modularity-resource-booking'),
            'min' => '',
            'max' => '',
            'step' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'taxonomy',
                'operator' => '==',
                'value' => 'product-package',
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