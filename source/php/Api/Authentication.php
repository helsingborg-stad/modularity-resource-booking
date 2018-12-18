<?php

namespace ModularityResourceBooking\Api;

class Authentication
{
    public static $userId;

    public function __construct()
    {
        //Run register rest routes
        add_action('rest_api_init', array($this, 'registerRestRoutes'));
    }

    /**
     * Registers all rest routes for login / logout
     *
     * @return void
     */
    public function registerRestRoutes()
    {

        //Login rest route
        register_rest_route(
            "ModularityResourceBooking/v1",
            "Authentication/Login",
            array(
                'methods' => \WP_REST_Server::ALLMETHODS,
                'callback' => array($this, 'login')
            )
        );

        //Login rest route
        register_rest_route(
            "ModularityResourceBooking/v1",
            "Authentication/Logout",
            array(
                'methods' => \WP_REST_Server::ALLMETHODS,
                'callback' => array($this, 'logout')
            )
        );

    }

    /**
     * Login a user
     *
     * @param object $request Object containing request details
     *
     * @return WP_REST_Response
     */
    public function login($request)
    {

        //Verify provided data
        if ($request->get_param('username') && $request->get_param('password')) {

            //Try to signon
            $result = wp_signon(
                array(
                    'user_login' => $request->get_param('username'),
                    'user_password' => $request->get_param('password'),
                    'rememberme' => 1
                )
            );

            //Login successful
            if (!is_wp_error($result)) {
                return array(
                    'message' => __('Login successful.', 'modularity-resource-booking'),
                    'state' => 'success',
                    'user' => array_filter(
                        (array) $result->data,
                        function ($itemKey) {
                            return in_array($itemKey, array('ID', 'user_login', 'display_name'));
                        },
                        ARRAY_FILTER_USE_KEY
                    )
                );
            }

            //Incorrect password
            if (is_wp_error($result) && $result->get_error_code() == "incorrect_password") {
                return array(
                    'message' => __('The username and password you provided did not match.', 'modularity-resource-booking'),
                    'state' => 'error'
                );
            }

            //User not exists
            if (is_wp_error($result) && $result->get_error_code() == "invalid_username") {
                return array(
                    'message' => __('The username or email that you provided does not exists.', 'modularity-resource-booking'),
                    'state' => 'error'
                );
            }
        }

        return array(
            'message' => __('You have to provide both email and password.', 'modularity-resource-booking'),
            'state' => 'error'
        );
    }

    /**
     * Logout the user
     *
     * @param object $request Object containing request details
     *
     * @return WP_REST_Response
     */
    public function logout($request)
    {
        wp_logout();
        return array(
            'message' => __('You have now logged out.', 'modularity-resource-booking'),
            'state' => 'success'
        );
    }

}
