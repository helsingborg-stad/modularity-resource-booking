<?php

namespace ModularityResourceBooking;

class Orders extends \ModularityResourceBooking\Entity\PostType
{
    public static $postTypeSlug;
    public static $statusTaxonomySlug;

    public function __construct()
    {
        //Main post type
        self::$postTypeSlug = $this->postType();

        //Taxonomy
        self::$statusTaxonomySlug = $this->taxonomyOrderStatus();

        //Remove form Municipio template filter
        add_filter('Municipio/CustomPostType/ExcludedPostTypes', array($this, 'excludePostType'));

        //Filter handlebars on admin page
        add_filter('acf/prepare_field/key=field_5bed431057e88', array($this, 'replaceOrderId'), 10, 1); //Order ID
        add_filter('acf/prepare_field/key=field_5c12369d5bc92', array($this, 'listOrderArticles'), 10, 1); //Time Slot

        // Hide ACF field
        add_filter('acf/load_field/key=field_5c0fc16aaefa4', array($this, 'hideField'));

        //Save author to post on change
        add_action('save_post', array($this, 'updateAuthor'));

        //Do actions on taxonomy change
        add_action('save_post', array($this, 'taxonomyChangeActions'), 1);
    }

    /**
     * Do action on status change
     *
     * @param int $postId The id of post being saved
     *
     * @return void
     */
    public function taxonomyChangeActions($postId)
    {

        //Not order posttype
        if (get_post_type($postId) != self::$postTypeSlug) {
            return;
        }

        //Only in admin
        if (!is_admin()) {
            return;
        }

        //Get post data
        $data = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        //Not defined
        if (!isset($data["acf"][get_field_object('order_status')['key']])) {
            return;
        }

        //Get term setups
        $newTermSetup = $data["acf"][get_field_object('order_status')['key']];
        $oldTermSetup = get_the_terms($postId, self::$statusTaxonomySlug);

        if (is_array($oldTermSetup) && !empty($oldTermSetup)) {
            foreach ($oldTermSetup as $term) {
                if ($term->term_id != $newTermSetup) {

                    //Get actions
                    $actionOnAcquisition = get_field('do_action_on_aqusition', self::$statusTaxonomySlug . "_" . $newTermSetup);

                    //Send email to economy
                    if (!is_null($actionOnAcquisition) && in_array('economy_mail', $actionOnAcquisition)) {

                        new \ModularityResourceBooking\Helper\EconomyMail(
                            __('Request of new invoice', 'modularity-resource-booking'),
                            __('This is a request to create an invoice to the specifications below.', 'modularity-resource-booking'),
                            array(
                                array(
                                    'heading' => __('Order number:', 'modularity-resource-booking'),
                                    'content' => get_post_meta($postId, 'order_id', true)
                                ),
                                array(
                                    'heading' => __('Ordered articles:', 'modularity-resource-booking'),
                                    'content' => Helper\Product::name(
                                        Helper\ArrayParser::getSubKey(
                                            get_field('order_articles', $postId),
                                            'article_id'
                                        )
                                    )
                                ),
                                array(
                                    'heading' => __('Our reference: ', 'modularity-resource-booking'),
                                    'content' => Helper\Customer::getName(get_current_user_id()),
                                ),
                                array(
                                    'heading' => __('Their reference: ', 'modularity-resource-booking'),
                                    'content' => Helper\Customer::getName($data["acf"][get_field_object('customer_id')['key']])
                                ),
                                array(
                                    'heading' => __('Total (exluding VAT): ', 'modularity-resource-booking'),
                                    'content' => Helper\Product::price(805, true)
                                ),
                                array(
                                    'heading' => __('Notes: ', 'modularity-resource-booking'),
                                    'content' => $data["acf"][get_field_object('order_notations')['key']]
                                )
                            )
                        );
                    }

                    //Send email to customer
                    if (!is_null($actionOnAcquisition) && in_array('customer_approval_mail', $actionOnAcquisition)) {
                        new \ModularityResourceBooking\Helper\CustomerMail(
                            Helper\Customer::getEmail($data["acf"][get_field_object('customer_id')['key']]),
                            __('Order approved', 'modularity-resource-booking'),
                            __('Your order has been verified by us, and scheduled at your desired occasion.', 'modularity-resource-booking'),
                            array(
                                array(
                                    'heading' => __('Order number:', 'modularity-resource-booking'),
                                    'content' => get_post_meta($postId, 'order_id', true)
                                ),
                                array(
                                    'heading' => __('Articles:', 'modularity-resource-booking'),
                                    'content' => Helper\Product::name(
                                        Helper\ArrayParser::getSubKey(
                                            get_field('order_articles', $postId),
                                            'article_id'
                                        )
                                    )
                                ),
                                array(
                                    'heading' => __('Our reference: ', 'modularity-resource-booking'),
                                    'content' => Helper\Customer::getName(get_current_user_id())
                                ),
                                array(
                                    'heading' => __('Total (exluding VAT): ', 'modularity-resource-booking'),
                                    'content' => Helper\Product::price(805, true)
                                )
                            )
                        );
                    }
                }
            }
        }

    }

