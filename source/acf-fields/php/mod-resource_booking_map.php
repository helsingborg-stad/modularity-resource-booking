<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5c4acdd9a5388',
    'title' => 'Resource Booking Package map',
    'fields' => array(
        0 => array(
            'key' => 'field_5c4acdec2c771',
            'label' => __('Package', 'modularity-resource-booking'),
            'name' => 'resource_booking_map_package',
            'type' => 'taxonomy',
            'instructions' => __('Select package', 'modularity-resource-booking'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'taxonomy' => 'product-package',
            'field_type' => 'select',
            'allow_null' => 0,
            'add_term' => 1,
            'save_terms' => 0,
            'load_terms' => 0,
            'return_format' => 'id',
            'multiple' => 0,
        ),
        1 => array(
            'key' => 'field_5c4f0e22f4ee1',
            'label' => __('Google API Key', 'modularity-resource-booking'),
            'name' => 'resource_booking_map_apikey',
            'type' => 'text',
            'instructions' => __('Add your google API key', 'modularity-resource-booking'),
            'required' => 0,
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
            'maxlength' => '',
        ),
        2 => array(
            'key' => 'field_5c4ad7e4c2661',
            'label' => __('Map Location', 'modularity-resource-booking'),
            'name' => 'resource_booking_map_location',
            'type' => 'google_map',
            'instructions' => __('Add area name of you resources (Helsingborg)', 'modularity-resource-booking'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'center_lat' => '56.0465',
            'center_lng' => '12.6945',
            'zoom' => 12,
            'height' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'mod-rb-package-map',
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