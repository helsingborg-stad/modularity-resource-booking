<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5bed90f741b0e',
    'title' => 'Order notations',
    'fields' => array(
        0 => array(
            'key' => 'field_5bed9195a4cd2',
            'label' => __('Notes about this order', 'modularity-resource-booking'),
            'name' => 'order_notations',
            'type' => 'textarea',
            'instructions' => __('Internal instructions or notes about this order.', 'modularity-resource-booking'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => __('No notes has been made about this order yet...', 'modularity-resource-booking'),
            'maxlength' => '',
            'rows' => '',
            'new_lines' => '',
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