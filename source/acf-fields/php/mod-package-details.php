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
        1 => array(
            'key' => 'field_5bfe9bc2badac',
            'label' => __('Customer group price variations', 'modularity-resource-booking'),
            'name' => 'customer_group_price_variations',
            'type' => 'repeater',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'collapsed' => '',
            'min' => 0,
            'max' => 0,
            'layout' => 'table',
            'button_label' => '',
            'sub_fields' => array(
                0 => array(
                    'key' => 'field_5bfe9c16badad',
                    'label' => __('Product price', 'modularity-resource-booking'),
                    'name' => 'product_price',
                    'type' => 'number',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '50',
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
                1 => array(
                    'key' => 'field_5bfe9c5cbadae',
                    'label' => __('Customer group', 'modularity-resource-booking'),
                    'name' => 'customer_group',
                    'type' => 'taxonomy',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '50',
                        'class' => '',
                        'id' => '',
                    ),
                    'taxonomy' => 'customer_group',
                    'field_type' => 'select',
                    'allow_null' => 0,
                    'add_term' => 0,
                    'save_terms' => 0,
                    'load_terms' => 0,
                    'return_format' => 'object',
                    'multiple' => 0,
                ),
            ),
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