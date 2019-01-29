<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5c4ef6a792434',
    'title' => __('Pages', 'modularity-resource-booking'),
    'fields' => array(
        0 => array(
            'key' => 'field_5c4ef6c47c961',
            'label' => __('Sign in page', 'modularity-resource-booking'),
            'name' => 'sign_in_page',
            'type' => 'post_object',
            'instructions' => __('Select the page where the sign in form is placed.', 'modularity-resource-booking'),
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'post_type' => array(
                0 => 'page',
            ),
            'taxonomy' => '',
            'allow_null' => 0,
            'multiple' => 0,
            'return_format' => 'id',
            'ui' => 1,
        ),
        1 => array(
            'key' => 'field_5c5010ca2b9c4',
            'label' => __('Order history page', 'modularity-resource-booking'),
            'name' => 'order_history_page',
            'type' => 'post_object',
            'instructions' => __('Select the page where the order history is placed.', 'modularity-resource-booking'),
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'post_type' => array(
                0 => 'page',
            ),
            'taxonomy' => '',
            'allow_null' => 0,
            'multiple' => 0,
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