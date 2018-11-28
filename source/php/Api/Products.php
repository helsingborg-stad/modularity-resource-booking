<?php

namespace ModularityResourceBooking\Api;

class Products
{
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
                        }
                    ),
                ),
            )
        );

        //Get all products (limited)
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
                        }
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
     * Get all orders for n amout of time
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
                        'posts_per_page' => 99,
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
    public function getPackages($request)
    {
        return new \WP_REST_Response(
            $this->filterTaxonomyOutput(
                get_terms(
                    array(
                        'taxonomy' => 'post_tag',
                        'hide_empty' => false,
                    )
                )
            ), 200
        );
    }


    /**
     * Clean return array from uneccesary data (make it slimmer)
     *
     * @param array $orders Array (or object) reflecting items to output.
     *
     * @return array $result Resulting array object
     */
    public function filterPostOutput($postdata, $result = array())
    {

        //Wrap single item in array
        if (is_object($postdata) && !is_array($postdata)) {
            $postdata = array($postdata);
        }

        if (is_array($postdata) && !empty($postdata)) {
            foreach ($postdata as $postitem) {
                $result[] = array(
                    'id' => (int) $postitem->ID,
                    'title' => (string) $postitem->post_title,
                    'description' => (string) $postitem->post_content,
                    'price' => (int) get_field('product_price', $postitem->ID),
                    'location' => get_field('product_location', $postitem->ID),
                    'packages' => wp_get_post_terms(
                        $postitem->ID,
                        'product-package',
                        array(
                            'fields' => 'ids'
                        )
                    )
                );
            }
        }

        return $result;
    }

    /**
     * Clean return array from uneccesary data (make it slimmer)
     *
     * @param array $taxonomy Array (or object) reflecting items to output.
     *
     * @return array $result Resulting array object
     */
    public function filterTaxonomyOutput($taxonomy, $result = array())
    {
        return $taxonomy;

        return $result;
    }

}
