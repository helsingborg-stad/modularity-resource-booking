<?php

namespace ModularityResourceBooking;

class Slots
{
    public static function getProductsByPackage($termId)
    {
        //Make sure package (term) exists
        if (!term_exists($termId, 'product-package')) {
            return false;
        }

        $products = get_posts(array(
            'post_type' => 'product',
            'numberposts' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product-package',
                    'field' => 'term_id',
                    'terms' => $termId,
                    'include_children' => false
                )
            )
        ));

        return $products;
    }

    public static function getSlotsTotalByPackage($termId)
    {
        $products = self::getProductsByPackage($termId);

        if (empty($products)) {
            return false;
        }

        $slotsTotal = 0;
        //Get total slots (based on the product will the lowest stock)
        foreach ($products as $product) {
            if ($slotsTotal > get_field('items_in_stock', $product->ID) || $slotsTotal == 0) {
                $slotsTotal = get_field('items_in_stock', $product->ID);
            }
        }

        $availableSlots = 0;

        return $slotsTotal;
    }

    public static function getOrdersByPackageSlot($packageId, $slotId)
    {
        //Make sure package (term) exists
        if (!term_exists($packageId, 'product-package')) {
            return false;
        }

        $orders = get_posts(array(
            'post_type' => 'purchase',
            'numberposts' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'product_package_id',
                    'value' => $packageId,
                    'compare' => '='
                ),
                array(
                    'key' => 'slot_id',
                    'value' => $slotId,
                    'compare' => '='
                ),
            )
        ));

        return $orders;
    }

    public static function getSlotId($start, $stop)
    {
        $start = base_convert(strtotime($start), 10, 36);
        $stop = base_convert(strtotime($stop), 10, 36);
        return $start . '.' . $stop;
    }

    public static function getSlotInterval($slotId)
    {
        $dates = explode('.', $slotId);
        if (!(is_array($dates) && count($dates) === 2)) {
            return false;
        }

        $dates = array(
            'start' => isset($dates[0]) ? date('Y-m-d H:i', base_convert($dates[0], 36, 10)) : '',
            'stop' => isset($dates[1]) ? date('Y-m-d H:i', base_convert($dates[1], 36, 10)) : '',
        );

        return $dates;
    }
}