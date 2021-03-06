<?php

namespace ModularityResourceBooking\Api;

class TimeSlots
{
    public static $userId;

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
        //Get user id
        self::$userId = get_current_user_id();

        register_rest_route(
            "ModularityResourceBooking/v1",
            "Slots",
            array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => array($this, 'getSlots'),
                'args' => $this->getCollectionParams(),
                'permission_callback' => array($this, 'checkOrderOwnership'),
            )
        );
    }

    /**
     * Check that the current user is the same as requested user
     *
     * @param object $request Request data
     *
     * @return bool
     */
    public function checkOrderOwnership($request): bool
    {
        //Bypass security, by constant
        if (RESOURCE_BOOKING_DISABLE_SECURITY) {
            return true;
        }

        //Make sure we are logged in
        if ((int) self::$userId > 0) {
            $userData = get_userdata(self::$userId);
            $userRoles = $userData->roles;
            
            //Make sure we are admin or current user matches param
            if (in_array('administrator', $userRoles) || self::$userId === $request->get_param('user_id')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the query params for collections
     *
     * @return array
     */
    public function getCollectionParams()
    {
        return array(
            'article_type' => array(
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

        $slotType = get_field('mod_res_book_automatic_or_manual', 'option');

        // Make sure slot type is configured
        if (empty($slotType)) {
            return new \WP_REST_Response(
                array(
                    'message' => __('Cannot generate time slots, go to settings and select "Weekly" or "Manual" time slots.', 'modularity-resource-booking'),
                    'state' => 'error'
                ),
                404
            );
        }

        // Generate slots and append stock
        $slots = array_map(function ($slot) use ($params) {
            // Get stock
            $stock = self::getArticleSlotStock($params['article_id'], $params['article_type'], $slot['id'], $params['user_id']);
            unset($stock['id']);

            return array_merge($slot, $stock);
        }, self::generateSlots($slotType));

        if (!empty($slots)) {
            return new \WP_REST_Response($slots, 200);
        } else {
            return new \WP_REST_Response(
                array(
                    'message' => __('No result found.', 'modularity-resource-booking'),
                    'state' => 'error'
                ),
                404
            );
        }
    }
    
    /**
     * Get customer group limit
     * @param $userId
     * @return null|int
     */
    public static function customerGroupLimit($articleId, $articleType, $userId = 0)
    {
        // Get user ID if missing
        $userId = $userId ? $userId : get_current_user_id();
        $customerGroup = wp_get_object_terms($userId, 'customer_group', array('fields' => 'ids'));
        $groupLimit = null;
        
        //  Bail if no customer group
        if (!isset($customerGroup[0]) || empty($customerGroup[0])) {
            return $groupLimit;
        }

        // Get limit from Package
        if ($articleType === 'package') {
            $packageGroupLimits = get_field('customer_group_stock_limit', 'term_' . $articleId);
            if (!empty($packageGroupLimits) && is_array($packageGroupLimits)) {
                foreach ($packageGroupLimits as $packageGroupLimit) {
                    if ($packageGroupLimit['customer_group'] === $customerGroup[0]) {
                        $groupLimit = $packageGroupLimit['stock_limit'];
                        break;
                    }
                }
            }

            //  Get limit on product-level
            if ($groupLimit === null) {
                $lowestLimitOnProductLevel = null;
                $products = get_posts(
                    array(
                        'post_type' => 'product',
                        'numberposts' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'product-package',
                                'field' => 'id',
                                'terms' => $articleId
                            )
                        )
                    )
                );

                if (!empty($products) && is_array($products)) {
                    foreach ($products as $product) {
                        $limit = self::customerGroupLimit($product->ID, 'product', $userId);
                        if (is_numeric($limit) && $limit > 0) {
                            $lowestLimitOnProductLevel = ($lowestLimitOnProductLevel === null) ? $limit
                            : ($lowestLimitOnProductLevel > $limit) ? $limit
                            : $lowestLimitOnProductLevel;
                        }
                    }
                }

                $groupLimit = $lowestLimitOnProductLevel !== null ? $lowestLimitOnProductLevel : $groupLimit;
            }
        }

        // Get limit from Product
        if ($articleType === 'product') {
            $productGroupLimits = get_field('customer_group_stock_limit', $articleId);
            if (!empty($productGroupLimits) && is_array($productGroupLimits)) {
                foreach ($productGroupLimits as $productGroupLimit) {
                    if ($productGroupLimit['customer_group'] === $customerGroup[0]) {
                        $groupLimit = $productGroupLimit['stock_limit'];
                        break;
                    }
                }
            }
        }
        

        return is_numeric($groupLimit) ? (int) $groupLimit : $groupLimit;
    }

    /**
     * List of users within same customer group
     * @param int $userId
     * @return array
     */
    public static function customerGroupMembers($userId = 0)
    {
        // Get user ID if missing
        $userId = $userId ? $userId : get_current_user_id();
        $customerGroup = wp_get_object_terms($userId, 'customer_group', array('fields' => 'ids'));
        $groupMembers = array($userId);
        if (isset($customerGroup[0]) && !empty($customerGroup[0])) {
            $groupMembers = \ModularityResourceBooking\Entity\Filter::getUserByTaxonomy('customer_group', $customerGroup[0]);
            $groupMembers = array_column($groupMembers, 'ID');
        }

        return $groupMembers;
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
    public static function getArticleSlotStock($articleId, $articleType, $slotId, $userId)
    {
        $groupLimit = self::customerGroupLimit($articleId, $articleType, $userId);
        $groupMembers = self::customerGroupMembers($userId);
        $products = self::getProductsByArticle($articleId, $articleType);
        $products = array_map(function ($product) use ($articleType, $slotId, $groupMembers, $groupLimit) {
            // List of packages where the product is included
            $packages = wp_get_post_terms($product->ID, 'product-package', array('fields' => 'ids'));
            $packages = is_array($packages) && !empty($packages) ? $packages : array();
            
            // Product stock
            $stock = get_field('items_in_stock', $product->ID);

            // Check if product if unlimited
            $unlimited = $stock === '' ? true : false;

            // Calculate every time the product have been purchased within the slot period
            $articleIds = array_merge(array($product->ID), $packages);

            // Exclude canceled orders from query
            $orders = \ModularityResourceBooking\Api\Orders::getOrders(
                array(
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'order-status',
                            'terms' => array('canceled'),
                            'field' => 'slug',
                            'operator' => 'NOT IN',
                        )
                    )
                ),
                $articleType,
                $articleIds,
                $slotId
            );

            // Get number of times the customer(or other group members) have purchased this product
            $purchaseCount = 0;
            foreach ($orders as $order) {
                if (in_array($order->post_author, $groupMembers)) {
                    $purchaseCount++;
                }
            }

            // Calculate available stock
            $availableStock = (int)$stock - count($orders);

            // Calculate stock if limit is set
            if ($groupLimit !== null && $groupLimit != 0) {
                $groupStock = $groupLimit - $purchaseCount;
                $availableStock = (!$unlimited && $availableStock < $groupStock) ? $availableStock : $groupStock;
            } elseif ($groupLimit === null && $unlimited) {
                $availableStock = null; // Set to null if no limit is set and stock is unlimited
            }

            // Product with complete stock data
            $product = array(
                'id' => $product->ID,
                'unlimited_stock' => $unlimited,
                'total_stock' => $unlimited ? null : (int) $stock,
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
     *
     * @param $termId
     *
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
     * Wrapper method for generating slots
     * @param $type string Can be either "weekly" (default) or "manual"
     * @return array
     */
    public static function generateSlots(string $type = 'weekly')
    {
        $slotTypes = array('weekly', 'manual');

        if (!in_array($type, $slotTypes)) {
            return false;
        }

        $slots = array();

        switch ($type) {
            case 'weekly':
                return self::generateWeeklySlots();

            case 'manual':
                return self::generateManualSlots();
        }

        return $slots;
    }

    /**
     * Generates weekly slots
     * @param $totalWeeks int Defines how many weeks in the future we want to generate, defaults to 1 year (52 weeks).
     * @return array
     */
    public static function generateWeeklySlots(int $totalWeeks = 52)
    {
        $slots = array();

        //Decide what monday to refer to
        if (date("N") == 1) {
            $whatMonday = "monday";
        } else {
            $whatMonday = "last monday";
        }

        //Get offset
        if ($offset = get_field('mod_res_offset_bookable_weeks_by', 'option')) {
            $weekStart = (int) $offset;
            $weekStop  = $totalWeeks + (int) $offset;
        } else {
            $weekStart = 0;
            $weekStop  = $totalWeeks;
        }

        for ($n = $weekStart; $n <= $weekStop; $n++) {
            $start  = date('Y-m-d', strtotime($whatMonday, strtotime('+' . $n . ' week'))) . " 00:00";
            $stop   = date('Y-m-d', strtotime('sunday', strtotime('+' . $n . ' week'))) . " 23:59";
            $slotId = self::getSlotId($start, $stop);

            //Append slot
            $slots[] = array(
                'id' => $slotId,
                'start' => $start,
                'stop' => $stop
            );
        }

        return $slots;
    }

    /**
     * Generates manual slots
     * @return array
     */
    public static function generateManualSlots()
    {
        $slots = array();
        $data = get_field('mod_res_book_time_slots', 'option');

        if (is_array($data) && !empty($data)) {
            foreach ($data as $item) {
                $start  = $item['start_date'] . " 00:00";
                $stop   = $item['end_date'] . " 23:59";
                $slotId = self::getSlotId($item['start_date'] . " 00:00", $stop);

                //Append slot
                $slots[] = array(
                    'id' => $slotId,
                    'start' => $start,
                    'stop' => $stop
                );
            }
        }

        return $slots;
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
