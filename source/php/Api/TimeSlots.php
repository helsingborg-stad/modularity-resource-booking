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
            'user_id' => array(
                'description' => 'User ID.',
                'type' => 'integer',
                'default' => 0,
                'sanitize_callback' => 'absint',
            ),
            'package_id' => array(
                'description' => 'Package ID.',
                'type' => 'integer',
                'default' => 0,
                'sanitize_callback' => 'absint',
            ),
        );
    }

    /**
     * @return array $slots Slots ordered by time
     */
    public function getSlots($request)
    {
        //$params = $request->get_params();
        //error_log($params['user_id']);
        //error_log($params['package_id']);

        error_log("==================");
        $userId = 3; // joah1032 admin
        $packageId = 147; // Paket 1

        $result = array();

        if (get_field('mod_res_book_automatic_or_manual', 'option') == "weekly") {
            //Decide what monday to refer to
            if (date("N") == 1) {
                $whatMonday = "monday";
            } else {
                $whatMonday = "last monday";
            }

            for ($n = 0; $n <= 52; $n++) {
                $available = true;

                $start = date('Y-m-d', strtotime($whatMonday, strtotime('+' . $n . ' week'))) . " 00:00";
                $stop = date('Y-m-d', strtotime('sunday', strtotime('+' . $n . ' week'))) . " 23:59";
                $slotId = \ModularityResourceBooking\Slots::getSlotId($start, $stop);

                $orders = \ModularityResourceBooking\Slots::getOrdersByPackageSlot($packageId, $slotId);

                $result[] = array(
                    'id' => $slotId,
                    'start' => $start,
                    'stop' => $stop,
                    'is_available' => $available,
                    'total_stock' => null, // innehållande vilken som är en produkts lägsta stockvärde, oklart om det funkar??
                    'available_stock' => null, // total_stock - antal ordrar för det datumet, som innehåller samma produkt. - slot limit
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

        //var_dump($result);
    }

}
