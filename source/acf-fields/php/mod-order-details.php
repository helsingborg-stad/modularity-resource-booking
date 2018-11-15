<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5bed425d9abc2',
    'title' => __('Order details', 'modularity-resource-booking'),
    'fields' => array(
        0 => array(
            'key' => 'field_5bed431057e88',
            'label' => __('OrderID', 'modularity-resource-booking'),
            'name' => '',
            'type' => 'message',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => __('{{ORDER_ID}}', 'modularity-resource-booking'),
            'new_lines' => '',
            'esc_html' => 0,
        ),
        1 => array(
            'key' => 'field_5bed438cc99db',
            'label' => __('Customer', 'modularity-resource-booking'),
            'name' => 'customer_id',
            'type' => 'user',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'role' => '',
            'allow_null' => 0,
            'multiple' => 0,
            'return_format' => 'object',
        ),
        2 => array(
            'key' => 'field_5bed43f2bf1f2',
            'label' => __('Product', 'modularity-resource-booking'),
            'name' => 'product_package_id',
            'type' => 'taxonomy',
            'instructions' => __('The product package that the customer has ordered.', 'modularity-resource-booking'),
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'taxonomy' => 'product-package',
            'field_type' => 'select',
            'allow_null' => 0,
            'add_term' => 0,
            'save_terms' => 0,
            'load_terms' => 0,
            'return_format' => 'object',
            'multiple' => 0,
        ),
        3 => array(
            'key' => 'field_5bed44c343f42',
            'label' => __('Order Status', 'modularity-resource-booking'),
            'name' => 'order_status',
            'type' => 'taxonomy',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'taxonomy' => 'order-status',
            'field_type' => 'radio',
            'allow_null' => 0,
            'add_term' => 0,
            'save_terms' => 0,
            'load_terms' => 0,
            'return_format' => 'id',
            'multiple' => 0,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'order',
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