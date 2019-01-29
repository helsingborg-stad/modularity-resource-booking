<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5c505a650df0a',
    'title' => __('Brand color', 'modularity-resource-booking'),
    'fields' => array(
        0 => array(
            'key' => 'field_5c505bba20c6b',
            'label' => __('Email brand color', 'modularity-resource-booking'),
            'name' => 'mod_rb_email_brand_color',
            'type' => 'color_picker',
            'instructions' => __('Select a dark brand color to be used in the automatic email from resource booking.', 'modularity-resource-booking'),
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '#cb0050',
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
    'position' => 'side',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
));
}