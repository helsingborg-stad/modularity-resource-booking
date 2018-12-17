<?php

namespace ModularityResourceBooking\Helper;

class Slots
{
    public static function getArticleStock($articleType, $articleId, $slotId, $userId)
    {
        error_log("Article ID");
        error_log($articleId);

        error_log("Slot ID");
        error_log($slotId);

        $products = array();
        if ($articleType === 'package') {
            // List of products form one package
            // error_log("Products in package<";
            $products = self::getProductsByPackage($articleId);
        } elseif ($articleType === 'product') {
            // The product
            //echo "<br><b>Products in package</b><br>";
            $product = get_post($articleId);
            if (!empty($product)) {
                $products[] = $product;
            }
        }

        if (empty($products)) {
            return new \WP_Error(
                'empty_result',
                __('No articles could be found with \'article_id\': ' . $articleId, 'modularity-resource-booking')
            );
        }

        // Todo move to own method

        // Get customer group
        $customerGroup = wp_get_object_terms($userId, 'customer_group', array('fields' => 'ids'));
        $groupLimit = null;
        $customerIds = null;
        if (isset($customerGroup[0]) && !empty($customerGroup[0])) {
            // Get customer group limit
            $groupLimit = get_field('customer_slot_limit', 'customer_group' . '_' . $customerGroup[0]);
            $groupLimit = $groupLimit === '' ? null : (int)$groupLimit;
            // List of users within same customer group
            $customerIds = \ModularityResourceBooking\Entity\Filter::getUserByTaxonomy('customer_group', $customerGroup[0]);
            $customerIds = array_column($customerIds, 'ID');
        }
        error_log("USER GROUP");
        error_log(print_r($customerGroup, true));
        error_log("GROUP LIMIT");
        error_log(print_r($groupLimit, true));
        error_log("CUSTOMER GROUP Users");
        error_log(print_r($customerIds, true));

        $products = array_map(function ($product) use ($articleType, $slotId, $customerIds, $groupLimit) {
            // List of packages where the product is included
            $packages = wp_get_post_terms($product->ID, 'product-package', array('fields' => 'ids'));
            $packages = is_array($packages) && !empty($packages) ? $packages : array();
            // Product stock
            $stock = get_field('items_in_stock', $product->ID);
            // Check if product if unlimited
            $unlimited = $stock === '' ? true : false;
            $stock = (int)$stock;
            // Calculate every time the product have been purchased within the slot period
            $articleIds = array_merge(array($product->ID), $packages);
            $orders = self::getOrdersByArticles($articleType, $articleIds, $slotId);
            $orderCount = count($orders);

            // Get number of times the customer/group have purchased this product
            $purchaseCount = 0;
            foreach ($orders as $order) {
                if (in_array($order->post_author, $customerIds)) {
                    $purchaseCount++;
                }
            }
            error_log("Unlimited:" . ($unlimited ? "true" : "false"));
            error_log("Stock total:" . $stock);
            error_log("Purchase Count:" . $purchaseCount);
            error_log("Order Count:" . $orderCount);

            // Calculate available stock
            $availableStock = $stock - $orderCount;
            error_log("Available after stock - orderCount:" . $availableStock);

            // Calculate with limit
            if ($groupLimit !== null) {
                $groupStock = $groupLimit - $purchaseCount;
                $availableStock = (!$unlimited && $availableStock < $groupStock) ? $availableStock : $groupStock;
                error_log("If Group Limit is set:" . $availableStock);
            } elseif ($groupLimit === null && $unlimited) {
                $availableStock = null;
            }

            error_log("FINISHED AVAILABLE STOCK:" . $availableStock);

            $product = array(
                'id' => $product->ID,
                'unlimited_stock' => $unlimited,
                'total_stock' => $unlimited ? null : $stock,
                'available_stock' => $availableStock
            );

            return $product;
        }, $products);

        // Remove null(Unlimited stock) values, to get list of products with a stock value
        $articlesWithStock = array_diff(array_column($products, 'available_stock'), array(null));
        if (!empty($articlesWithStock)) {
            // Get minimum stock value from all products
            $minimumStock = min($articlesWithStock);

            error_log("Min stock");
            error_log(print_r($minimumStock, true));
            // Get the product with the minimum stock value
            $productsWithMinStock = array_filter($products, function ($product) use ($minimumStock) {
                return ($product['available_stock'] === $minimumStock);
            });
            error_log("Filtered Products");
            error_log(print_r($productsWithMinStock, true));
            // Return first object, in case more than one article have the same available stock left
            return $productsWithMinStock[0];
        }

        error_log("Last Products");
        error_log(print_r($products, true));

        error_log("===========================END==================================");

        // Return first object (it should be a single product or all the products has unlimited stock)
        return $products[0];
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