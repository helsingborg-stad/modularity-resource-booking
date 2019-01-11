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
                    'price' => (int) Helper\Product::price($postitem),
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
                    'price' => (int) Helper\Product::price($term),
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
    /**
     * Get the price of a product (post) or package (taxonomy)
     *
     * @param array $item Object representing a taxonomy or
     *                    post that should be parsed for price data
     *
     * @return int $result Integer representing the product price
     */
    public function getPrice($item)
    {

        //Get term or post keys
        if (get_class($item) == "WP_Term") {
            $fieldName = "package_price";
        } else {
            $fieldName = "product_price";
        }

        //Get this price
        $basePrice = get_field($fieldName, $item);

        //Get user group
        $userGroup = get_field('customer_group', 'user_' . self::$userId);

        //Get user groups
        $userGroupPrices = get_field('customer_group_price_variations', $item);

        //Get this user group price
        if (is_array($userGroupPrices) && !empty($userGroupPrices)) {
            foreach ($userGroupPrices as $userGroupPrice) {
                if ($userGroupPrice['customer_group'] == $userGroup) {
                    return $userGroupPrice['product_price'];
                }
            }
        }

        //Could not find specified price, sum all products
        if (get_class($item) == "WP_Term" && empty($basePrice)) {

            $posts = get_posts(
                array(
                    'post_type' => 'product',
                    'numberposts' => -1,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product-package',
                            'field' => 'id',
                            'terms' => $item->term_id
                        )
                    )
                )
            );

            if (is_array($posts) && !empty($posts)) {
                $productSumPrice = null;
                foreach ($posts as $subitem) {
                    $productSumPrice = $productSumPrice + $this->getPrice($subitem);
                }

                if (!empty($productSumPrice)) {
                    return $productSumPrice;
                }
            }
        }

        //No price found, return base price
        return $basePrice;
    }

    /**
     * Returns a merged array of media requirements from all products within a package
     * @param  [int/string] $termId Package (term) ID
     * @return [array/boolean]
     */
    public function getPackageMediaRequirements($termId)
    {
        $term = get_term($termId, 'product-package');

        if (!$term) {
            return false;
        }

        $products = get_posts(
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
        );

        $mediaRequirements = [];

        if (empty($products)) {
            return [];
        }

        foreach ($products as $product) {
            if (empty(get_field('media_requirement', $product->ID))) {
                continue;
            }

            foreach (get_field('media_requirement', $product->ID) as $media) {
                $mediaName = $media['media_name'];
                unset($media['media_name']);
                $mediaKey = md5(json_encode($media));

                if (empty($mediaRequirements) || !isset($mediaRequirements[$mediaKey])) {
                    $mediaRequirements[$mediaKey] = array_merge(array('media_name' => [$mediaName]), $media);
                    continue;
                }

                if (!in_array($mediaName, $mediaRequirements[$mediaKey]['media_name'])) {
                    array_push($mediaRequirements[$mediaKey]['media_name'], $mediaName);
                }
            }
        }

        if (empty($mediaRequirements)) {
            return [];
        }

        return array_values(array_map(function($media) {
            if (isset($media['media_name']) && is_array($media['media_name'])) {
                $media['media_name'] = implode(', ', $media['media_name']);
            }
            return $media;
        }, $mediaRequirements));
    }
}
