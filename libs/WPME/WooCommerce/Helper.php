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
 * Class VariantCart
 *
 * @package WPME\WooCommerce
 */
class Helper
{
	/**
	 * Gets email from WooCommerce order
	 *
	 * @param $order_id
	 * @return bool
	 */
	public static function getEmailFromOrder($order_id)
	{
		if(class_exists('\WC_Order')){
			$order = new \WC_Order($order_id);
			if(method_exists($order, 'get_address')){
				$orderAddress = $order->get_address();
				return array_key_exists('email', $orderAddress) ? (!empty($orderAddress['email']) ? $orderAddress['email'] : FALSE) : FALSE;
			}
			return FALSE;
		}
		return FALSE;
	}
}
