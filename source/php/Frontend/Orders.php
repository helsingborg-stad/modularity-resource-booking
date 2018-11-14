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

    public function getOrder()
    {
        return new \WP_REST_Response(array("Single item order!"), 200);
    }

    public function listOrders()
    {
        return new \WP_REST_Response(get_posts(array()), 200);
    }

    public function create($request)
    {
        return new \WP_REST_Response(array('result' => __("Your order has been registered.")), 201);
    }

    public function remove($orderId)
    {
        if (get_post_type($orderId) == "order") {
            return new \WP_REST_Response(array('result' => __("This is not a valid order id.")), 404);
        }

        return new \WP_REST_Response(array('result' => __("Your order has been removed.")), 200);
    }

    public function modify($orderId)
    {
        if (get_post_type($orderId) == "order") {
            return new \WP_REST_Response(array('result' => __("This is not a valid order id.")), 404);
        }

        return new \WP_REST_Response(array('result' => __("Your order has been modified.")), 200);
    }

    public function checkOrderOwnership($orderId) : bool
    {
        if (true || get_post_meta($orderId, 'user_id', true) === self::$userId) {
            return true;
        }
        return false;
    }

    public function checkInsertCapability()
    {
        if (is_user_logged_in() && current_user_can('create_posts')) {
            return true;
        }
        return false;
    }
}
