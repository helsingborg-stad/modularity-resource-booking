<?php

namespace ModularityResourceBooking\Api;

/**
 * Class Orders
 *
 * @package ModularityResourceBooking\Api
 */
class Orders
{

    /**
     * Class variables
     *
     * @var The current user id
     */
    public static $userId;

    /**
     * Orders constructor.
     */
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

        //Get my orders (for a limited period of time)
        register_rest_route(
            "ModularityResourceBooking/v1",
            "MyOrders",
            array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => array($this, 'listMyOrders'),
                'permission_callback' => array($this, 'CheckUserAuthentication'),
                'args' => $this->getCollectionParams(),
            )
        );

        //Create a new order
        register_rest_route(
            "ModularityResourceBooking/v1",
            "CreateOrder",
            array(
                'methods' => \WP_REST_Server::CREATABLE,
                'callback' => array($this, 'create'),
                'permission_callback' => array($this, 'checkInsertCapability')
            )
        );

        // Cancel order
        register_rest_route(
            "ModularityResourceBooking/v1",
            "CancelOrder/(?P<id>[\d]+)",
            array(
                'methods' => \WP_REST_Server::EDITABLE,
                'callback' => array($this, 'cancelOrder'),
                'permission_callback' => array($this, 'checkOrderOwnership'),
                'args' => array(
                    'id' => array(
                        'validate_callback' => function ($param, $request, $key) {
                            return is_numeric($param);
                        },
                        'sanitize_callback' => 'absint',
                        'required' => true,
                        'type' => 'integer',
                        'description' => 'The order id.'
                    ),
                ),
            )
        );

        //Upload files to order
        register_rest_route(
            "ModularityResourceBooking/v1",
            "UploadFiles",
            array(
                'methods' => \WP_REST_Server::CREATABLE,
                'callback' => array($this, 'upload'),
                'permission_callback' => array($this, 'checkInsertCapability')
            )
        );
    }

    public function cancelOrder($request)
    {
        $id = $request->get_param('id');
        $updateStatus = wp_set_post_terms($id, 'canceled', 'order-status', false);

        if (is_wp_error($updateStatus)) {
            return new \WP_REST_Response(
                array(
                    'message' => __('The order could not be canceled.', 'modularity_resource_booking'),
                ),
                400
            );
        }

        $orderId = get_the_title($id);

        if (!empty(get_field('actions_customer_cancel_order', 'options')) && is_array(get_field('actions_customer_cancel_order', 'options'))) {
            foreach (get_field('actions_customer_cancel_order', 'options') as $mailTemplate) {
                $mailService = new \ModularityResourceBooking\Mail\Service($mailTemplate);
                $mailService->setOrder($id);
                $mailService->setUser(self::$userId);
                $mailService->composeMail();
                $mailService->sendMail();

                $errors = $mailService->getErrors();
                if (!empty($errors->get_error_messages())) {
                    error_log(print_r($errors, true));
                }
            }
        }

        // Return success
        return new \WP_REST_Response(
            array(
                'message' => __('The order has been canceled.', 'modularity_resource_booking'),
            ),
            200
        );
    }

    /**
     * Get the query params for collections
     * @return array
     */
    public function getCollectionParams()
    {
        return array(
            'page' => array(
                'description' => 'Current page of the collection.',
                'type' => 'integer',
                'default' => 1,
                'sanitize_callback' => 'absint',
            )
        );
    }

    /**
     * Get all orders for n amount of time
     *
     * @param object $request   Object containing request details
     * @param array  $args      Containing query information
     * @param array  $metaQuery Containing meta query information
     *
     * @return \WP_REST_Response
     */
    public function listOrders($request, $args = array(), $metaQuery = null)
    {
        //Basic query
        $query = array(
            'post_type' => 'purchase',
            'posts_per_page' => 99,
            'orderby' => 'date',
            'order' => 'DESC'
        );

        $query = array_merge($query, $args);

        //Append meta query
        if (!is_null($metaQuery) && is_array($metaQuery)) {
            $query['meta_query'] = $metaQuery;
        }

        $orders = get_posts($query);

        foreach ($orders as $key => &$order) {
            $name = $order->post_title;
            $orderData = get_field('order_data', $order->ID);
            if (is_array($orderData) && !empty($orderData)) {
                $orderId = $order->ID;
                $order = array_shift($orderData);
                $terms = wp_get_post_terms($order['id'], 'order-status', array('fields' => 'ids'));
                $status = isset($terms[0]) ? get_term($terms[0], 'order-status') : null;
                $cancelable = get_field('can_be_canceled', $status) ? true : false;
                $order['status'] = $status->name ?? '';
                $order['cancelable'] = $cancelable;
                $order['name'] = $name;
                $order['permalink'] = get_permalink($orderId);
            } else {
                unset($orders[$key]);
            }
        }

        return new \WP_REST_Response(
            $orders,
            200
        );
    }

    /**
     * Get all orders for n amount of time that i own / are the customer on
     *
     * @param object $request Object containing request details
     *
     * @return \WP_REST_Response|array
     */
    public function listMyOrders($request)
    {
        $parameters = $request->get_params();
        return $this->listOrders(
            $request,
            array(
                'paged' => $parameters['page'],
                'tax_query' => array(
                    array(
                        'taxonomy' => 'order-status',
                        'terms' => array('canceled'),
                        'field' => 'slug',
                        'operator' => 'NOT IN',
                    ),
                )
            ),
            array(
                array(
                    'key' => 'customer_id',
                    'value' => self::$userId,
                )
            )
        );
    }

    /**
     * Create a new order
     * @param object $request Object containing request details
     * @return \WP_REST_Response|bool
     */
    public function create($request)
    {
        $validatePostDataResponse = $this->validatePostData(array('order_articles'));
        if ($validatePostDataResponse instanceof \WP_REST_Response) {
            return $validatePostDataResponse;
        }

        $data = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        // Get customer group data
        $groupLimit = TimeSlots::customerGroupLimit(self::$userId);
        $groupMembers = TimeSlots::customerGroupMembers(self::$userId);

        // Remap order items and check stock availability
        $orderArticles = $data['order_articles'];

        if (!is_array($orderArticles) || empty($orderArticles)) {
            return new \WP_REST_Response(
                array(
                    'message' => __('You have to specify a article.', 'modularity-resource-booking'),
                    'state' => 'error'
                ),
                403
            );
        }

        foreach ($orderArticles as $key => &$item) {
            $itemData = (array)json_decode(stripslashes(html_entity_decode($item)));

            // Get list of product objects
            $products = TimeSlots::getProductsByArticle($itemData['article_id'], $itemData['type']);
            if (empty($products)) {
                return new \WP_REST_Response(
                    array(
                        'message' => __('No articles could be found with \'article_id\': ' . $itemData['article_id'], 'modularity-resource-booking'),
                        'state' => 'error'
                    ),
                    400
                );
            }
            $articleStock = TimeSlots::getArticleSlotStock($products, $itemData['type'], $itemData['slot_id'], $groupMembers, $groupLimit);
            if ($articleStock['available_stock'] !== null && $articleStock['available_stock'] <= 0) {
                return new \WP_REST_Response(
                    array(
                        'message' => __('Out of stock for \'article_id\': ' . $itemData['article_id'], 'modularity-resource-booking'),
                        'state' => 'error'
                    ),
                    403
                );
            }

            $item = array(
                'field_5c122674bc676' => $itemData['type'] ?? null,
                'field_5bed43f2bf1f2' => $itemData['article_id'] ?? null,
                'field_5c0fc17caefa5' => $itemData['slot_id'] ?? null,
            );
        }

        $orderId = strtoupper(substr(md5(microtime()), rand(0, 26), 8));

        //Define new post
        $postItem = array(
            'post_title' => isset($data['order_title']) && is_string($data['order_title']) && !empty($data['order_title']) ? $data['order_title'] : $orderId,
            'post_type' => 'purchase',
            'post_status' => 'publish',
            'post_author' => self::$userId,
            'post_name' => $orderId
        );

        // Prepend id if provided (converted to update)
        if (is_numeric($request->get_param('id')) && get_post_type($request->get_param('id')) == "purchase") {
            $postItem = array('ID' => $request->get_param('id')) + $postItem;
        }

        // Upload media files
        $mediaItems = array();
        if (is_array($_FILES) && !empty($_FILES) && !isset($data['skip_files'])
            || is_array($_FILES) && !empty($_FILES) && $data['skip_files'] !== '1') {
            $mediaItems = $this->uploadFiles($_FILES, $orderArticles[0]['field_5bed43f2bf1f2'], $orderArticles[0]['field_5c122674bc676']);

            if ($mediaItems instanceof \WP_REST_Response) {
                return $mediaItems;
            }
        }
        
        //Make insert
        $insert = wp_insert_post($postItem);

        //Handles insert failure
        if (is_wp_error($insert) || $insert === 0) {
            return new \WP_REST_Response(
                array(
                    'message' => __('Bummer, something went wrong.', 'modularity-resource-booking'),
                    'state' => 'error'
                ),
                400
            );
        }

        // Save order items to repeater field
        update_field('field_5c0fc16aaefa4', $orderArticles, $insert);

        //Update meta
        if (isset($data['order_title'])) {
            update_post_meta($insert, 'order_title', $data['order_title']);
        }

        update_post_meta($insert, 'order_id', $orderId);

        //Update fields
        update_field('customer_id', self::$userId, $insert);
        update_field('order_status', get_field('order_status', 'option'), $insert);

        //Append attachment data
        if (is_array($mediaItems) && !empty($mediaItems)) {
            //Add items for storage of each id
            foreach ($mediaItems as $mediaKey => $mediaItem) {
                update_sub_field(array('media_items', $mediaKey + 1, 'file'), $mediaItem, $insert);
            }

            //Add number of items avabile (hotfix!)
            update_post_meta($insert, 'media_items', count($mediaItems));
            update_post_meta($insert, '_media_items', 'field_5bffbfed18455');
        }

        // Save complete order data with current prices etc
        update_post_meta($insert, 'order_data', $this->filterorderOutput(get_post($insert)));

        if (!empty(get_field('actions_new_order', 'options')) && is_array(get_field('actions_new_order', 'options'))) {
            foreach (get_field('actions_new_order', 'options') as $mailTemplate) {
                $mailService = new \ModularityResourceBooking\Mail\Service($mailTemplate);
                $mailService->setOrder($insert);
                $mailService->setUser(self::$userId);
                $mailService->composeMail();
                $mailService->sendMail();

                $errors = $mailService->getErrors();
                if (!empty($errors->get_error_messages())) {
                    error_log(print_r($errors, true));
                }
            }
        }

        //Return success
        return new \WP_REST_Response(
            array(
                'message' => get_field('registered_order', 'options') && !empty(get_field('registered_order', 'options')) ? get_field('registered_order', 'options') :
                __('Your order have been registered.', 'modularity-resource-booking') . ' ' . __('Visit', 'modularity-resource-booking') . ' <a href="' . get_permalink(get_field('order_history_page', 'options')) . '">' . __('Order History', 'modularity-resource-booking') . '</a> ' . __('to view your order', 'modularity-resource-booking') . '.',
                'order' => $this->filterorderOutput(get_post($insert)),
                'state' => 'success'
            ),
            201
        );
    }


    /**
     * Undocumented function
     *
     * @param [type] $request
     * @return void
     */
    public function upload($request)
    {
        //  Make sure we have files to process
        if (!is_array($_FILES) || empty($_FILES)) {
            return new \WP_REST_Response(
                array(
                    'message' => __('$_FILES is empty', 'modularity-resource-booking'),
                    'state' => 'error'
                ),
                404
            );
        }

        // Required post data keys
        $requiredKeys = array(
            'order_id'
        );

        //  Validate postdata
        $validatePostDataResponse = $this->validatePostData($requiredKeys);
        if ($validatePostDataResponse instanceof \WP_REST_Response) {
            return $validatePostDataResponse;
        }

        //  Reference to form Data
        $data = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        //  Make sure order exists
        if (get_post_type($data['order_id']) !== \ModularityResourceBooking\Orders::$postTypeSlug) {
            return new \WP_REST_Response(
                array(
                    'message' => __('Could not find any order with that id.', 'modularity-resource-booking'),
                    'state' => 'error'
                ),
                404
            );
        }

        //  Make sure user has permission to upload to this order
        if (!isset(get_userdata(self::$userId)->caps['administrator'])
            && get_field('customer_id', $data['order_id']) !== self::$userId) {
            return new \WP_REST_Response(
                array(
                    'message' => __('You dont have permission to upload to this order.', 'modularity-resource-booking'),
                    'state' => 'error'
                ),
                404
            );
        }

        $orderData = get_post_meta($data['order_id'], 'order_data', true)[0];
        
        //  Make sure order has articles
        if (empty($orderData['articles'])) {
            return new \WP_REST_Response(
                array(
                    'message' => __('Order has no articles.', 'modularity-resource-booking'),
                    'state' => 'error'
                ),
                404
            );
        }

        $articleId = $orderData['articles'][0]['id'];
        $articleType = $orderData['articles'][0]['type'];

        $mediaItems = $this->uploadFiles($_FILES, $articleId, $articleType);
        if ($mediaItems instanceof \WP_REST_Response) {
            return $mediaItems;
        }

        //Append attachment data
        if (is_array($mediaItems) && !empty($mediaItems)) {
            //Add items for storage of each id
            foreach ($mediaItems as $mediaKey => $mediaItem) {
                update_sub_field(array('media_items', $mediaKey + 1, 'file'), $mediaItem, $data['order_id']);
            }

            //Add number of items avabile (hotfix!)
            update_post_meta($data['order_id'], 'media_items', count($mediaItems));
            update_post_meta($data['order_id'], '_media_items', 'field_5bffbfed18455');
        }

        //Return success
        return new \WP_REST_Response(
            array(
                'message' => get_field('uploaded_items_notice', 'options') && !empty(get_field('uploaded_items_notice', 'options')) ? get_field('uploaded_items_notice', 'options') :
                __('Files has been uploaded to the order.', 'modularity-resource-booking'),
                'state' => 'success'
            ),
            201
        );
    }

    /**
     * Undocumented function
     *
     * @param [type] $requiredKeys
     * @return void
     */
    public function validatePostData($requiredKeys)
    {
        //Verify that post data is avabile
        if (isset($_POST) && !empty($_POST)) {
            if (is_array($requiredKeys) && !empty($requiredKeys)) {
                foreach ($requiredKeys as $requirement) {
                    if (!array_key_exists($requirement, $_POST)) {
                        return new \WP_REST_Response(
                            array(
                                'message' => __('A parameter is missing: ', 'modularity-resource-booking') . $requirement,
                                'state' => 'error'
                            ),
                            400
                        );
                    }
                }
            }
        } else {
            return new \WP_REST_Response(
                array(
                    'message' => __('The post request sent was empty.', 'modularity-resource-booking'),
                    'state' => 'error'
                ),
                400
            );
        }
        
        return true;
    }

    /**
     * Undocumented function
     *
     * @param [type] $files
     * @param [type] $articleId
     * @param [type] $articleType
     * @return void
     */
    public function uploadFiles($files, $articleId, $articleType)
    {
        // Upload media files
        $mediaItems = array();
        if (is_array($files) && !empty($files)) {
            $mediaItems = \ModularityResourceBooking\Helper\MediaUpload::upload($articleId, $articleType, $files);

            if (is_wp_error($mediaItems)) {
                return new \WP_REST_Response(
                    array(
                        'message' => $mediaItems->get_error_message(),
                        'state' => $mediaItems->get_error_code(),
                        'data' => $mediaItems->get_error_data(),
                    ),
                    400
                );
            }
        }

        return $mediaItems;
    }

    /**
     * Check that the current user is the owner of order x
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

        $orderId = $request->get_param('id');

        if (((int)get_post_meta($orderId, 'customer_id', true) === self::$userId) || (int)get_post($orderId)->post_author === self::$userId) {
            return true;
        }

        return false;
    }

    /**
     * Check that the current user can enter a new item
     *
     * @return bool
     */
    public function checkInsertCapability()
    {

        //Bypass security, by constant
        if (RESOURCE_BOOKING_DISABLE_SECURITY) {
            return true;
        }

        if (is_user_logged_in() && (current_user_can('order') || current_user_can('administrator'))) {
            return true;
        }

        return false;
    }

    /**
     * Check if a user is logged in
     *
     * @return bool
     */
    public function checkUserAuthentication()
    {
        //Bypass security, by constant
        if (RESOURCE_BOOKING_DISABLE_SECURITY) {
            return true;
        }

        return is_user_logged_in();
    }

    /**
     * Clean return array from uneccesary data (make it slimmer)
     *
     * @param array $orders Array (or object) reflecting items to output.
     * @param array $result Array contining the output (basically a declaration)
     *
     * @return array $result Resulting array object
     */
    public function filterOrderOutput($orders, $result = array())
    {
        //Wrap single item in array
        if (is_object($orders) && !is_array($orders)) {
            $orders = array($orders);
        }

        if (is_array($orders) && !empty($orders)) {
            foreach ($orders as $order) {
                //Order status
                $terms = wp_get_post_terms($order->ID, 'order-status', array('fields' => 'ids'));
                $orderStatus = isset($terms[0]) ? get_term($terms[0], 'order-status') : null;

                //Get ordered items
                $articles = get_field('order_articles', $order->ID);

                //Create result array
                $result[] = array(
                    'id' => (int)$order->ID,
                    'order_id' => (string)get_post_meta($order->ID, 'order_id', true),
                    'user_id' => (int)$order->post_author,
                    'uname' => (string)\ModularityResourceBooking\Helper\Customer::getName($order->post_author),
                    'name' => (string)$order->post_title,
                    'date' => date('Y-m-d', strtotime($order->post_date)),
                    'slug' => (string)$order->post_name,
                    'status' => $orderStatus->name ?? null,
                    'articles' => is_array($articles) && !empty($articles) ? $this->filterArticlesOutput($articles) : array()
                );
            }
        }

        //Append media data if owner
        if (is_array($result) && !empty($result)) {
            foreach ($result as $key => $item) {
                if ($item['user_id'] == self::$userId) {
                    $result[$key] = $item + array(
                            'media' => (array)get_field('media_items', $item['id'])
                        );
                }
            }
        }

        //Append action links if owner
        if (is_array($result) && !empty($result)) {
            foreach ($result as $key => $item) {
                if ($item['user_id'] == self::$userId) {
                    $result[$key] = $item + array(
                            'actions' => array(
                            )
                        );
                }
            }
        }

        return $result;
    }

    /**
     * Filter the article list output
     * @param array $articles List of articles
     * @return array Filtered articles
     */
    public function filterArticlesOutput($articles)
    {
        $groupId = $this->getCustomerGroup();

        $price = 0;
        foreach ($articles as $key => &$article) {
            $price = 0;
            $slot = TimeSlots::getSlotInterval($article['slot_id']);
            if ($article['type'] === 'package') {
                $price = \ModularityResourceBooking\Helper\Product::getPrice(get_term($article['article_id']), self::$userId);
            } elseif ($article['type'] === 'product') {
                // Get product price
                $price = \ModularityResourceBooking\Helper\Product::getPrice(get_post($article['article_id']), self::$userId);
            }

            $article = array(
                'id' => $article['article_id'],
                'title' => \ModularityResourceBooking\Helper\Product::name($article['article_id']),
                'type' => $article['type'],
                'start' => $slot['start'],
                'stop' => $slot['stop'],
                'price' => $price
            );
        }

        return $articles;
    }

    /**
     * Get the product price
     * @param $productId
     * @param $groupId
     * @return int
     */
    public function getProductPrice($productId, $groupId = 0)
    {
        $price = get_field('product_price', $productId);
        // Check if a user group price variation is set
        $groupVariations = get_field('customer_group_price_variations', $productId);
        if ($groupId && is_array($groupVariations) && !empty($groupVariations)) {
            $key = array_search($groupId, array_column($groupVariations, 'customer_group'));
            if ($key !== false) {
                $price = $groupVariations[$key]['product_price'];
            }
        }

        return (int)$price;
    }

    /**
     * Get customer group
     * @return null|int
     */
    public function getCustomerGroup()
    {
        $customerGroup = wp_get_object_terms(self::$userId, 'customer_group', array('fields' => 'ids'));
        $customerGroup = $customerGroup[0] ?? null;
        return $customerGroup;
    }
}
