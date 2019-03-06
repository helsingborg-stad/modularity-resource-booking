<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5bfe8d5fdeedd',
    'title' => __('Customer account', 'modularity-resource-booking'),
    'fields' => array(
        0 => array(
            'key' => 'field_5c73361b0f27d',
            'label' => __('Account status', 'modularity-resource-booking'),
            'name' => 'customer_account_active',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'message' => '',
            'default_value' => 0,
            'ui' => 1,
            'ui_on_text' => __('Active', 'modularity-resource-booking'),
            'ui_off_text' => __('Disabled', 'modularity-resource-booking'),
        ),
        1 => array(
            'key' => 'field_5bfe8eb5174c1',
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
            'field_type' => 'select',
            'allow_null' => 0,
            'add_term' => 1,
            'save_terms' => 1,
            'load_terms' => 0,
            'return_format' => 'id',
            'multiple' => 0,
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
        1 => array(
            0 => array(
                'param' => 'user_role',
                'operator' => '==',
                'value' => 'resource_admin',
            ),
        ),
        2 => array(
            0 => array(
                'param' => 'user_role',
                'operator' => '==',
                'value' => 'administrator',
            ),
        ),
    ),
    'menu_order' => 99999,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => array(
        0 => 'permalink',
        1 => 'the_content',
        2 => 'excerpt',
        3 => 'discussion',
        4 => 'comments',
        5 => 'revisions',
        6 => 'slug',
        7 => 'author',
        8 => 'format',
        9 => 'page_attributes',
        10 => 'featured_image',
        11 => 'categories',
        12 => 'tags',
        13 => 'send-trackbacks',
    ),
    'active' => 1,
    'description' => '',
));
}