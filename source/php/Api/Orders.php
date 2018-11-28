<?php

namespace ModularityResourceBooking\Api;

class Orders
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
     * @param object $request Object containing request details
     *
     * @return WP_REST_Response
     */
    public function listOrders($request)
    {
        return new \WP_REST_Response(
            $this->filterorderOutput(
                get_posts(
                    array(
                        'post_type' => 'purchase',
                        'posts_per_page' => 99,
                        'orderby' => 'date',
                        'order' => 'DESC'
                    )
                )
            ), 200
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

            $requiredKeys = array("slot_start", "slot_stop", "product_package_id");

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

        //Define new post
        $postItem = array(
            'post_title' => (
                __('Order of', 'modularity-resource-booking') . " " . $this->getPackageName($data['product_package_id']) . " " .
                "(" . $data['slot_start'] . " " . __('to', 'modularity-resource-booking') . " " . $data['slot_stop'] . ")"),
            'post_type' => 'purchase',
            'post_status' => 'publish',
            'post_author' => self::$userId,
        );

        //Prepend id if proveided (converted to update)
        if (is_numeric($request->get_param('id')) && get_post_type($request->get_param('id')) == "purchase") {
            $postItem = array('ID' => $request->get_param('id')) + $postItem;
        }

        //Makr insert
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

        //Update meta
        update_post_meta($insert, 'order_id', strtoupper(substr(md5(microtime()), rand(0, 26), 8)));
        update_post_meta($insert, 'slot_start', $data['slot_start']);
        update_post_meta($insert, 'slot_stop', $data['slot_stop']);

        //Update fields
        update_field('product_package_id', $data['product_package_id'], $insert);
        update_field('customer_id', self::$userId, $insert);
        update_field('order_status', get_field('order_status', 'option'), $insert);

        //Return success
        return new \WP_REST_Response(
            array(
                'message' => sprintf(
                    __('Your order of "%s" between %s and %s has been registered.', 'modularity-resource-booking'),
                    $this->getPackageName($data['product_package_id']),
                    $data['slot_start'],
                    $data['slot_stop']
                ),
                'order' => array_pop(
                    $this->filterorderOutput(
                        get_post($insert)
                    )
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
        if (true || get_post_meta($orderId, 'user_id', true) === self::$userId) {
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
                    'period' => array(
                        'start' => (string) get_post_meta($order->ID, 'slot_start', true),
                        'stop' => (string) get_post_meta($order->ID, 'slot_stop', true)
                    ),
                    'product_package_id' => (int) get_post_meta($order->ID, 'product_package_id', true),
                    'product_package_name' => (string) $this->getPackageName(get_post_meta($order->ID, 'product_package_id', true)),
                );
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
