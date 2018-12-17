<?php

namespace ModularityResourceBooking\Api;

/**
 * Class Orders
 * @package ModularityResourceBooking\Api
 */
class Orders
{

    /**
     * @var
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

        //Get single order
        register_rest_route(
            "ModularityResourceBooking/v1",
            "Order/(?P<id>[\d]+)",
            array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => array($this, 'getOrder'),
                'args' => array(
                    'id' => array(
                        'validate_callback' => function ($param, $request, $key) {
                            return is_numeric($param);
                        }
                    ),
                ),
            )
        );

        //Get all orders (for a limited period of time)
        register_rest_route(
            "ModularityResourceBooking/v1",
            "Order",
            array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => array($this, 'listOrders')
            )
        );

        //Get my orders (for a limited period of time)
        register_rest_route(
            "ModularityResourceBooking/v1",
            "MyOrders",
            array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => array($this, 'listMyOrders')
            )
        );

        //Create a new order
        register_rest_route(
            "ModularityResourceBooking/v1",
            "CreateOrder",
            array(
                //'methods' => \WP_REST_Server::CREATABLE,
                'methods' => \WP_REST_Server::ALLMETHODS,
                'callback' => array($this, 'create'),
                'permission_callback' => array($this, 'checkInsertCapability')
            )
        );

        //Modify order
        register_rest_route(
            "ModularityResourceBooking/v1",
            "ModifyOrder/(?P<id>[\d]+)",
            array(
                //'methods' => \WP_REST_Server::EDITABLE,
                'methods' => \WP_REST_Server::ALLMETHODS,
                'callback' => array($this, 'modify'),
                'permission_callback' => array($this, 'checkOrderOwnership'),
                'args' => array(
                    'id' => array(
                        'validate_callback' => function ($param, $request, $key) {
                            return is_numeric($param);
                        }
                    ),
                ),
            )
        );

        //Remove order
        register_rest_route(
            "ModularityResourceBooking/v1",
            "RemoveOrder/(?P<id>[\d]+)",
            array(
                //'methods' => \WP_REST_Server::DELETABLE,
                'methods' => \WP_REST_Server::ALLMETHODS,
                'callback' => array($this, 'remove'),
                'permission_callback' => array($this, 'checkOrderOwnership'),
                'args' => array(
                    'id' => array(
                        'validate_callback' => function ($param, $request, $key) {
                            return is_numeric($param);
                        }
                    ),
                ),
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
    public function getOrder($request)
    {
        return new \WP_REST_Response(
            array_pop(
                $this->filterorderOutput(
                    get_post($request->get_param('id'))
                )
            ), 200
        );
    }

    /**
     * Get all orders for n amout of time
     *
     * @param object $request   Object containing request details
     * @param array  $metaQuery Containing meta query information
     *
     * @return WP_REST_Response
     */
    public function listOrders($request, $metaQuery = null)
    {

        //Basic query
        $query = array(
                    'post_type' => 'purchase',
                    'posts_per_page' => 99,
                    'orderby' => 'date',
                    'order' => 'DESC'
                );

        //Append meta query
        if (!is_null($metaQuery) && is_array($metaQuery)) {
            $query['meta_query'] = $metaQuery;
        }

        return new \WP_REST_Response(
            $this->filterorderOutput(
                get_posts($query)
            ), 200
        );
    }

