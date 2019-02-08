<?php

namespace ModularityResourceBooking;

class Products extends \ModularityResourceBooking\Entity\PostType
{
    public static $postTypeSlug;
    public static $packageTaxonomySlug;

    public function __construct()
    {

        //Main post type
        self::$postTypeSlug = $this->postType();

        //Taxonomy
        self::$packageTaxonomySlug = $this->taxonomyPackage();

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
            _x('Products', 'Post type plural', 'modularity-resource-booking'),
            _x('Product', 'Post type singular', 'modularity-resource-booking'),
            'product',
            array(
                'description'          =>   __('Product registry.', 'modularity-resource-booking'),
                'menu_icon'            =>   'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDQ4Ny4xIDQ4Ny4xIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA0ODcuMSA0ODcuMTsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MTJweCIgaGVpZ2h0PSI1MTJweCI+PGc+PGc+PHBhdGggZD0iTTM0Mi4zLDEzNy45NzhIMzg1bC02My4zLTEwOC42Yy01LjEtOC44LTE2LjQtMTEuOC0yNS4yLTYuNmMtOC44LDUuMS0xMS44LDE2LjQtNi42LDI1LjJMMzQyLjMsMTM3Ljk3OHoiIGZpbGw9IiNGRkZGRkYiLz48cGF0aCBkPSJNMTk3LjQsNDcuOTc4YzUuMS04LjgsMi4yLTIwLjEtNi42LTI1LjJzLTIwLjEtMi4yLTI1LjIsNi42bC02My4zLDEwOC43SDE0NUwxOTcuNCw0Ny45Nzh6IiBmaWxsPSIjRkZGRkZGIi8+PHBhdGggZD0iTTQ1NS43LDE3MS4yNzhIMzEuM2MtMTcuMywwLTMxLjMsMTQtMzEuMywzMS4zdjM0LjdjMCwxNy4zLDE0LDMxLjMsMzEuMywzMS4zaDkuOGwzMC4yLDE2My43ICAgIGMzLjgsMTkuMywyMS40LDM0LjYsMzkuNywzNC42aDEyaDc4LjhjOCwwLDE4LjQsMCwyOSwwbDAsMGg5LjZoOS42bDAsMGMxMC42LDAsMjEsMCwyOSwwaDc4LjhoMTJjMTguMywwLDM1LjktMTUuMywzOS43LTM0LjYgICAgbDMwLjQtMTYzLjhoMTUuOWMxNy4zLDAsMzEuMy0xNCwzMS4zLTMxLjN2LTM0LjdDNDg3LDE4NS4yNzgsNDczLDE3MS4yNzgsNDU1LjcsMTcxLjI3OHogTTE3Mi44LDMzNC44Nzh2NzAuNiAgICBjMCwxMC4xLTguMiwxNy43LTE3LjcsMTcuN2MtMTAuMSwwLTE3LjctOC4yLTE3LjctMTcuN3YtMjkuNnYtNjkuNGMwLTEwLjEsOC4yLTE3LjcsMTcuNy0xNy43YzEwLjEsMCwxNy43LDguMiwxNy43LDE3LjdWMzM0Ljg3OCAgICB6IE0yMjkuNiwzMzQuODc4djcwLjZjMCwxMC4xLTguMiwxNy43LTE3LjcsMTcuN2MtMTAuMSwwLTE3LjctOC4yLTE3LjctMTcuN3YtMjkuNnYtNjkuNGMwLTEwLjEsOC4yLTE3LjcsMTcuNy0xNy43ICAgIHMxNy43LDguMiwxNy43LDE3LjdWMzM0Ljg3OHogTTI4Ni43LDM3NS44Nzh2MjkuNmMwLDkuNS03LjYsMTcuNy0xNy43LDE3LjdjLTkuNSwwLTE3LjctNy42LTE3LjctMTcuN3YtNzAuNnYtMjguNCAgICBjMC05LjUsOC4yLTE3LjcsMTcuNy0xNy43czE3LjcsNy42LDE3LjcsMTcuN1YzNzUuODc4eiBNMzQzLjUsMzc1Ljg3OHYyOS42YzAsOS41LTcuNiwxNy43LTE3LjcsMTcuN2MtOS41LDAtMTcuNy03LjYtMTcuNy0xNy43ICAgIHYtNzAuNnYtMjguNGMwLTkuNSw3LjYtMTcuNywxNy43LTE3LjdjOS41LDAsMTcuNyw3LjYsMTcuNywxNy43VjM3NS44Nzh6IiBmaWxsPSIjRkZGRkZGIi8+PC9nPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48L3N2Zz4=',
                'public'               =>   false,
                'publicly_queriable'   =>   true,
                'show_ui'              =>   true,
                'show_in_nav_menus'    =>   false,
                'has_archive'          =>   false,
                'rewrite'              =>   array(
                    'slug'       =>   __('product', 'modularity-resource-booking'),
                    'with_front' =>   false
                ),
                'hierarchical'          =>  false,
                'exclude_from_search'   =>  true,
                'taxonomies'            =>  array(),
                'supports'              =>  array('title', 'revisions', 'editor')
            )
        );

        /* Indicate what packages this product belongs to */
        $postType->addTableColumn(
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
        );

        /* Price indication */
        $postType->addTableColumn(
            'price',
            __('Price', 'modularity-resource-booking'),
            true,
            function ($column, $postId) {
                echo get_field('product_price', $postId, true);
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
