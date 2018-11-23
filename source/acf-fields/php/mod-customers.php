<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5bf50cc73ff8a',
    'title' => __('Customer settings', 'modularity-resource-booking'),
    'fields' => array(
        0 => array(
            'key' => 'field_5bf50da4a7e84',
            'label' => __('Customer group', 'modularity-resource-booking'),
            'name' => 'customer_group',
            'type' => 'taxonomy',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'taxonomy' => 'customer_group',
            'field_type' => 'checkbox',
            'add_term' => 0,
            'save_terms' => 1,
            'load_terms' => 1,
            'return_format' => 'id',
            'multiple' => 0,
            'allow_null' => 0,
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'user_form',
                'operator' => '==',
                'value' => 'all',
            ),
            1 => array(
                'param' => 'user_role',
                'operator' => '==',
                'value' => 'customer',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'acf_after_title',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
));
}