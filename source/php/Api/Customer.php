<?php

namespace ModularityResourceBooking\Api;

class Customer
{
    public static $userId;
    public static $metaKeys;
    public static $fieldMap;

    public function __construct()
    {
        //Run register rest routes
        add_action('rest_api_init', array($this, 'registerRestRoutes'));

        //Meta keys containing user meta (define to allow insert & modification on key)
        self::$metaKeys = array(
            'billing_company' => __('Company name', 'modularity-resource-booking'),
            'billing_company_number' => __('Company number', 'modularity-resource-booking'),
            'billing_contact_person' => __('Contact person', 'modularity-resource-booking'),
            'billing_address' => __('Billing address', 'modularity-resource-booking'),
            'billing_glnr_number' => __('Glnr (e-invoice number)', 'modularity-resource-booking'),
            'billing_vat_number' => __('VAT-Number', 'modularity-resource-booking'),
            'phone' => __('Phone number', 'modularity-resource-booking')
        );

        //Mapping table (api input to wp usert table names)
        self::$fieldMap = array(
            'user_email' => 'email',
            'display_name' => 'company_name',
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'user_url' => 'website'
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

        //Create user account
        register_rest_route(
            "ModularityResourceBooking/v1",
            "CreateUser",
            array(
                'methods' => \WP_REST_Server::CREATABLE,
                'callback' => array($this, 'create'),
            )
        );

        //Modify user account
        register_rest_route(
            "ModularityResourceBooking/v1",
            "ModifyUser/(?P<id>[\d]+)",
            array(
                'methods' => \WP_REST_Server::EDITABLE,
                'callback' => array($this, 'modify'),
                'permission_callback' => array($this, 'canUpdateUser'),
                'args' => array(
                    'id' => array(
                        'validate_callback' => function ($param, $request, $key) {
                            return is_numeric($param);
                        },
                        'sanitize_callback' => 'absint',
                        'required' => true,
                        'type' => 'integer',
                        'description' => 'The user id.'
                    ),
                ),
            )
        );
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
        $requiredKeys = array("email", "password", "company");

        foreach ($requiredKeys as $requirement) {
            if (!array_key_exists($requirement, $_POST) && !empty($_POST[$requirement])) {
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
                'message' => __('A user account is already registered with this email.', 'modularity-resource-booking'),
                'state' => 'error'
            );
        }

        //Check if email is valid
        if (!is_email($data['email']) || strpos($data['email'], "+")) {
            return array(
                'message' => __('Malformed email adress provided.', 'modularity-resource-booking'),
                'state' => 'error'
            );
        }

        //Check password strength
        if (is_wp_error($strength = \ModularityResourceBooking\Helper\Validation::passwordStrenght($data['password']))) {
            return array(
                'message' => $strength->get_error_message(),
                'state' => 'error'
            );
        }

        //Check coordination number
        if (is_wp_error($coordination = \ModularityResourceBooking\Helper\Validation::coordinationNumber($data['billing_company_number']))) {
            return array(
                'message' => $coordination->get_error_message(),
                'state' => 'error'
            );
        } else {
            $cleaner = new \Olssonm\IdentityNumber\IdentityNumberFormatter($data['billing_company_number'], 10, true); 
            $data['billing_company_number'] = $cleaner->getFormatted(); 
        }        

        //Define update array
        $insertArray = array(
            'user_login' => $data['email'],
            'user_pass' => $data['password'],
            'role' => 'customer',
        );

        //Update array creation of to be updated fields
        foreach (self::$fieldMap as $fielName => $inputField) {
            if (isset($data[$inputField])) {
                $insertArray[$fielName] = $data[$inputField];
            }
        }

        //Insert the user
        if ($userId = wp_insert_user($insertArray)) {

            //Update user meta data
            foreach (self::$metaKeys as $metaKey => $metaField) {
                if (isset($data[$metaKey])) {
                    update_user_meta($userId, $metaKey, $data[$metaKey]);
                }
            }

            //Send manager email
            new \ModularityResourceBooking\Helper\ManagerMail(
                __('New customer', 'modularity-resource-booking'),
                __('A new customer been registered in your booking system. You have to review this customer and assign the customer account a user group.', 'modularity-resource-booking'),
                array(
                    array(
                        'heading' => __('Company:', 'modularity-resource-booking'),
                        'content' => \ModularityResourceBooking\Helper\Customer::getCompany($userId)
                    ),
                    array(
                        'heading' => __('Company number:', 'modularity-resource-booking'),
                        'content' => \ModularityResourceBooking\Helper\Customer::getCompanyNumber($userId)
                    ),
                    array(
                        'heading' => __('Glnr number: ', 'modularity-resource-booking'),
                        'content' => \ModularityResourceBooking\Helper\Customer::getGlnr($userId)
                    ),
                    array(
                        'heading' => __('Vat-number: ', 'modularity-resource-booking'),
                        'content' => \ModularityResourceBooking\Helper\Customer::getVat($userId)
                    ),
                    array(
                        'heading' => __('Contact name:', 'modularity-resource-booking'),
                        'content' => \ModularityResourceBooking\Helper\Customer::getName($userId)
                    ),
                    array(
                        'heading' => __('Email address: ', 'modularity-resource-booking'),
                        'content' => \ModularityResourceBooking\Helper\Customer::getEmail($userId)
                    ),
                    array(
                        'heading' => __('Phone number: ', 'modularity-resource-booking'),
                        'content' => \ModularityResourceBooking\Helper\Customer::getPhone($userId)
                    )
                ),
                array(
                    array(
                        'text' => __('Show profile', 'modularity-resource-booking'),
                        'url' => add_query_arg('user_id', $userId, self_admin_url('user-edit.php'))
                    )
                )
            );

            return array(
                'message' => __('Your user account has been created, you will be notified when its approved and ready to use.', 'modularity-resource-booking'),
                'state' => 'success',
                'customer' => $this->filterCustomerOutput(
                    get_user_by('ID', $userId)
                )
            );

        } else {
            return array(
                'message' => __('Something unexpected happened.', 'modularity-resource-booking'),
                'state' => 'error'
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

        //Check if user id exists
        if (!get_user_by('ID', $request->get_param('id'))) {
            return array(
                'message' => __('That is not a valid user identification.', 'modularity-resource-booking'),
                'state' => 'error'
            );
        }

        //Check if user id exists
        if (email_exists($data[self::$fieldMap['user_email']]) != self::$userId) {
            return array(
                'message' => __('That email adress is already taken, sorry.', 'modularity-resource-booking'),
                'state' => 'error'
            );
        }

        //Check if user id exists
        if (isset($data['email']) && !is_email($data['email']) || strpos($data['email'], "+")) {
            return array(
                'message' => __('Malformed email adress provided.', 'modularity-resource-booking'),
                'state' => 'error'
            );
        }

        //Define update array
        $updateArray = array(
            'ID' => $request->get_param('id'),
            'user_login' => get_userdata($request->get_param('id'))->user_login
        );

        //Set new password
        if (isset($data['password']) && !empty($data['password'])) {

            if (is_wp_error($strength = \ModularityResourceBooking\Helper\Validation::passwordStrenght($data['password']))) {
                return array(
                    'message' => $strength->get_error_message(),
                    'state' => 'error'
                );
            }

            wp_set_password($data['password'], $request->get_param('id'));
        }

        //Check coordination number
        if (is_wp_error($coordination = \ModularityResourceBooking\Helper\Validation::coordinationNumber($data['billing_company_number']))) {
            return array(
                'message' => $coordination->get_error_message(),
                'state' => 'error'
            );
        } else {
            $cleaner = new \Olssonm\IdentityNumber\IdentityNumberFormatter($data['billing_company_number'], 10, true); 
            $data['billing_company_number'] = $cleaner->getFormatted(); 
        }

        //Update array creation of to be updated fields
        foreach (self::$fieldMap as $fielName => $inputField) {
            if (isset($data[$inputField])) {
                $updateArray[$fielName] = $data[$inputField];
            }
        }

        //Update user meta data
        foreach (self::$metaKeys as $metaKey => $metaField) {
            if (isset($data[$metaKey])) {
                update_user_meta($request->get_param('id'), $metaKey, $data[$metaKey]);
            }
        }

        //Update user
        if ($userId = wp_insert_user($updateArray)) {
            return array(
                'message' => __('Your account details has been updated.', 'modularity-resource-booking'),
                'state' => 'success',
                'customer' => $this->filterCustomerOutput(
                    get_user_by('ID', $userId)
                )
            );
        } else {
            return array(
                'message' => __('Something unexpected happened.', 'modularity-resource-booking'),
                'state' => 'error'
            );
        }
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
                        'id' => (int)$user->data->ID,
                        'username' => (string)$user->data->user_login,
                        'email' => (string)$user->data->user_email,
                        'first_name' => (string)get_user_meta($user->data->ID, 'first_name', true),
                        'last_name' => (string)get_user_meta($user->data->ID, 'last_name', true),
                        'company_name' => (string)$user->data->display_name
                    ) + (array)$userMeta;
            }
        }

        return $result;
    }

    /**
     * Check that the current user is the ower of the current account
     *
     * @param object $request Request object
     *
     * @return bool
     */
    public function canUpdateUser($request): bool
    {
        $userId = (int)$request->get_param('id');

        //Bypass security, by constant
        if (RESOURCE_BOOKING_DISABLE_SECURITY) {
            return true;
        }

        if (is_user_logged_in() && is_super_admin()) {
            return true;
        }

        if (is_user_logged_in() && self::$userId === $userId) {
            return true;
        }

        return false;
    }
}
