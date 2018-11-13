<?php

namespace ModularityResourceBooking;

class Orders extends \ModularityResourceBooking\Entity\PostType
{
    public static $postTypeSlug;
    public static $packageTaxonomySlug;

    public function __construct()
    {

        //Main post type
        self::$postTypeSlug = $this->postType();

        //Taxonomy
        //self::$packageTaxonomySlug = $this->taxonomyPackage();

        //Remove form Municipio template filter
        add_filter('Municipio/CustomPostType/ExcludedPostTypes', array($this, 'excludePostType'));
    }

    //Exclude this post type from page template filter.
    public function excludePostType($postTypes)
    {
        $postTypes[] = $this->postType();
        return $postTypes;
    }

    /**
     * Create post type
     *
     * @return void
     */
    public function postType() : string
    {
        // Create posttype
        $postType = new \ModularityResourceBooking\Entity\PostType(
            _x('Orders', 'Post type plural', 'modularity-resource-booking'),
            _x('Order', 'Post type singular', 'modularity-resource-booking'),
            'order',
            array(
                'description'          =>   __('Order registry.', 'modularity-resource-booking'),
                'menu_icon'            =>   'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDYxMS45OTYgNjExLjk5NiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNjExLjk5NiA2MTEuOTk2OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij48Zz48Zz48cGF0aCBkPSJNNTg4LjYzLDExMy4xOTNMMjEzLjgxMiwzMy44NzFjLTE1Ljg1OC0zLjM1NS0zMS41NzYsNi44NzYtMzQuOTMxLDIyLjczNGwtNy4xMjEsNDUuNzYybDQzMi40NzcsOTEuNTE5bDcuMTIxLTQ1Ljc2MiAgICBDNjE0LjcxMywxMzIuMjcyLDYwNC40ODgsMTE2LjU0OSw1ODguNjMsMTEzLjE5M3oiIGZpbGw9IiNGRkZGRkYiLz48cGF0aCBkPSJNNDMxLjAwOSwyMDMuNTkxYy00LjM3OC0xNS43NjYtMjAuODU0LTI1LjA4NS0zNi42MTUtMjAuNzE0TDMyMy4yNCwyMDIuNjNsLTE2Ny43NDItMzUuNWwtMTguNDQ4LDg3LjE2NUwyMS43ODYsMjg2LjI4NyAgICBjLTE1Ljc2LDQuMzcyLTI1LjA3OSwyMC44NDgtMjAuNzA4LDM2LjYwOWw2NC45NTgsMjM0LjA3OGM0LjM3OCwxNS43NiwyMC44NTUsMjUuMDg1LDM2LjYxNSwyMC43MDhsMzcyLjYwOC0xMDMuNDAzICAgIGMxNS43Ni00LjM3OCwyNS4wNzktMjAuODQ4LDIwLjcwOC0zNi42MTVsLTExLjE1LTQwLjE4NGw0MS43ODksOC44MzVjMTUuODU4LDMuMzYxLDMxLjU3Ni02Ljg3LDM0LjkzMS0yMi43MjhsMjYuNDM5LTEyNC45MzcgICAgTDQzNy40NSwyMjYuNzk3TDQzMS4wMDksMjAzLjU5MXogTTQ3NC4wNCwzMjIuNTU5bDkuMjE1LTQzLjU1MmMxLjM4NC02LjUyMSw3Ljg1LTEwLjcyNywxNC4zNy05LjM1bDQzLjU1Miw5LjIyMSAgICBjNi41MjcsMS4zODQsMTAuNzMzLDcuODQzLDkuMzU2LDE0LjM3bC05LjIxNSw0My41NTJjLTEuMzg0LDYuNTIxLTcuODQ5LDEwLjczMy0xNC4zNyw5LjM1bC00My41NTItOS4yMTUgICAgQzQ3Ni44NjMsMzM1LjU0Niw0NzIuNjU2LDMyOS4wOCw0NzQuMDQsMzIyLjU1OXogTTI4LjI3LDMwOS42NDZsMTAzLjExNS0yOC42MDZsMjQzLjI5OS02Ny41MTdsMjYuMTgxLTcuMjc0ICAgIGMwLjQ3OC0wLjEyOSwwLjk1NS0wLjE5LDEuNDIxLTAuMTljMi4xLDAsNC42MTEsMS4zNzgsNS4zNDUsNC4wMTdsMy4wNzQsMTEuMDdsOS42MzEsMzQuNzA0TDM3LjE0OCwzNjIuMTg2bC0xMi43MDUtNDUuNzY4ICAgIEMyMy42NDcsMzEzLjU0NiwyNS4zOTksMzEwLjQ0MiwyOC4yNywzMDkuNjQ2eiBNNDcyLjYwMSw0NDQuMTQxYzAuNDksMS43NzYtMC4wMjQsMy4yNDUtMC41NDUsNC4xNjQgICAgYy0wLjUxNCwwLjkxOC0xLjUwNiwyLjExOS0zLjI4MiwyLjYwOEw5Ni4xNzMsNTU0LjMxNmMtMC40NzEsMC4xMjktMC45NTUsMC4xOTYtMS40MjEsMC4xOTZjLTIuMSwwLTQuNjExLTEuMzg0LTUuMzQ1LTQuMDIzICAgIEw1MS41MTksNDEzLjk1NWwzODMuMTg4LTEwNi4zNDJsMjMuMzcxLDg0LjIwOEw0NzIuNjAxLDQ0NC4xNDF6IiBmaWxsPSIjRkZGRkZGIi8+PHBhdGggZD0iTTE1Ni4zNzksNDUzLjQ4NGMtMS43ODgtNi40MjktOC40OTktMTAuMjI1LTE0LjkyOC04LjQ0M2wtNDMuNTE1LDEyLjA4Yy02LjQyMywxLjc4Mi0xMC4yMjUsOC40OTktOC40MzcsMTQuOTI4ICAgIGwxMi4wNzQsNDMuNTA5YzEuNzg4LDYuNDI5LDguNDk5LDEwLjIyNSwxNC45MjgsOC40MzdsNDMuNTE1LTEyLjA3NGM2LjQyOS0xLjc4MiwxMC4yMjUtOC40OTksOC40NDMtMTQuOTI4TDE1Ni4zNzksNDUzLjQ4NHoiIGZpbGw9IiNGRkZGRkYiLz48L2c+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjwvc3ZnPg==',
                'public'               =>   false,
                'publicly_queriable'   =>   true,
                'show_ui'              =>   true,
                'show_in_nav_menus'    =>   false,
                'has_archive'          =>   false,
                'rewrite'              =>   array(
                    'slug'       =>   __('order', 'modularity-resource-booking'),
                    'with_front' =>   false
                ),
                'hierarchical'          =>  false,
                'exclude_from_search'   =>  true,
                'taxonomies'            =>  array(),
                'supports'              =>  array('title', 'revisions')
            )
        );

        /* Indicate what packages this product belongs to */
        /*$postType->addTableColumn(
            'package',
            __('Package', 'modularity-resource-booking'),
            true,
            function ($column, $postId) {
                $i = 0;
                $types = get_the_terms($postId, self::$packageTaxonomySlug);

                if (empty($types)) {
                    _e("Undefined", 'modularity-resource-booking');
                } else {
                    foreach ((array)$types as $typeKey => $type) {

                        echo $type->name;

                        if ($typeKey+1 !== count($types)) {
                            echo ", ";
                        }

                    }
                }
            }
        );*/

        /* Price indication */
        $postType->addTableColumn(
            'price',
            __('Price', 'modularity-resource-booking'),
            true,
            function ($column, $postId) {
                //echo get_field('product_price', $postId, true);
            }
        );

        /* Return the slug */
        return $postType->slug;
    }

    /**
     * Create package taxonomy
     *
     * @return string
     */
    public function taxonomyPackage() : string
    {

        //Register product packages
        $packages = new \ModularityResourceBooking\Entity\Taxonomy(
            __('Packages', 'modularity-resource-booking'),
            __('Package', 'modularity-resource-booking'),
            'product-package',
            array(self::$postTypeSlug),
            array(
                'hierarchical' => false
            )
        );

        //Add filter
        new \ModularityResourceBooking\Entity\Filter(
            'product-package',
            self::$postTypeSlug
        );

        //Return taxonomy slug
        return $packages->slug;
    }

}
