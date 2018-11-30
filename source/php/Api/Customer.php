<?php

namespace ModularityResourceBooking\Api;

class Customer
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

        //Check if emailo exists
        register_rest_route(
            "ModularityResourceBooking/v1",
            "UserEmailExists",
            array(
                'methods' => \WP_REST_Server::ALLMETHODS,
                'callback' => array($this, 'emailExists')
            )
        );

    }

    /**
     * Check if a email exists in user table
     *
     * @return WP_REST_Response
     */
    public function emailExists()
    {
        $data = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        if (!isset($data['email']) || empty($data['email'])) {
            return new \WP_REST_Response(
                array(
                    'message' => __('Email cannot be empty.', 'modularity-resource-booking'),
                    'state' => 'error'
                ), 406
            );
        }

        if (email_exists($data['email'])) {
            return new \WP_REST_Response(
                array(
                    'message' => __('The email provided does already exist in our account system. It you think this is a error, please contact a administrator.', 'modularity-resource-booking'),
                    'state' => 'error'
                ), 406
            );
        }

        return new \WP_REST_Response(
            array(
                'message' => __('No account found with that email.', 'modularity-resource-booking'),
                'state' => 'success'
            ), 200
        );

    }
}
