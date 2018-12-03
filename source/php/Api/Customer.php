<?php

namespace ModularityResourceBooking\Api;

class Customer
{
    public static $userId;
    public static $metaKeys;

    public function __construct()
    {
        //Run register rest routes
        add_action('rest_api_init', array($this, 'registerRestRoutes'));

        //Add required meta keys as array
        self::$metaKeys = array(
            'corporate_number' => __('Corporate number', 'modularity-resource-booking')
        );
    }

    /**
     * Registers all rest routes for managing customers
     *
     * @return void
     */
    public function registerRestRoutes()
    {

        //Get user id
        self::$userId = get_current_user_id();

        //Check if email exists
        register_rest_route(
            "ModularityResourceBooking/v1",
            "UserEmailExists",
            array(
                'methods' => \WP_REST_Server::ALLMETHODS,
                'callback' => array($this, 'emailExists')
            )
        );

        //Create user account
        register_rest_route(
            "ModularityResourceBooking/v1",
            "CreateUser",
            array(
                'methods' => \WP_REST_Server::ALLMETHODS,
                'callback' => array($this, 'create'),
                'args' => array(
                    'id' => array(
                        'validate_callback' => function ($param, $request, $key) {
                            return is_numeric($param);
                        }
                    ),
                )
            )
        );

        //Modify user account
        register_rest_route(
            "ModularityResourceBooking/v1",
            "ModifyUser/(?P<id>[\d]+)",
            array(
                'methods' => \WP_REST_Server::ALLMETHODS,
                'callback' => array($this, 'modify')
            )
        );

        //Get user account
        register_rest_route(
            "ModularityResourceBooking/v1",
            "GetUser/(?P<id>[\d]+)",
            array(
                'methods' => \WP_REST_Server::ALLMETHODS,
                'callback' => array($this, 'get'),
                'args' => array(
                    'id' => array(
                        'validate_callback' => function ($param, $request, $key) {
                            return is_numeric($param);
                        }
                    ),
                )
            )
        );

    }

    /**
     * Get a user by id
     *
     * @param object $request Object containing request details
     *
     * @return WP_REST_Response
     */
    public function get($request)
    {
        //Update user
        if ($userId = wp_update_user($updateArray)) {
            return array_pop(
                $this->filterCustomerOutput(
                    get_user_by('ID', $request->get_param('id'))
                )
            );
        }
    }

    /**
     * Create or update a user
     *
     * @param object $request Object containing request details
     *
     * @return WP_REST_Response
     */
    public function create($request)
    {
        $requiredKeys = array("email", "password");

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

        //Check if user id exists
        if (email_exists($data['email']) || username_exists($data['email'])) {
            return array(
                'message' => __('A user account is already registered with this email or username.', 'modularity-resource-booking'),
                'state' => 'error'
            );
        }

        if ($user = wp_create_user($data['email'], $data['password'], $data['email'])) {
            return array(
                'message' => __('A new user account has been created.', 'modularity-resource-booking'),
                'state' => 'success',
                'customer' => $this->filterCustomerOutput(
                    get_user_by('ID', $userId)
                )
            );
        }

    }

    /**
     * Masking function for create/update functionality
     *
     * @param object $request Object containing request details
     *
     * @return WP_REST_Response
     */
    public function modify($request)
    {
        $data = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        //Mapping table (enables us to rename input variables in api)
        $fieldMap = array(
            'ID' => 'id',
            'user_email' => 'email',
            'display_name' => 'company'
        );

        //Check if user id exists
        if (!get_user_by('ID',  $request->get_param('id'))) {
            return array(
                'message' => __('That is not a valid user identification.', 'modularity-resource-booking'),
                'state' => 'error'
            );
        }

        //Define update array
        $updateArray = array('ID' =>  $request->get_param('id'));

        //Update array creation of to be updated fields
        foreach ($fieldMap as $fielName => $inputField) {
            if (isset($data[$inputField])) {
                $updateArray[$fielName] = $data[$inputField];
            }
        }

        //Update user meta data
        foreach (self::$metaKeys as $metaKey => $metaField) {
            if (isset($data[$metaKey])) {
                update_usermeta($request->get_param('id'), $metaKey, $data[$metaKey]);
            }
        }

        //Update user
        if ($userId = wp_update_user($updateArray)) {
            return array(
                'message' => __('Your account details has been updated.', 'modularity-resource-booking'),
                'state' => 'success',
                'customer' => $this->filterCustomerOutput(
                    get_user_by('ID', $userId)
                )
            );
        }
    }

    /**
     * Check if a email exists in user table
     *
     * @param object $request Object containing request details
     *
     * @return WP_REST_Response
     */
    public function emailExists($request)
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

    /**
     * Clean return array from uneccesary data (make it slimmer)
     *
     * @param array $users  Array (or object) reflecting items to output.
     * @param array $result Declaration of result
     *
     * @return array $result Resulting array object
     */
    public function filterCustomerOutput($users, $result = array())
    {

        //Wrap single item in array
        if (is_object($users) && !is_array($users)) {
            $users = array($users);
        }

        if (is_array($users) && !empty($users)) {
            foreach ($users as $user) {

                //Automatically fetch user meta
                $userMeta = array();
                foreach (self::$metaKeys as $key => $value) {
                    $userMeta[$key] = get_user_meta($user->data->ID, $key, true);
                }

                //Create response object
                $result[] = array(
                    'id' => (int) $user->data->ID,
                    'username' => (string) $user->data->user_login,
                    'name' => (string) $user->data->display_name,
                    'meta' => (array) $userMeta
                );
            }
        }

        return $result;
    }
}
