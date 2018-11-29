<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5bffb822b9213',
    'title' => __('Product media requirements', 'modularity-resource-booking'),
    'fields' => array(
        0 => array(
            'key' => 'field_5bffb829f14ab',
            'label' => __('Media requirement', 'modularity-resource-booking'),
            'name' => 'media_requirement',
            'type' => 'repeater',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'collapsed' => 'field_5bffb838f14ac',
            'min' => 0,
            'max' => 10,
            'layout' => 'table',
            'button_label' => __('Add media requirement', 'modularity-resource-booking'),
            'sub_fields' => array(
                0 => array(
                    'key' => 'field_5bffb838f14ac',
                    'label' => __('Media name', 'modularity-resource-booking'),
                    'name' => 'media_name',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '33',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => __('Enter the media name', 'modularity-resource-booking'),
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => 50,
                ),
                1 => array(
                    'key' => 'field_5bffb857f14ad',
                    'label' => __('Media type', 'modularity-resource-booking'),
                    'name' => 'media_type',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '33',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        'image' => __('Image', 'modularity-resource-booking'),
                        'video' => __('Video', 'modularity-resource-booking'),
                    ),
                    'default_value' => array(
                        0 => 'image',
                    ),
                    'allow_null' => 0,
                    'multiple' => 0,
                    'ui' => 0,
                    'return_format' => 'value',
                    'ajax' => 0,
                    'placeholder' => '',
                ),
                2 => array(
                    'key' => 'field_5bffb89ff14ae',
                    'label' => __('Maxiumum filesize', 'modularity-resource-booking'),
                    'name' => 'maxiumum_filesize',
                    'type' => 'number',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '33',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 15,
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => __('MB', 'modularity-resource-booking'),
                    'min' => 1,
                    'max' => 50,
                    'step' => 1,
                ),
            ),
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'product',
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