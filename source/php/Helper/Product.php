<?php

namespace ModularityResourceBooking\Helper;

class Product
{
    /**
     * Get the price of a product (post) or package (taxonomy)
     *
     * @param array|int|string $item           Object representing a taxonomy or
     *                                         post that should be parsed for price data
     * @param boolean          $currencySymbol Prepend currency symbol
     *
     * @return int|WP_Error $result Integer representing the product price
     */
    public static function price($item, $currencySymbol = false)
    {

        //Multiple items (split, and recuse)
        if (is_array($item) && !empty($item)) {
            foreach ($item as $multi) {
                $productArraySumPrice = $productArraySumPrice + self::price($multi);
            }
            if ($currencySymbol) {
                return (string) $productArraySumPrice . " " . RESOURCE_BOOKING_CURRENCY_SYMBOL;
            }
            return (int) $productArraySumPrice;
        }

        //Get object
        if (is_numeric($item)) {

            if ($tempItem = get_post($item)) {
                $item = $tempItem;
            } else {
                $item = get_taxonomy($item);
            }

            //Send error if not found
            if ($item === false) {
                return new \WP_Error(
                    'id_not_valid',
                    __('The id sent for price calculation is not valid.', 'modularity-resource-booking')
                );
            }
        }

        //Get term or post keys
        if (get_class($item) == "WP_Term") {
            $fieldName = "package_price";
        } elseif (get_class($item) == "WP_Post") {
            $fieldName = "product_price";
        } else {
            return new \WP_Error(
                'type_not_valid',
                __('The type sent for price calculation is not valid, must be WP_Post or WP_Term.', 'modularity-resource-booking')
            );
        }

        //Get this price
        $basePrice = get_field($fieldName, $item);

        //Get user group
        $userGroup = get_field('customer_group', 'user_' . get_current_user_id());

        //Get user groups
        $userGroupPrices = get_field('customer_group_price_variations', $item);

        //Get this user group price
        if (is_array($userGroupPrices) && !empty($userGroupPrices)) {
            foreach ($userGroupPrices as $userGroupPrice) {
                if ($userGroupPrice['customer_group'] == $userGroup) {

                    if ($currencySymbol) {
                        return $userGroupPrice['product_price'] . " " . RESOURCE_BOOKING_CURRENCY_SYMBOL;
                    }

                    return $userGroupPrice['product_price'];
                }
            }
        }

        //Could not find specified price, sum all products
        if (get_class($item) == "WP_Term" && empty($basePrice)) {

            $posts = get_posts(
                array(
                    'post_type' => 'product',
                    'numberposts' => -1,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product-package',
                            'field' => 'id',
                            'terms' => $item->term_id
                        )
                    )
                )
            );

            if (is_array($posts) && !empty($posts)) {
                $productSumPrice = null;
                foreach ($posts as $subitem) {
                    $productSumPrice = $productSumPrice + self::price($subitem);
                }

                if (!empty($productSumPrice)) {
                    if ($currencySymbol) {
                        return (string) $productSumPrice . " " . RESOURCE_BOOKING_CURRENCY_SYMBOL;
                    }
                    return (int) $productSumPrice;
                }
            }
        }

        //No price found, return base price
        if ($currencySymbol) {
            return (string) $basePrice . " " . RESOURCE_BOOKING_CURRENCY_SYMBOL;
        }
        return (int) $basePrice;
    }

    /**
     * Get a name of package or product
     *
     * @param int|array $items The id of the packages(s) or products(s)
     *
     * @return string Containing the names
     */
    public static function name($items)
    {
        //Declarations
        $result = array();

        //Steamline data (takes both int and array)
        if (!is_array($items)) {
            $items = array($items);
        }

        //Get packages
        if (is_array($items) && !empty($items)) {
            foreach ($items as $itemId) {
                if ($packageObject = get_term($itemId, 'product-package')) {
                    $result[] = $packageObject->name;
                } elseif ($productObject = get_post($itemId)) {
                    $result[] = $productObject->post_title;
                }
            }
            return implode(", ", $result);
        }

        return "";
    }
}


