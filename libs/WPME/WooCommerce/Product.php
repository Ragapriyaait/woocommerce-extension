<?php
/**
 * This file is part of the WPMKTGENGINE plugin.
 *
 * Copyright 2016 Genoo, LLC. All rights reserved worldwide.  (web: http://www.wpmktgengine.com/)
 * GPL Version 2 Licensing:
 *  PHP code is licensed under the GNU General Public License Ver. 2 (GPL)
 *  Licensed "As-Is"; all warranties are disclaimed.
 *  HTML: http://www.gnu.org/copyleft/gpl.html
 *  Text: http://www.gnu.org/copyleft/gpl.txt
 *
 * Proprietary Licensing:
 *  Remaining code elements, including without limitation:
 *  images, cascading style sheets, and JavaScript elements
 *  are licensed under restricted license.
 *  http://www.wpmktgengine.com/terms-of-service
 *  Copyright 2016 Genoo LLC. All rights reserved worldwide.
 */

namespace WPME\WooCommerce;

/**
 * Class Product
 *
 * @package WPME\WooCommerce
 */
class Product
{
    /**
     * This was meant to be global, but is for WooCommerce
     *
     * @param \WP_Post $product
     *
     * @return array
     */
    public static function convertToProductArray(\WP_Post $product)
    {
        $link = get_permalink($product->ID);
        $productSingle = new \WC_Product_Simple($product->ID);
        $productCategories = $productSingle->get_categories();
        $productCategories = explode(', ', strip_tags($productCategories));
        $productCategories = is_array($productCategories) && !empty($productCategories) ? $productCategories : array();
        $productArray = array(
            'categories' => $productCategories,
            'id' => $product->ID,
            'name' => $product->post_title,
            'price' => $productSingle->get_price(),
            'sku' => $productSingle->get_sku(),
            'tags' => strip_tags($productSingle->get_tags()), // divided by comma
            'type' => $productSingle->get_type(),
            'url' => $link,
            'vendor' => '',
            'weight' => (int)$productSingle->get_weight(),
            'option1_name' => '',
            'option1_value' => '',
            'option2_name' => '',
            'option2_value' => '',
            'option3_name' => '',
            'option3_value' => '',
        );
        return $productArray;
    }

    /**
     * @param array $result
     *
     * @return array
     */
    public static function convertArrayToProductId($result = array())
    {
        $r[] = array();
        if(!empty($result) && is_array($result)){
            foreach($result as $updatedProduct){
                // Set product ID as product meta
                if($updatedProduct->result == 'success'){
                    // Add message
                    $r[$updatedProduct->external_product_id] = $updatedProduct->product_id;
                }
            }
        }
        return $r;
    }

    /**
     * @param $result
     */
    public static function setProductsIds($result)
    {
        $result = self::convertArrayToProductId($result);
        if(!empty($result) && is_array($result)){
            foreach($result as $id => $genoo){
                update_post_meta(
                    $id,
                    WPMKTENGINE_PRODUCT_KEY,
                    $genoo
                );
            }
        }
        return TRUE;
    }
}