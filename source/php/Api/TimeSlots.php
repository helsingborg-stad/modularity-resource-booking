<?php

namespace ModularityResourceBooking\Api;

class TimeSlots
{

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
        //Get single order
        register_rest_route(
            "ModularityResourceBooking/v1",
            "Slots",
            array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => array($this, 'getSlots'),
            )
        );

    }

    /**
     * Registers an options page
     *
     * @return array $slots Slots orderd by time
     */
    public function getSlots()
    {
        $result = array();

        if (get_field('mod_res_book_automatic_or_manual', 'option') == "weekly") {

            //Decide what monday to refer to
            if (date("N") == 1) {
                $whatMonday = "monday";
            } else {
                $whatMonday = "last monday";
            }

            for ($n = 0; $n <= 52; $n++) {
                $result[] = array(
                    'start' => date(
                        'Y-m-d',
                        strtotime($whatMonday, strtotime('+' .$n. ' week'))
                    ) . " 00:00",
                    'stop' => date(
                        'Y-m-d',
                        strtotime('sunday', strtotime('+' . $n .' week'))
                    ) . " 23:59"
                );
            }

            return $result;
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

            return $result;
        }
    }

}
