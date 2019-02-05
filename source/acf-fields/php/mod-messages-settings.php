<?php 



    'key' => 'group_5c5963e405f7b',
    'title' => __('Messages', 'modularity-resource-booking'),
    'fields' => array(
        0 => array(
            'key' => 'field_5c59642286228',
            'label' => __('Registered order', 'modularity-resource-booking'),
            'name' => 'registered_order',
            'type' => 'wysiwyg',
            'instructions' => __('This message will appear when successfully submitting a new order through the booking form.', 'modularity-resource-booking'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'tabs' => 'visual',
            'toolbar' => 'basic',
            'media_upload' => 1,
            'delay' => 0,
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
    'menu_order' => 99,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => 1,
    'description' => '',
));
