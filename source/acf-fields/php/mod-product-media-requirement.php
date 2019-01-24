<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5bffb822b9213',
    'title' => 'Product media requirements',
    'fields' => array(
        0 => array(
            'key' => 'field_5bffb829f14ab',
            'label' => __('Materialkrav', 'modularity-resource-booking'),
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
            'button_label' => __('Lägg till materialkrav', 'modularity-resource-booking'),
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
                        'width' => '25',
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
                        'width' => '15',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        'image' => __('Image', 'modularity-resource-booking'),
                        'video' => __('Video', 'modularity-resource-booking'),
                    ),
                    'default_value' => array(
                        0 => __('image', 'modularity-resource-booking'),
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
                        'width' => '15',
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
                3 => array(
                    'key' => 'field_5bffbac04a89f',
                    'label' => __('Image width', 'modularity-resource-booking'),
                    'name' => 'image_width',
                    'type' => 'number',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_5bffb857f14ad',
                                'operator' => '==',
                                'value' => 'image',
                            ),
                        ),
                    ),
                    'wrapper' => array(
                        'width' => '23',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => __('px', 'modularity-resource-booking'),
                    'min' => 100,
                    'max' => 10000,
                    'step' => '',
                ),
                4 => array(
                    'key' => 'field_5bffbb1a4a8a0',
                    'label' => __('Image height', 'modularity-resource-booking'),
                    'name' => 'image_height',
                    'type' => 'number',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_5bffb857f14ad',
                                'operator' => '==',
                                'value' => 'image',
                            ),
                        ),
                    ),
                    'wrapper' => array(
                        'width' => '22',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => __('px', 'modularity-resource-booking'),
                    'min' => 100,
                    'max' => 10000,
                    'step' => '',
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