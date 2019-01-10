<?php

namespace ModularityResourceBooking\Helper;

class Price
{
    /**
     * Get the price of a product (post) or package (taxonomy)
     *
     * @param array|int $item Object representing a taxonomy or
     *                        post that should be parsed for price data
     *
     * @return int $result Integer representing the product price
     */
    public static function getPrice($item)
    {

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
        } else {
            $fieldName = "product_price";
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
                    $productSumPrice = $productSumPrice + self::getPrice($subitem);
                }

                if (!empty($productSumPrice)) {
                    return $productSumPrice;
                }
            }
        }

        //No price found, return base price
        return $basePrice;
    }
}


