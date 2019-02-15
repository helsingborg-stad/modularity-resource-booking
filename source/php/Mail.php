<?php

namespace ModularityResourceBooking;

class Mail extends \ModularityResourceBooking\Entity\PostType
{
    public static $postTypeSlug;

    public function __construct()
    {
        //Main post type
        self::$postTypeSlug = $this->postType();

        new \ModularityResourceBooking\Mail\Controller();
        new \ModularityResourceBooking\Mail\Recipients();
    }

    /**
     * Create post typeMail
     *
     * @return void
     */
    public function postType() : string
    {
        // Create posttype
        $postType = new \ModularityResourceBooking\Entity\PostType(
            _x('Mail Templates', 'Post type plural', 'modularity-resource-booking'),
            _x('Mail Template', 'Post type singular', 'modularity-resource-booking'),
            'modularity-rb-mail',
            array(
                'description'          =>   __('Mail actions', 'modularity-resource-booking'),
                'menu_icon'            =>   'dashicons-email-alt',
                'public'               =>   false,
                'publicly_queriable'   =>   true,
                'show_ui'              =>   true,
                'show_in_nav_menus'    =>   false,
                'has_archive'          =>   false,
                'rewrite'              =>   array(
                    'slug'       =>   __('modularity-rb-mail', 'modularity-resource-booking'),
                    'with_front' =>   false
                ),
                'hierarchical'          =>  false,
                'exclude_from_search'   =>  true,
                'taxonomies'            =>  array(),
                'supports'              =>  array('title', 'revisions')
            )
        );

        /* Return the slug */
        return $postType->slug;
    }
}
