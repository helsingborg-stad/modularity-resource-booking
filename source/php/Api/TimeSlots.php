<?php

namespace ModularityResourceBooking\Api;

class TimeSlots
{
    public function __construct()
    {
        //Run register rest routes
        add_action('rest_api_init', array($this, 'registerRestRoutes'));

        add_filter('posts_where', array($this, 'postsWhereWildcard'), 2, 10);
    }

    /**
     * Registers all rest routes for managing orders
     * @return void
     */
    public function registerRestRoutes()
    {
        register_rest_route(
            "ModularityResourceBooking/v1",
            "Slots",
            array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => array($this, 'getSlots'),
                'args' => $this->getCollectionParams(),
            )
        );
    }

    /**
     * Get the query params for collections
     * @return array
     */
    public function getCollectionParams()
    {
        return array(
            'type' => array(
                'description' => 'The article type.',
                'type' => 'string',
                'default' => 'product',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'user_id' => array(
                'description' => 'User ID.',
                'type' => 'integer',
                'default' => 0,
                'sanitize_callback' => 'absint',
            ),
            'article_id' => array(
                'description' => 'Article ID.',
                'type' => 'integer',
                'default' => 0,
                'sanitize_callback' => 'absint',
            ),
        );
    }

    /**
     * Get slots with stock data
     * @return \WP_REST_Response
     */
    public function getSlots($request)
    {
        // Request params
        $params = $request->get_params();
        $result = array();

        // Get customer group data
        $customerGroup = wp_get_object_terms($params['user_id'], 'customer_group', array('fields' => 'ids'));
        $groupLimit = null;
        $groupMembers = array($params['user_id']);
        if (isset($customerGroup[0]) && !empty($customerGroup[0])) {
            // Get customer group limit
            $groupLimit = get_field('customer_slot_limit', 'customer_group' . '_' . $customerGroup[0]);
            $groupLimit = $groupLimit === '' ? null : (int)$groupLimit;
            // List of users within same customer group
            $groupMembers = \ModularityResourceBooking\Entity\Filter::getUserByTaxonomy('customer_group', $customerGroup[0]);
            $groupMembers = array_column($groupMembers, 'ID');
        }

        // Get list of product objects
        $products = self::getProductsByArticle($params['article_id'], $params['type']);
        if (empty($products)) {
            return new \WP_REST_Response(
                array(
                    'message' => __('No articles could be found with \'article_id\': ' . $params['article_id'], 'modularity-resource-booking'),
                    'state' => 'error'
                ), 404
            );
        }

        if (get_field('mod_res_book_automatic_or_manual', 'option') == "weekly") {
            //Decide what monday to refer to
            if (date("N") == 1) {
                $whatMonday = "monday";
            } else {
                $whatMonday = "last monday";
            }

            for ($n = 0; $n <= 52; $n++) {
                $start = date('Y-m-d', strtotime($whatMonday, strtotime('+' . $n . ' week'))) . " 00:00";
                $stop = date('Y-m-d', strtotime('sunday', strtotime('+' . $n . ' week'))) . " 23:59";
                $slotId = self::getSlotId($start, $stop);

                $articleStock = $this->getArticleSlotStock($products, $params['type'], $slotId, $groupMembers, $groupLimit);
                if (is_wp_error($articleStock)) {
                    return new \WP_REST_Response(
                        array(
                            'message' => $articleStock->get_error_message(),
                            'state' => 'error'
                        ), 404
                    );
                }

                $result[] = array(
                    'id' => $slotId,
                    'start' => $start,
                    'stop' => $stop,
                    'unlimited_stock' => $articleStock['unlimited_stock'],
                    'total_stock' => $articleStock['total_stock'],
                    'available_stock' => $articleStock['available_stock'],
                );
            }
        }

        if (get_field('mod_res_book_automatic_or_manual', 'option') == "manual") {
            $data = get_field('mod_res_book_time_slots', 'option');
            if (is_array($data) && !empty($data)) {
                foreach ($data as $item) {
                    $start = $item['start_date'] . " 00:00";
                    $stop = $item['end_date'] . " 23:59";
                    $slotId = self::getSlotId( $item['start_date'] . " 00:00", $stop);

                    $articleStock = $this->getArticleSlotStock($products, $params['type'], $slotId, $groupMembers, $groupLimit);
                    if (is_wp_error($articleStock)) {
                        return new \WP_REST_Response(
                            array(
                                'message' => $articleStock->get_error_message(),
                                'state' => 'error'
                            ), 404
                        );
                    }

                    $result[] = array(
                        'id' => $slotId,
                        'start' => $start,
                        'stop' => $stop,
                        'unlimited_stock' => $articleStock['unlimited_stock'],
                        'total_stock' => $articleStock['total_stock'],
                        'available_stock' => $articleStock['available_stock'],
                    );
                }
            }
        }

        if (!empty($result)) {
            return new \WP_REST_Response($result, 200);
        } else {
            return new \WP_REST_Response(
                array(
                    'message' => __('No result found.', 'modularity-resource-booking'),
                    'state' => 'error'
                ), 404
            );
        }
    }

    /**
     * Adds wildcards to meta query when searching for slot & package IDs
     * @param $where
     * @param $query
     * @return mixed
     */
    public function postsWhereWildcard($where, $query)
    {
        if (isset($query->query['post_type']) && $query->query['post_type'] === 'purchase') {
            $where = str_replace("meta_key = 'order_articles_\$_type", "meta_key LIKE 'order_articles_%_type", $where);
            $where = str_replace("meta_key = 'order_articles_\$_slot_id", "meta_key LIKE 'order_articles_%_slot_id", $where);
            $where = str_replace("meta_key = 'order_articles_\$_article_id", "meta_key LIKE 'order_articles_%_article_id", $where);
        }

        return $where;
    }

    /**
     * Calculate and returns stock values for slot periods
     * @param $products
     * @param $articleType
     * @param $slotId
     * @param $groupMembers
     * @param $groupLimit
     * @return array
     */
    public function getArticleSlotStock($products, $articleType, $slotId, $groupMembers, $groupLimit)
    {
        $products = array_map(function ($product) use ($articleType, $slotId, $groupMembers, $groupLimit) {
            // List of packages where the product is included
            $packages = wp_get_post_terms($product->ID, 'product-package', array('fields' => 'ids'));
            $packages = is_array($packages) && !empty($packages) ? $packages : array();
            // Product stock
            $stock = get_field('items_in_stock', $product->ID);
            // Check if product if unlimited
            $unlimited = $stock === '' ? true : false;
            $stock = (int)$stock;
            // Calculate every time the product have been purchased within the slot period
            $articleIds = array_merge(array($product->ID), $packages);
            $orders = self::getOrdersByArticles($articleType, $articleIds, $slotId);
            $orderCount = count($orders);
            // Get number of times the customer(or other group members) have purchased this product
            $purchaseCount = 0;
            foreach ($orders as $order) {
                if (in_array($order->post_author, $groupMembers)) {
                    $purchaseCount++;
                }
            }
            // Calculate available stock
            $availableStock = $stock - $orderCount;
            // Calculate stock if limit is set
            if ($groupLimit !== null) {
                $groupStock = $groupLimit - $purchaseCount;
                $availableStock = (!$unlimited && $availableStock < $groupStock) ? $availableStock : $groupStock;
            } elseif ($groupLimit === null && $unlimited) {
                $availableStock = null; // Set to null if no limit is set and stock is unlimited
            }
            // Product with complete stock data
            $product = array(
                'id' => $product->ID,
                'unlimited_stock' => $unlimited,
                'total_stock' => $unlimited ? null : $stock,
                'available_stock' => $availableStock
            );
            return $product;
        }, $products);

        // Remove NULL(Unlimited stock) values, to get list of products with a stock value
        $articlesWithStock = array_diff(array_column($products, 'available_stock'), array(null));
        if (!empty($articlesWithStock)) {
            // Get minimum stock value from all products
            $minimumStock = min($articlesWithStock);
            // Get the product with minimum stock value
            $productsWithMinStock = array_filter($products, function ($product) use ($minimumStock) {
                return ($product['available_stock'] === $minimumStock);
            });
            // Reset array keys
            $productsWithMinStock = array_values($productsWithMinStock);
            // Return first object, in case more than one article have the same available stock left
            return $productsWithMinStock[0];
        }
        // Return first object (should be a product with unlimited stock)
        return $products[0];
    }

    /**
     * Get a list of product objects, handles both single products and packages
     * @param $articleId
     * @param $articleType
     * @return array|bool
     */
    public static function getProductsByArticle($articleId, $articleType)
    {
        $products = array();
        if ($articleType === 'package') {
            $products = self::getProductsByPackage($articleId);
        } elseif ($articleType === 'product') {
            $product = get_post($articleId);
            if (!empty($product)) {
                $products[] = $product;
            }
        }

        return $products;
    }

    /**
     * Get all products in a package
     * @param $termId
     * @return array|bool
     */
    public static function getProductsByPackage($termId)
    {
        //Make sure package (term) exists
        if (!term_exists($termId, 'product-package')) {
            return false;
        }

        $products = get_posts(array(
            'post_type' => 'product',
            'numberposts' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product-package',
                    'field' => 'term_id',
                    'terms' => $termId,
                    'include_children' => false
                )
            )
        ));

        return $products;
    }

    /**
     * Get all orders containing a list of article IDs
     * @param null  $type
     * @param array $articleIds
     * @param null  $slotId
     * @return array
     */
    public static function getOrdersByArticles($type = null, $articleIds = array(), $slotId = null)
    {
        $orders = get_posts(array(
            'post_type' => 'purchase',
            'numberposts' => -1,
            'suppress_filters' => false,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'order_articles_$_type',
                    'value' => $type,
                    'compare' => '='
                ),
                array(
                    'key' => 'order_articles_$_article_id',
                    'value' => $articleIds,
                    'compare' => 'IN'
                ),
                array(
                    'key' => 'order_articles_$_slot_id',
                    'value' => $slotId,
                    'compare' => '='
                ),
            )
        ));

        return $orders;
    }

    /**
     * Transform slot interval to ID
     * @param $start
     * @param $stop
     * @return string
     */
    public static function getSlotId($start, $stop)
    {
        $start = base_convert(strtotime($start), 10, 36);
        $stop = base_convert(strtotime($stop), 10, 36);
        return $start . '.' . $stop;
    }

    /**
     * Transform slot ID to readable interval
     * @param $slotId
     * @return array|bool
     */
    public static function getSlotInterval($slotId)
    {
        $dates = explode('.', $slotId);
        if (!(is_array($dates) && count($dates) === 2)) {
            return false;
        }

        $dates = array(
            'start' => isset($dates[0]) ? date('Y-m-d H:i', base_convert($dates[0], 36, 10)) : '',
            'stop' => isset($dates[1]) ? date('Y-m-d H:i', base_convert($dates[1], 36, 10)) : '',
        );

        return $dates;
    }
}