    /**
     * Replaces the order id handlebar
     *
     * @param int $postId The id of post being saved
     *
     * @return void
     */
    public function updateAuthor($postId)
    {

        //Not order posttype
        if (get_post_type($postId) != self::$postTypeSlug) {
            return;
        }

        //Only in admin
        if (!is_admin()) {
            return;
        }

        //Check if customer id is set & valid
        if (!isset($_POST['field_5bed438cc99db'])) {
            return;
        }

        if (!is_numeric($_POST['field_5bed438cc99db'])) {
            return;
        }

        //Update author from meta
        if ($authorId = $_POST['field_5bed438cc99db'] != get_post_field('post_author', $postId)) {
            wp_update_post(
                array(
                    'ID' => $postId,
                    'post_author' => $authorId
                )
            );
        }
    }

    /**
     * Replaces the order id hablebar
     *
     * @param array $field Field definitions
     *
     * @return array
     */
    public function replaceOrderId($field)
    {
        global $post;
        if (isset($field['message']) && !empty($field['message'])) {
            $field['message'] = str_replace("{{ORDER_ID}}", get_post_meta($post->ID, 'order_id', true), $field['message']);
        }
        return $field;
    }

    /**
     * Display a list of ordered articles
     * @param array $field Field definitions
     * @return array
     */
    public function listOrderArticles($field)
    {
        if (isset($field['message'])) {
            $orders = '';
            $rows = get_field('order_articles');
            if ($rows) {
                $orders .= '<table class="widefat">
                                <thead>
                                    <tr>
                                        <th class="row-title"> ' . __('Article', 'modularity-resource-booking') . ' </th>
                                        <th>' . __('Type', 'modularity-resource-booking') . '</th>
                                        <th> ' . __('Time period', 'modularity-resource-booking') . ' </th>
                                    </tr>
                                </thead>
	                            <tbody>';
                foreach ($rows as $row) {
                    $interval = Api\TimeSlots::getSlotInterval($row['slot_id']);

                    switch ($row['type']) {
                        case 'package':
                            $term = get_term($row['article_id'], 'product-package');
                            $title = $term->name ?? '';
                            $url = get_edit_term_link($row['article_id'], 'product-package');
                            break;
                        default:
                            $title = get_the_title($row['article_id']);
                            $url = get_edit_post_link($row['article_id']);
                    }

                    $orders .= '
                                <tr>
                                    <td class="row-title"><label for="tablecell"><a href="' . $url . '">' . $title . '</a></label></td>
                                    <td>' . ucfirst(array_pop($row['type'])) . '</td>
                                    <td>' . $interval['start'] . ' - ' . $interval['stop'] . '</td>
                                </tr>';
                }
                $orders .= '</tbody></table>';
            }
            $field['message'] = $orders;
        }
        return $field;
    }

    /**
     * Exclude from post type from Muncipio posttype filters
     *
     * @param array $postTypes Array containing posttypes that should be filterable
     *
     * @return void
     */
    public function excludePostType($postTypes)
    {
        $postTypes[] = $this->postType();
        return $postTypes;
    }

