<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5bffb822b9213',
    'title' => __('Product media requirements', 'modularity-resource-booking'),
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
            'max' => 1,
            'layout' => 'table',
            'button_label' => __('Lägg till materialkrav', 'modularity-resource-booking'),
            'sub_fields' => array(
                0 => array(
                    'key' => 'field_5bffb838f14ac',
                    'label' => __('Media name (optional)', 'modularity-resource-booking'),
                    'name' => 'media_name',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
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
                    'key' => 'field_5c49ac9c5e242',
                    'label' => __('File types', 'modularity-resource-booking'),
                    'name' => 'file_types',
                    'type' => 'checkbox',
                    'instructions' => __('Accepted file types.', 'modularity-resource-booking'),
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        'image/jpeg' => __('JPG', 'modularity-resource-booking'),
                        'image/png' => __('PNG', 'modularity-resource-booking'),
                        'video/mp4' => __('MP4', 'modularity-resource-booking'),
                        'application/pdf' => __('PDF', 'modularity-resource-booking'),
                    ),
                    'allow_custom' => 0,
                    'default_value' => array(
                    ),
                    'layout' => 'vertical',
                    'toggle' => 0,
                    'return_format' => 'value',
                    'save_custom' => 0,
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
                    'label' => __('Width', 'modularity-resource-booking'),
                    'name' => 'image_width',
                    'type' => 'number',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_5bffb829f14ab',
                                'operator' => '!=empty',
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
                    'label' => __('Height', 'modularity-resource-booking'),
                    'name' => 'image_height',
                    'type' => 'number',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => array(
                        0 => array(
                            0 => array(
                                'field' => 'field_5bffb829f14ab',
                                'operator' => '!=empty',
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