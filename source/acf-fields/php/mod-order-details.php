<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5bed425d9abc2',
    'title' => 'Order details',
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
            'key' => 'field_5c0fc16aaefa4',
            'label' => __('Articles (hidden)', 'modularity-resource-booking'),
            'name' => 'order_articles',
            'type' => 'repeater',
            'instructions' => __('List of ordered articles.', 'modularity-resource-booking'),
            'required' => 0,
            'conditional_logic' => 1,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'collapsed' => '',
            'min' => 0,
            'max' => 0,
            'layout' => 'block',
            'button_label' => '',
            'sub_fields' => array(
                0 => array(
                    'key' => 'field_5c122674bc676',
                    'label' => __('Type', 'modularity-resource-booking'),
                    'name' => 'type',
                    'type' => 'select',
                    'instructions' => __('The article type.', 'modularity-resource-booking'),
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '33',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        'product' => __('Product', 'modularity-resource-booking'),
                        'package' => __('Package', 'modularity-resource-booking'),
                    ),
                    'default_value' => array(
                        0 => __('product', 'modularity-resource-booking'),
                    ),
                    'allow_null' => 0,
                    'multiple' => 0,
                    'ui' => 0,
                    'return_format' => 'value',
                    'ajax' => 0,
                    'placeholder' => '',
                ),
                1 => array(
                    'key' => 'field_5bed43f2bf1f2',
                    'label' => __('Article', 'modularity-resource-booking'),
                    'name' => 'article_id',
                    'type' => 'number',
                    'instructions' => __('The article that the customer has ordered.', 'modularity-resource-booking'),
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '33',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'min' => '',
                    'max' => '',
                    'step' => '',
                ),
                2 => array(
                    'key' => 'field_5c0fc17caefa5',
                    'label' => __('Slot', 'modularity-resource-booking'),
                    'name' => 'slot_id',
                    'type' => 'text',
                    'instructions' => __('Booked time interval.', 'modularity-resource-booking'),
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '33',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ),
            ),
        ),
        3 => array(
            'key' => 'field_5c12369d5bc92',
            'label' => __('Articles', 'modularity-resource-booking'),
            'name' => '',
            'type' => 'message',
            'instructions' => __('List of ordered articles.', 'modularity-resource-booking'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => '',
            'new_lines' => 'wpautop',
            'esc_html' => 0,
        ),
        4 => array(
            'key' => 'field_5bed44c343f42',
            'label' => __('Order Status', 'modularity-resource-booking'),
            'name' => 'order_status',
            'type' => 'taxonomy',
            'instructions' => '',
            'required' => 1,
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
            'save_terms' => 1,
            'load_terms' => 1,
            'return_format' => 'id',
            'multiple' => 0,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'purchase',
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