    /**
     * Create post type
     *
     * @return void
     */
    public function postType(): string
    {
        // Create posttype
        $postType = new \ModularityResourceBooking\Entity\PostType(
            _x('Orders', 'Post type plural', 'modularity-resource-booking'),
            _x('Order', 'Post type singular', 'modularity-resource-booking'),
            'purchase',
            array(
                'description' => __('Order registry.', 'modularity-resource-booking'),
                'menu_icon' => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDYxMS45OTYgNjExLjk5NiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNjExLjk5NiA2MTEuOTk2OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij48Zz48Zz48cGF0aCBkPSJNNTg4LjYzLDExMy4xOTNMMjEzLjgxMiwzMy44NzFjLTE1Ljg1OC0zLjM1NS0zMS41NzYsNi44NzYtMzQuOTMxLDIyLjczNGwtNy4xMjEsNDUuNzYybDQzMi40NzcsOTEuNTE5bDcuMTIxLTQ1Ljc2MiAgICBDNjE0LjcxMywxMzIuMjcyLDYwNC40ODgsMTE2LjU0OSw1ODguNjMsMTEzLjE5M3oiIGZpbGw9IiNGRkZGRkYiLz48cGF0aCBkPSJNNDMxLjAwOSwyMDMuNTkxYy00LjM3OC0xNS43NjYtMjAuODU0LTI1LjA4NS0zNi42MTUtMjAuNzE0TDMyMy4yNCwyMDIuNjNsLTE2Ny43NDItMzUuNWwtMTguNDQ4LDg3LjE2NUwyMS43ODYsMjg2LjI4NyAgICBjLTE1Ljc2LDQuMzcyLTI1LjA3OSwyMC44NDgtMjAuNzA4LDM2LjYwOWw2NC45NTgsMjM0LjA3OGM0LjM3OCwxNS43NiwyMC44NTUsMjUuMDg1LDM2LjYxNSwyMC43MDhsMzcyLjYwOC0xMDMuNDAzICAgIGMxNS43Ni00LjM3OCwyNS4wNzktMjAuODQ4LDIwLjcwOC0zNi42MTVsLTExLjE1LTQwLjE4NGw0MS43ODksOC44MzVjMTUuODU4LDMuMzYxLDMxLjU3Ni02Ljg3LDM0LjkzMS0yMi43MjhsMjYuNDM5LTEyNC45MzcgICAgTDQzNy40NSwyMjYuNzk3TDQzMS4wMDksMjAzLjU5MXogTTQ3NC4wNCwzMjIuNTU5bDkuMjE1LTQzLjU1MmMxLjM4NC02LjUyMSw3Ljg1LTEwLjcyNywxNC4zNy05LjM1bDQzLjU1Miw5LjIyMSAgICBjNi41MjcsMS4zODQsMTAuNzMzLDcuODQzLDkuMzU2LDE0LjM3bC05LjIxNSw0My41NTJjLTEuMzg0LDYuNTIxLTcuODQ5LDEwLjczMy0xNC4zNyw5LjM1bC00My41NTItOS4yMTUgICAgQzQ3Ni44NjMsMzM1LjU0Niw0NzIuNjU2LDMyOS4wOCw0NzQuMDQsMzIyLjU1OXogTTI4LjI3LDMwOS42NDZsMTAzLjExNS0yOC42MDZsMjQzLjI5OS02Ny41MTdsMjYuMTgxLTcuMjc0ICAgIGMwLjQ3OC0wLjEyOSwwLjk1NS0wLjE5LDEuNDIxLTAuMTljMi4xLDAsNC42MTEsMS4zNzgsNS4zNDUsNC4wMTdsMy4wNzQsMTEuMDdsOS42MzEsMzQuNzA0TDM3LjE0OCwzNjIuMTg2bC0xMi43MDUtNDUuNzY4ICAgIEMyMy42NDcsMzEzLjU0NiwyNS4zOTksMzEwLjQ0MiwyOC4yNywzMDkuNjQ2eiBNNDcyLjYwMSw0NDQuMTQxYzAuNDksMS43NzYtMC4wMjQsMy4yNDUtMC41NDUsNC4xNjQgICAgYy0wLjUxNCwwLjkxOC0xLjUwNiwyLjExOS0zLjI4MiwyLjYwOEw5Ni4xNzMsNTU0LjMxNmMtMC40NzEsMC4xMjktMC45NTUsMC4xOTYtMS40MjEsMC4xOTZjLTIuMSwwLTQuNjExLTEuMzg0LTUuMzQ1LTQuMDIzICAgIEw1MS41MTksNDEzLjk1NWwzODMuMTg4LTEwNi4zNDJsMjMuMzcxLDg0LjIwOEw0NzIuNjAxLDQ0NC4xNDF6IiBmaWxsPSIjRkZGRkZGIi8+PHBhdGggZD0iTTE1Ni4zNzksNDUzLjQ4NGMtMS43ODgtNi40MjktOC40OTktMTAuMjI1LTE0LjkyOC04LjQ0M2wtNDMuNTE1LDEyLjA4Yy02LjQyMywxLjc4Mi0xMC4yMjUsOC40OTktOC40MzcsMTQuOTI4ICAgIGwxMi4wNzQsNDMuNTA5YzEuNzg4LDYuNDI5LDguNDk5LDEwLjIyNSwxNC45MjgsOC40MzdsNDMuNTE1LTEyLjA3NGM2LjQyOS0xLjc4MiwxMC4yMjUtOC40OTksOC40NDMtMTQuOTI4TDE1Ni4zNzksNDUzLjQ4NHoiIGZpbGw9IiNGRkZGRkYiLz48L2c+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjxnPjwvZz48Zz48L2c+PGc+PC9nPjwvc3ZnPg==',
                'public' => true,
                'publicly_queriable' => true,
                'show_ui' => true,
                'show_in_nav_menus' => true,
                'has_archive' => true,
                'rewrite' => array(
                    'slug' => __('purchase', 'modularity-resource-booking'),
                    'with_front' => false
                ),
                'hierarchical' => false,
                'exclude_from_search' => false,
                'taxonomies' => array(),
                'supports' => array('title', 'revisions')
            )
        );

        /* Customer column */
        $postType->addTableColumn(
            'customer',
            __('Customer', 'modularity-resource-booking'),
            true,
            function ($column, $postId) {

                $userId = get_post_meta($postId, 'customer_id', true);

                if ($userId) {
                    echo Helper\Customer::getName($userId);
                } else {
                    _e("Undefined", 'modularity-resource-booking');
                }
            }
        );

        /* Status */
        $postType->addTableColumn(
            'status',
            __('Status', 'modularity-resource-booking'),
            true,
            function ($column, $postId) {
                $i = 0;

                $types = get_the_terms($postId, self::$statusTaxonomySlug);

                if (empty($types)) {
                    _e("Undefined", 'modularity-resource-booking');
                } else {
                    foreach ((array)$types as $typeKey => $type) {

                        echo $type->name;

                        if ($typeKey + 1 !== count($types)) {
                            echo ", ";
                        }

                    }
                }
            }
        );

        /* Customer column */
        $postType->addTableColumn(
            'orderid',
            __('Order ID', 'modularity-resource-booking'),
            true,
            function ($column, $postId) {

                $orderId = get_post_meta($postId, 'order_id', true);

                if ($orderId) {
                    echo $orderId;
                } else {
                    _e("Undefined", 'modularity-resource-booking');
                }
            }
        );

        /* Return the slug */
        return $postType->slug;
    }

    /**
     * Create package taxonomy
     *
     * @return string
     */
    public function taxonomyOrderStatus(): string
    {
        //Register product packages
        $orderStatus = new \ModularityResourceBooking\Entity\Taxonomy(
            __('Order statuses', 'modularity-resource-booking'),
            __('Order status', 'modularity-resource-booking'),
            'order-status',
            array(self::$postTypeSlug),
            array(
                'hierarchical' => false
            )
        );

        //Add filter
        new \ModularityResourceBooking\Entity\Filter(
            $orderStatus->slug,
            self::$postTypeSlug
        );

        $TaxonomySlug = $orderStatus->slug;
        $postTypeSlug = self::$postTypeSlug;

        //Remove meta box
        add_action(
            'admin_menu',
            function () use ($TaxonomySlug, $postTypeSlug) {
                \remove_meta_box("tagsdiv-" . $TaxonomySlug, $postTypeSlug, 'side');
            },
            10
        );

        //Return taxonomy slug
        return $orderStatus->slug;
    }

    /**
     * Hide ACF field
     * @param $field
     * @return mixed
     */
    public function hideField($field)
    {
        $field['conditional_logic'] = 1;
        return $field;
    }
}
