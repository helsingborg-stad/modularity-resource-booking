<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5c65d682e3a62',
    'title' => __('Customer actions', 'modularity-resource-booking'),
    'fields' => array(
        0 => array(
            'key' => 'field_5c65d688670c1',
            'label' => __('New customer account', 'modularity-resource-booking'),
            'name' => 'actions_new_customer_account',
            'type' => 'post_object',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'post_type' => array(
                0 => 'modularity-rb-mail',
            ),
            'taxonomy' => '',
            'allow_null' => 1,
            'multiple' => 1,
            'return_format' => 'id',
            'ui' => 1,
        ),
        1 => array(
            'key' => 'field_5c65d87b7bd48',
            'label' => __('New order', 'modularity-resource-booking'),
            'name' => 'actions_new_order',
            'type' => 'post_object',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'post_type' => array(
                0 => 'modularity-rb-mail',
            ),
            'taxonomy' => '',
            'allow_null' => 1,
            'multiple' => 1,
            'return_format' => 'id',
            'ui' => 1,
        ),
        2 => array(
            'key' => 'field_5c65e33c6382f',
            'label' => __('Customer cancel order', 'modularity-resource-booking'),
            'name' => 'actions_customer_cancel_order',
            'type' => 'post_object',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'post_type' => array(
                0 => 'modularity-rb-mail',
            ),
            'taxonomy' => '',
            'allow_null' => 1,
            'multiple' => 1,
            'return_format' => 'id',
            'ui' => 1,
        ),
        3 => array(
            'key' => 'field_5c65e46c5db45',
            'label' => __('Customer account approved', 'modularity-resource-booking'),
            'name' => 'actions_customer_account_approved',
            'type' => 'post_object',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'post_type' => array(
                0 => 'modularity-rb-mail',
            ),
            'taxonomy' => '',
            'allow_null' => 1,
            'multiple' => 1,
            'return_format' => 'id',
            'ui' => 1,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'resource-booking-options',
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