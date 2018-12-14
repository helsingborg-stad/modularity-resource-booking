<?php

namespace ModularityResourceBooking\Api;

class TimeSlots
{
    public function __construct()
    {
        //Run register rest routes
        add_action('rest_api_init', array($this, 'registerRestRoutes'));
        //add_action('init', array($this, 'getSlots'));
        add_filter('posts_where', array($this, 'postsWhereWildcard'), 2, 10);
    }

    /**
     * Registers all rest routes for managing orders
     *
     * @return void
     */
    public function registerRestRoutes()
    {
        register_rest_route(
            "ModularityResourceBooking/v1",
            "Slots",
            array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => array($this, 'getSlots'),
                'args' => $this->getCollectionParams(),
            )
        );
    }

    /**
     * Get the query params for collections
     * @return array
     */
    public function getCollectionParams()
    {
        return array(
            'type' => array(
                'description' => 'The article type.',
                'type' => 'string',
                'default' => 'product',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'user_id' => array(
                'description' => 'User ID.',
                'type' => 'integer',
                'default' => 0,
                'sanitize_callback' => 'absint',
            ),
            'article_id' => array(
                'description' => 'Article ID.',
                'type' => 'integer',
                'default' => 0,
                'sanitize_callback' => 'absint',
            ),
        );
    }

    /**
     * Get slots with stock data
     * @return \WP_REST_Response
     */
    public function getSlots($request)
    {
        // Request params
        $params = $request->get_params();

        $result = array();
        if (get_field('mod_res_book_automatic_or_manual', 'option') == "weekly") {
            //Decide what monday to refer to
            if (date("N") == 1) {
                $whatMonday = "monday";
            } else {
                $whatMonday = "last monday";
            }

            for ($n = 0; $n <= 52; $n++) {
                $start = date('Y-m-d', strtotime($whatMonday, strtotime('+' . $n . ' week'))) . " 00:00";
                $stop = date('Y-m-d', strtotime('sunday', strtotime('+' . $n . ' week'))) . " 23:59";
                $slotId = \ModularityResourceBooking\Helper\Slots::getSlotId($start, $stop);

                $articleStock = \ModularityResourceBooking\Helper\Slots::getArticleStock($params['type'], $params['article_id'], $slotId, $params['user_id']);
                if (is_wp_error($articleStock)) {
                    return new \WP_REST_Response(
                        array(
                            'message' => $articleStock->get_error_message(),
                            'state' => 'error'
                        ), 404
                    );
                }

                $result[] = array(
                    'id' => $slotId,
                    'start' => $start,
                    'stop' => $stop,
                    'unlimited_stock' => $articleStock['unlimited_stock'],
                    'total_stock' => $articleStock['total_stock'],
                    'available_stock' => $articleStock['available_stock'],
                );
            }
        }

        if (get_field('mod_res_book_automatic_or_manual', 'option') == "manual") {
            $data = get_field('mod_res_book_time_slots', 'option');
            if (is_array($data) && !empty($data)) {
                foreach ($data as $item) {
                    $result[] = array(
                        'start' => $item['start_date'] . " 00:00",
                        'stop' => $item['end_date'] . " 23:59"
                    );
                }
            }
        }

        if (!empty($result)) {
            return new \WP_REST_Response($result, 200);
        } else {
            return new \WP_REST_Response(
                array(
                    'message' => __('No result found.', 'modularity-resource-booking'),
                    'state' => 'error'
                ), 404
            );
        }
    }

    /**
     * Adds wildcards to meta query when searching for slot & package IDs
     * @param $where
     * @param $query
     * @return mixed
     */
    public function postsWhereWildcard($where, $query)
    {
        if (isset($query->query['post_type']) && $query->query['post_type'] === 'purchase') {
            $where = str_replace("meta_key = 'order_articles_\$_type", "meta_key LIKE 'order_articles_%_type", $where);
            $where = str_replace("meta_key = 'order_articles_\$_slot_id", "meta_key LIKE 'order_articles_%_slot_id", $where);
            $where = str_replace("meta_key = 'order_articles_\$_article_id", "meta_key LIKE 'order_articles_%_article_id", $where);
        }

        return $where;
    }
}
