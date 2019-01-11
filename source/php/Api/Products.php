<?php

namespace ModularityResourceBooking\Api;

class Products
{
    public static $userId;

    public function __construct()
    {
        //Run register rest routes
        add_action('rest_api_init', array($this, 'registerRestRoutes'));
    }

    /**
     * Registers all rest routes for managing orders
     *
     * @return void
     */
    public function registerRestRoutes()
    {

        //Get user id
        self::$userId = get_current_user_id();

        //Get single product
        register_rest_route(
            "ModularityResourceBooking/v1",
            "Product/(?P<id>[\d]+)",
            array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => array($this, 'getProduct'),
                'args' => array(
                    'id' => array(
                        'validate_callback' => function ($param, $request, $key) {
                            return is_numeric($param);
                        },
                        'sanitize_callback' => 'absint',
                        'required' => true,
                        'type' => 'integer',
                        'description' => 'The product id.'
                    ),
                ),
            )
        );

        //Get all products
        register_rest_route(
            "ModularityResourceBooking/v1",
            "Product",
            array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => array($this, 'getProducts')
            )
        );

        //Get single package
        register_rest_route(
            "ModularityResourceBooking/v1",
            "Package/(?P<id>[\d]+)",
            array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => array($this, 'getPackage'),
                'args' => array(
                    'id' => array(
                        'validate_callback' => function ($param, $request, $key) {
                            return is_numeric($param);
                        },
                        'sanitize_callback' =>'absint',
                        'required' => true,
                        'type' => 'integer',
                        'description' => 'The package id.'
                    ),
                ),
            )
        );

        //Get all packages (limited)
        register_rest_route(
            "ModularityResourceBooking/v1",
            "Package",
            array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => array($this, 'getPackages')
            )
        );

    }

    /**
     * Get a single order
     *
     * @param object $request Object containing request details
     *
     * @return WP_REST_Response
     */
    public function getProduct($request)
    {
        return new \WP_REST_Response(
            array_pop(
                $this->filterPostOutput(
                    get_post($request->get_param('id'))
                )
            ), 200
        );
    }

    /**
     * Get all products
     *
     * @param object $request Object containing request details
     *
     * @return WP_REST_Response
     */
    public function getProducts($request)
    {
        return new \WP_REST_Response(
            $this->filterPostOutput(
                get_posts(
                    array(
                        'post_type' => 'product',
                        'posts_per_page' => -1,
                        'orderby' => 'date',
                        'order' => 'DESC'
                    )
                )
            ), 200
        );
    }

    /**
     * Get all packages
     *
     * @param object $request Object containing request details
     *
     * @return WP_REST_Response
     */
    public function getPackage($request)
    {
        if ($term = get_term($request->get_param('id'), 'product-package')) {
            return new \WP_REST_Response(
                $this->filterTaxonomyOutput($term),
                200
            );
        }

        return new \WP_REST_Response(
            array(
                'message' => __('Could not find any package with that id.', 'modularity-resource-booking'),
                'state' => 'error'
            ), 404
        );
    }

    /**
     * Get all packages
     *
     * @param object $request Object containing request details
     *
     * @return WP_REST_Response
     */
    public function getPackages($request)
    {
        return new \WP_REST_Response(
            $this->filterTaxonomyOutput(
                get_terms(
                    array(
                        'taxonomy' => 'product-package',
                        'hide_empty' => true,
                    )
                )
            ), 200
        );
    }

    /**
     * Clean return array from uneccesary data (make it slimmer)
     *
     * @param array $postData Array (or object) reflecting items to output.
     * @param array $result   Declaration of end result var
     *
     * @return array $result Resulting array object
     */
    public function filterPostOutput($postData, $result = array())
    {

        //Wrap single item in array
        if (is_object($postData) && !is_array($postData)) {
            $postData = array($postData);
        }

        if (is_array($postData) && !empty($postData)) {
            foreach ($postData as $postitem) {
                $result[] = array(
                    'id' => (int) $postitem->ID,
                    'title' => (string) $postitem->post_title,
                    'description' => (string) $postitem->post_content,
                    'price' => (int) \ModularityResourceBooking\Helper\Product::price($postitem),
                    'location' => get_field('product_location', $postitem->ID),
                    'total_stock' => (int) get_field('items_in_stock', $postitem->ID),
                    'packages' => wp_get_post_terms(
                        $postitem->ID,
                        'product-package',
                        array(
                            'fields' => 'ids'
                        )
                    ),
                    'media_requirements' => (array) get_field('media_requirement', $postitem->ID)
                );
            }
        }

        return $result;
    }

    /**
     * Clean return array from uneccesary data (make it slimmer)
     *
     * @param array $taxonomy Array (or object) reflecting items to output.
     * @param array $result   Declaration of end result var
     *
     * @return array $result Resulting array object
     */
    public function filterTaxonomyOutput($taxonomy, $result = array())
    {

        //Wrap single item in array
        if (is_object($taxonomy) && !is_array($taxonomy)) {
            $taxonomy = array($taxonomy);
        }

        //Get formatted object
        if (is_array($taxonomy) && !empty($taxonomy)) {
            foreach ($taxonomy as $term) {
                $result[] = array(
                    'id' => $term->term_id,
                    'title' => $term->name,
                    'description' => $term->description,
                    'price' => (int) \ModularityResourceBooking\Helper\Product::price($term),
                    'media_requirements' => \ModularityResourceBooking\Helper\Product::getPackageMediaRequirements($term->term_id),
                    'products' => $this->filterPostOutput(
                        get_posts(
                            array(
                                'posts_per_page' => -1,
                                'post_type' => 'product',
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'product-package',
                                        'field' => 'term_id',
                                        'terms' => $term->term_id,
                                    )
                                )
                            )
                        )
                    )
                );
            }
        }

        //Calculate price of package if not set
        if (is_array($result) && !empty($result)) {
            foreach ($result as $key => $item) {
                $price = 0;
                if (!$item['price'] && count($item['products'])) {
                    foreach ($item['products'] as $product) {
                        $price = $price + $product['price'];
                    }
                }

                if ($price != 0) {
                    $result[$key]['price'] = $price;
                }
            }
        }

        return $result;
    }
}
