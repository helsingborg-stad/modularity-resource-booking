<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5c063df1cb24f',
    'title' => __('Email settings', 'modularity-resource-booking'),
    'fields' => array(
        0 => array(
            'key' => 'field_5c063e124e657',
            'label' => __('Manager email', 'modularity-resource-booking'),
            'name' => 'mod_rb_manager_email',
            'type' => 'email',
            'instructions' => __('A email that will be contacted if there are any new user registrations and other requests that requires administration.', 'modularity-resource-booking'),
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
        ),
        1 => array(
            'key' => 'field_5c063e7f2530b',
            'label' => __('Economy email', 'modularity-resource-booking'),
            'name' => 'mod_rb_economy_email',
            'type' => 'email',
            'instructions' => __('Where to send automatically generated invoices.', 'modularity-resource-booking'),
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-resource-booking',
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