    /**
     * Get all orders for n amout of time that i own / are the customer on
     *
     * @param object $request Object containing request details
     *
     * @return WP_REST_Response
     */
    public function listMyOrders($request)
    {
        return $this->listOrders(
            $request,
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
     *
     * @param object $request Object containing request details
     *
     * @return WP_REST_Response
     */
    public function create($request)
    {

        //Verify that post data is avabile
        if (isset($_POST) && !empty($_POST)) {

            $requiredKeys = array('order_articles', 'user_id');

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

            $data = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        } else {
            return new \WP_REST_Response(
                array(
                    'message' => __('The post request sent was empty.', 'modularity-resource-booking'),
                    'state' => 'error'
                ),
                400
            );
        }

        $orderId = strtoupper(substr(md5(microtime()), rand(0, 26), 8));

        //Define new post
        $postItem = array(
            'post_title' => $orderId,
            'post_type' => 'purchase',
            'post_status' => 'publish',
            'post_author' => (int)$data['user_id']
        );

        //Prepend id if proveided (converted to update)
        if (is_numeric($request->get_param('id')) && get_post_type($request->get_param('id')) == "purchase") {
            $postItem = array('ID' => $request->get_param('id')) + $postItem;
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
                201
            );
        }

        //Sanitize order items
        $orderArticles = $data['order_articles'];
        if (is_array($orderArticles) && !empty($orderArticles)) {
            $orderArticles = array_map(function($item) {
                $data = (array)json_decode(stripslashes(html_entity_decode($item)));
                $item = array(
                    'field_5c122674bc676' => $data['type'] ?? null,
                    'field_5bed43f2bf1f2' => $data['article_id'] ?? null,
                    'field_5c0fc17caefa5' => $data['slot_id'] ?? null,
                );
                return $item;
            }, $orderArticles);
        }

        for($int=0; $int < count($orderArticles); $int++){
            if(isset($orderArticles[$int]['field_5c122674bc676']) && !empty($orderArticles[$int]['field_5c122674bc676']) && $orderArticles[$int]['field_5c122674bc676'] === 'package') {
                $productIds =  \ModularityResourceBooking\Helper::getProductsByPackage($orderArticles[$int]['field_5bed43f2bf1f2']);

                foreach($productIds as $prodId){
                    $mediaItems = \ModularityResourceBooking\Helper\MediaUpload::upload($prodId, $_FILES);

                    if (is_object($mediaItems) && $mediaItems->error != null) {
                        return new \WP_REST_Response(
                            array(
                                'message' => $mediaItems->error,
                                'state' => 'error'
                            ),
                            201
                        );
                    }
                }

            } else {

                $prodId = $orderArticles[$int]['field_5bed43f2bf1f2'];
                $mediaItems = \ModularityResourceBooking\Helper\MediaUpload::upload($prodId, $_FILES);

                if (is_object($mediaItems) && $mediaItems->error != null) {
                    return new \WP_REST_Response(
                        array(
                            'message' => $mediaItems->error,
                            'state' => 'error'
                        ),
                        201
                    );
                }
            }
        }


        // Save order items to repeater field
        update_field('field_5c0fc16aaefa4', $orderArticles, $insert);


        //Update meta
        update_post_meta($insert, 'order_id', $orderId);

        //Update fields
        update_field('customer_id', self::$userId, $insert);
        update_field('order_status', get_field('order_status', 'option'), $insert);


        //Append attachment data
        if (is_array($mediaItems) && !empty($mediaItems)) {

            //Add items for storage of each id
            foreach ($mediaItems as $mediaKey => $mediaItem) {
                update_sub_field(array('media_items', $mediaKey+1, 'file'), $mediaItem, $insert);
            }

            //Add number of items avabile (hotfix!)
            update_post_meta($insert, 'media_items', count($mediaItems));
            update_post_meta($insert, '_media_items', 'field_5bffbfed18455');
        }

        //Return success
        return new \WP_REST_Response(
            array(
                'message' => sprintf(
                    __('Your order has been registered.', 'modularity-resource-booking')
                ),
//                'message' => sprintf(
//                    __('Your order of "%s" between %s and %s has been registered.', 'modularity-resource-booking'),
//                    $this->getPackageName($data['product_package_id']),
//                    $data['slot_start'],
//                    $data['slot_stop']
//                ),
                'order' => $this->filterorderOutput(get_post($insert)
                )
            ),
            201
        );
    }

    /**
     * Remove order with id x
     *
     * @param integer $request The request of order to remove
     *
     * @return WP_REST_Response
     */
    public function remove($request)
    {
        if (get_post_type($request->get_param('id')) != "purchase") {
            return new \WP_REST_Response(
                array(
                    'message' => __('That is not av valid order id.', 'modularity-resource-booking'),
                    'state' => 'error'
                ), 404
            );
        }

        if (wp_delete_post($request->get_param('id'))) {
            return new \WP_REST_Response(
                array(
                    'message' => __('Your order has been removed.', 'modularity-resource-booking'),
                    'state' => 'success'
                ), 200
            );
        }

        return new \WP_REST_Response(
            array(
                'message' => __('Could not remove that order due to an unknown error.', 'modularity-resource-booking'),
                'state' => 'error'
            ), 200
        );
    }

    /**
     * Modify order with id x
     *
     * @param integer $request The order to modify
     *
     * @return WP_REST_Response
     */
    public function modify($request)
    {
        return $this->create($request);
    }

    /**
     * Check that the current user is the owner of order x
     *
     * @param integer $orderId The order to remove
     *
     * @return bool
     */
    public function checkOrderOwnership($orderId) : bool
    {
        if (true || get_post_meta($orderId, 'user_id', true) === self::$userId) {
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

        if (true || is_user_logged_in() && current_user_can('create_posts')) {
            return true;
        }

        return false;
    }

    /**
     * Clean return array from uneccesary data (make it slimmer)
     *
     * @param array $orders Array (or object) reflecting items to output.
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
                $result[] = array(
                    'id' => (int) $order->ID,
                    'order_id' => (string) get_post_meta($order->ID, 'order_id', true),
                    'uid' => (int) $order->post_author,
                    'uname' => (string) get_userdata($order->post_author)->first_name . " " . get_userdata($order->post_author)->last_name,
                    'name' => (string) $order->post_title,
                    'date' => (string) $order->post_date,
                    'slug' => (string) $order->post_name,
// TODO display complete article list
//                    'period' => array(
//                        'start' => (string) get_post_meta($order->ID, 'slot_start', true),
//                        'stop' => (string) get_post_meta($order->ID, 'slot_stop', true)
//                    ),
//                    'product_package_id' => (int) get_post_meta($order->ID, 'product_package_id', true),
//                    'product_package_name' => (string) $this->getPackageName(get_post_meta($order->ID, 'product_package_id', true)),
                );
            }
        }

        //Append media data if owner
        if (is_array($result) && !empty($result)) {
            foreach ($result as $key => $item) {
                if ($item['uid'] == self::$userId) {
                    $result[$key] = $item + array(
                            'media' => (array) get_field('media_items', $item['id'])
                        );
                }
            }
        }

        //Append action links if owner
        if (is_array($result) && !empty($result)) {
            foreach ($result as $key => $item) {

                if ($item['uid'] == self::$userId) {
                    $result[$key] = $item + array(
                            'actions' => array(
                                'modify' => rest_url('ModularityResourceBooking/v1/ModifyOrder/' . $item['id']),
                                'delete' => rest_url('ModularityResourceBooking/v1/RemoveOrder/' . $item['id'])
                            )
                        );
                }
            }
        }

        return $result;
    }

    /**
     * Get a name of the package
     *
     * @param int $packageId The id of the pagage
     *
     * @return mixed String on found, false on invalid
     */
    public function getPackageName($packageId)
    {
        if ($packageObject = get_term($packageId)) {
            return $packageObject->name;
        }

        return false;
    }
}
