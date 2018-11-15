<?php

namespace ModularityResourceBooking\Frontend;

class Orders
{

    public static $userId;

    public function __construct()
    {
        //Get current user
        self::$userId = get_current_user_id();

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
            "ListOrders",
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
        return new \WP_REST_Response(array("Single item order!"), 200);
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
        return new \WP_REST_Response(array('result' => __('Your order has been registered.', 'modularity-resource-booking')), 201);
    }

    /**
     * Remove order with id x
     *
     * @param integer $orderId The order to remove
     *
     * @return WP_REST_Response
     */
    public function remove($orderId)
    {
        if (get_post_type($orderId) == "order") {
            return new \WP_REST_Response(array('result' => __('That is not av valid order id.', 'modularity-resource-booking')), 404);
        }

        return new \WP_REST_Response(array('result' => __('Your order has been removed.', 'modularity-resource-booking')), 200);
    }

    /**
     * Modify order with id x
     *
     * @param integer $orderId The order to modify
     *
     * @return WP_REST_Response
     */
    public function modify($orderId)
    {
        if (get_post_type($orderId) == "order") {
            return new \WP_REST_Response(array('result' => __("This is not a valid order id.")), 404);
        }

        return new \WP_REST_Response(array('result' => __('Your order has been modified.', 'modularity-resource-booking')), 200);
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
        if (is_user_logged_in() && current_user_can('create_posts')) {
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
                    'id' => $order->ID,
                    'uid' => $order->post_author,
                    'uname' => get_userdata($order->post_author)->first_name . " " . get_userdata($order->post_author)->last_name,
                    'name' => $order->post_title,
                    'date' => $order->post_date,
                    'slug' => $order->post_name,
                    'period' => array(
                        'start' => '',
                        'stop' => ''
                    )
                );
            }
        }

        return $result;
    }
}
