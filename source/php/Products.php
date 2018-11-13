<?php

namespace ModularityResourceBooking;

class Products extends \ModularityResourceBooking\Entity\PostType
{
    public static $postTypeSlug;
    public static $categoryTaxonomySlug;
    public static $typeTaxonomySlug;
    public static $priorityTaxonomySlug;
    public static $statusTaxonomySlug;
    public static $sprintTaxonomySlug;

    public function __construct()
    {

        //Main post type
        self::$postTypeSlug = $this->postType();

        //Taxonomy
        /*self::$categoryTaxonomySlug = $this->taxonomyCategory();
        self::$typeTaxonomySlug = $this->taxonomyType();
        self::$priorityTaxonomySlug = $this->taxonomyPriority();
        self::$statusTaxonomySlug = $this->taxonomyStatus();
        self::$sprintTaxonomySlug = $this->taxonomySprint();*/

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
            'ticket',
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

        //Customer in list
        /*$postType->addTableColumn(
            'contact',
            __('Support contact', 'todo'),
            true,
            function ($column, $postId) {
                $customer = get_field('ticket_support_contact', $postId, true);
                echo !empty($customer) ? $customer['user_firstname'] . " " . $customer['user_lastname'] : __('No contact', 'todo');
            }
        );*/

        return $postType->slug;
    }

}
