<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5beacf4f7895b',
    'title' => 'Product details',
    'fields' => array(
        0 => array(
            'key' => 'field_5beacf5dac8a7',
            'label' => __('Base price', 'modularity-resource-booking'),
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
            'key' => 'field_5bfe9585f90f4',
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
            'collapsed' => 'field_5bfe95b8f90f5',
            'min' => 0,
            'max' => 0,
            'layout' => 'table',
            'button_label' => __('Add price variation', 'modularity-resource-booking'),
            'sub_fields' => array(
                0 => array(
                    'key' => 'field_5bfe95b8f90f5',
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
                    'key' => 'field_5bfe95daf90f6',
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
                    'return_format' => 'id',
                    'multiple' => 0,
                ),
            ),
        ),
        2 => array(
            'key' => 'field_5bfe75f6f07f6',
            'label' => __('Items in stock', 'modularity-resource-booking'),
            'name' => 'items_in_stock',
            'type' => 'number',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => 1,
            'placeholder' => '',
            'prepend' => __('We have', 'modularity-resource-booking'),
            'append' => __('items in stock', 'modularity-resource-booking'),
            'min' => '',
            'max' => '',
            'step' => '',
        ),
        3 => array(
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