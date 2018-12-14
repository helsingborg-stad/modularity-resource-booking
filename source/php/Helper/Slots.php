<?php

namespace ModularityResourceBooking\Helper;

class Slots
{
    public static function getArticleStock($articleType, $articleId, $slotId, $userId)
    {
        error_log("Article ID");
        error_log ($articleId);

        error_log ("Slot ID");
        error_log ($slotId);

        $products = array();
        if ($articleType === 'package') {
            // List of products form one package
            // error_log("Products in package<";
            $products = self::getProductsByPackage($articleId);
        } elseif ($articleType === 'product') {
            // The product
            //echo "<br><b>Products in package</b><br>";
            $product = get_post($articleId);
            $products = !empty($product) ? $products[$product] : $products;
        }

        if (empty($products)) {
            return new \WP_Error(
                'empty_result',
                __('No articles could be found with \'article_id\': ' . $articleId, 'modularity-resource-booking')
            );
        }

        // Todo move to method

        // Get customer group
        $customerGroup = wp_get_object_terms($userId, 'customer_group', array('fields' => 'ids'));
        $groupLimit = null;
        if (isset($customerGroup[0]) && !empty($customerGroup[0])) {
            $groupLimit = get_field('customer_slot_limit', 'customer_group' . '_' . $customerGroup[0]);
            $groupLimit = $groupLimit === '' ? null : (int)$groupLimit;
        }
        error_log("USER GROUP");
        error_log(print_r($customerGroup, true));
        error_log("GROUP LIMIT");
        error_log(print_r($groupLimit, true));

        // Todo Get all user IDs within same group

        $products = array_map(function ($product) use ($articleType, $slotId) {
            // List of packages where the product is included
            $packages = wp_get_post_terms($product->ID, 'product-package', array('fields' => 'ids'));
            $packages = is_array($packages) && !empty($packages) ? $packages : array();
            // Product stock
            $stock = get_field('items_in_stock', $product->ID);
            // Check if product if unlimited
            $unlimited = $stock === '' ? true : false;
            // Calculate every time the product have been purchased within the slot period
            $articleIds = array_merge(array($product->ID), $packages);
            $orders = self::getOrdersByArticles($articleType, $articleIds, $slotId);
            error_log("Orders");
            error_log(print_r($orders, true));
            $orderCount = count($orders);
            //var_dump($orderCount);

            // Todo Get the group from orders to calculate available stock

            // Calculate available stock number
            $available = ((int)$stock - $orderCount);

            $product = array(
                'id' => $product->ID,
                'unlimited_stock' => $unlimited,
                'total_stock' => $unlimited ? null : (int)$stock,
                'available_stock' => $available
            );

            return $product;
        }, $products);

        error_log(print_r($products, true));

        // Todo Return the product data with least amount of stock left, or unlimited, and calc User limit

        $min = min(array_column($products, 'available_stock'));
        error_log("Minimum available Stock");
        error_log($min, true);
    }


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

//    public static function getSlotsTotalByPackage($termId)
//    {
//        $products = self::getProductsByPackage($termId);
//
//        if (empty($products)) {
//            return false;
//        }
//
//        $slotsTotal = 0;
//        //Get total slots (based on the product will the lowest stock)
//        foreach ($products as $product) {
//            if ($slotsTotal > get_field('items_in_stock', $product->ID) || $slotsTotal == 0) {
//                $slotsTotal = get_field('items_in_stock', $product->ID);
//            }
//        }
//
//        return $slotsTotal;
//    }

    public static function getOrdersByArticles($type = null, $articleIds = array(), $slotId = null)
    {
        $orders = get_posts(array(
            'post_type' => 'purchase',
            'numberposts' => -1,
            'suppress_filters' => false,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'order_articles_$_type',
                    'value' => $type,
                    'compare' => '='
                ),
                array(
                    'key' => 'order_articles_$_article_id',
                    'value' => $articleIds,
                    'compare' => 'IN'
                ),
                array(
                    'key' => 'order_articles_$_slot_id',
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