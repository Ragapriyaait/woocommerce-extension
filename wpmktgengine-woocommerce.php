<?php

/*







    Plugin Name: WooCommerce - WPMktgEngine | Genoo Extension







    Description: Genoo, LLC







    Author:  Genoo, LLC







    Author URI: http://www.genoo.com/







    Author Email: info@genoo.com







    Version: 1.7.78







    License: GPLv2







    WC requires at least: 3.0.0







    WC tested up to: 5.2.3







*/

/*







    Copyright 2015  WPMKTENGINE, LLC  (web : http://www.genoo.com/)







    This program is free software; you can redistribute it and/or modify







    it under the terms of the GNU General Public License, version 2, as







    published by the Free Software Foundation.







    This program is distributed in the hope that it will be useful,







    but WITHOUT ANY WARRANTY; without even the implied warranty of







    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the







    GNU General Public License for more details.







    You should have received a copy of the GNU General Public License







    along with this program; if not, write to the Free Software







    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA







*/

/**







 * Definitions







 */

if (!defined('WPMKTENGINE_ORDER_KEY')) {
    define('WPMKTENGINE_ORDER_KEY', 'wpme_order_id');
}

if (!defined('WPMKTENGINE_PRODUCT_KEY')) {
    define('WPMKTENGINE_PRODUCT_KEY', 'wpme_product_id');
}

define('WPMKTENGINE_ECOMMERCE_FOLDER', plugins_url(null, __FILE__));

define('WPMKTENGINE_ECOMMERCE_REFRESH', md5('1.0-version'));

// define('WPMKTENGINE_ECOMMERCE_LOG', apply_filters('wpmktengine_dev', FALSE));

define('WPMKTENGINE_ECOMMERCE_LOG', true);

define('WPMKTENGINE_ECOMMERCE_LOG_FOLDER', __DIR__);

/**







 * Give us the API







 */

function wpme_on_wpme_api_set()
{
    global $WPME_API;

    if ($WPME_API) {
        return;
    }

    if (
        class_exists('\WPME\ApiFactory') &&
        class_exists('\WPME\RepositorySettingsFactory')
    ) {
        $repo = new \WPME\RepositorySettingsFactory();

        $api = new \WPME\ApiFactory($repo);
    } elseif (
        class_exists('\Genoo\Api') &&
        class_exists('\Genoo\RepositorySettings')
    ) {
        $repo = new \Genoo\RepositorySettings();

        $api = new \Genoo\Api($repo);
    } elseif (
        class_exists('\WPMKTENGINE\Api') &&
        class_exists('\WPMKTENGINE\RepositorySettings')
    ) {
        $repo = new \WPMKTENGINE\RepositorySettings();

        $api = new \WPMKTENGINE\Api($repo);
    }

    $WPME_API = $api;
}

/**







 * On activation







 */

register_activation_hook(__FILE__, function () {
    // Basic extension data

    $fileFolder = basename(dirname(__FILE__));

    $file = basename(__FILE__);

    $filePlugin = $fileFolder . DIRECTORY_SEPARATOR . $file;

    // Activate?

    $activate = false;

    $isGenoo = false;

    // Get api / repo

    if (
        class_exists('\WPME\ApiFactory') &&
        class_exists('\WPME\RepositorySettingsFactory')
    ) {
        $activate = true;

        $repo = new \WPME\RepositorySettingsFactory();

        $api = new \WPME\ApiFactory($repo);

        if (class_exists('\Genoo\Api')) {
            $isGenoo = true;
        }
    } elseif (
        class_exists('\Genoo\Api') &&
        class_exists('\Genoo\RepositorySettings')
    ) {
        $activate = true;

        $repo = new \Genoo\RepositorySettings();

        $api = new \Genoo\Api($repo);

        $isGenoo = true;
    }

    /*elseif (

        class_exists('\WPMKTENGINE\Api') &&

        class_exists('\WPMKTENGINE\RepositorySettings')

    ) { */

    /*   $activate = true;



    $repo = new \WPMKTENGINE\RepositorySettings();



    $api = new \WPMKTENGINE\Api($repo);

    //}



    // 1. First protectoin, no WPME or Genoo plugin

    /* if ($activate == false) {

        genoo_wpme_deactivate_plugin(

            $filePlugin,



            'This extension requires WPMktgEngine or Genoo plugin to work with.'

        );

    } else { */

    // Right on, let's run the tests etc.

    // 2. Second test, can we activate this extension?

    // Active

    //  $active = get_option('wpmktengine_extension_ecommerce', null);

    //$activeLeadType = false;

    /*      if ($isGenoo === true) {

            $active = true;

        }



        if (

            $active === null ||

            $active == false ||

            $active == '' ||

            is_string($active) ||

            $active == true

        ) {

            // Oh oh, no value, lets add one



            try {

                $ecoomerceActivate = $api->getPackageEcommerce();



                if ($ecoomerceActivate == true || $isGenoo) {

                    // Might be older package



                    $ch = curl_init();



                    if (defined('GENOO_DOMAIN')) {

                        curl_setopt(

                            $ch,



                            CURLOPT_URL,



                            'https:' .

                                GENOO_DOMAIN .

                                '/api/rest/ecommerceenable/true'

                        );

                    } else {

                        curl_setopt(

                            $ch,



                            CURLOPT_URL,



                            'https:' .

                                WPMKTENGINE_DOMAIN .

                                '/api/rest/ecommerceenable/true'

                        );

                    }



                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');



                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);



                    curl_setopt($ch, CURLOPT_HTTPHEADER, [

                        'X-API-KEY: ' . $api->key,

                    ]);



                    $resp = curl_exec($ch);



                    if (!$resp) {

                        $active = false;



                        $error = curl_error($ch);



                        $errorCode = curl_errno($ch);

                    } else {

                        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 202) {

                            // Active whowa whoooaa



                            $active = true;



                            // now, get the lead_type_id



                            $json = json_decode($resp);



                            if (

                                is_object($json) &&

                                isset($json->lead_type_id)

                            ) {

                                $activeLeadType = $json->lead_type_id;

                            }

                        }

                    }



                    curl_close($ch);

                }

            } catch (\Exception $e) {

                $active = false;

            }



            // Save new value



            update_option('wpmktengine_extension_ecommerce', $active, true);

        } */

    // 3. Check if we can activate the plugin after all

    /*if ($active == false) {

            genoo_wpme_deactivate_plugin(

                $filePlugin,



                'This extension is not allowed as part of your package.'

            );

        } else { */

    // 4. After all we can activate, that's great, lets add those calls

    try {
        $api->setStreamTypes([
            [
                'name' => 'viewed product123',

                'description' => '',
            ],

            [
                'name' => 'added product to cart',

                'description' => '',
            ],

            [
                'name' => 'order completed',

                'description' => '',
            ],

            [
                'name' => 'order canceledq',

                'description' => '',
            ],

            [
                'name' => 'cart emptied',

                'description' => '',
            ],

            [
                'name' => 'order refund full1',

                'description' => '',
            ],

            [
                'name' => 'order refund partial',

                'description' => '',
            ],

            [
                'name' => 'new cart',

                'description' => '',
            ],

            [
                'name' => 'new order',

                'description' => '',
            ],

            [
                'name' => 'order cancelled',

                'description' => '',
            ],

            [
                'name' => 'order refund full',

                'description' => '',
            ],

            [
                'name' => 'order refund partial',

                'description' => '',
            ],

            [
                'name' => 'upsell purchased',

                'description' => 'Upsell Purchased',
            ],

            [
                'name' => 'order payment declined',

                'description' => '',
            ],

            [
                'name' => 'completed order',

                'description' => '',
            ],

            [
                'name' => 'subscription started',

                'description' => '',
            ],

            [
                'name' => 'subscription payment',

                'description' => '',
            ],

            [
                'name' => 'subscription renewal',

                'description' => '',
            ],

            [
                'name' => 'subscription reactivated',

                'description' => '',
            ],

            [
                'name' => 'subscription payment declined',

                'description' => '',
            ],

            [
                'name' => 'subscription payment cancelled',

                'description' => '',
            ],

            [
                'name' => 'subscription expired',

                'description' => '',
            ],

            [
                'name' => 'sub renewal failed',

                'description' => '',
            ],

            [
                'name' => 'sub payment failed',

                'description' => '',
            ],

            [
                'name' => 'subscription on hold',

                'description' => '',
            ],

            [
                'name' => 'cancelled order',

                'description' => '',
            ],

            [
                'name' => 'subscription cancelled',

                'description' => '',
            ],

            [
                'name' => 'Subscription Pending Cancellation',

                'description' => '',
            ],

            [
                'name' => 'Subscription Testtt',

                'description' => '',
            ],
            [
                'name' => 'Subscription Test leads',

                'description' => '',
            ],
        ]);
    } catch (\Exception $e) {
        // Decide later Sub Renewal Failed
    }

    // Activate and save leadType, import products

    if ($activeLeadType == false || is_null($activeLeadType)) {
        // Leadtype not provided, or NULL, they have to set up for them selfes

        // Create a NAG for setting up the field

        // Shouldnt happen
    } else {
        // Set up lead type

        //  $option = get_option('WPME_ECOMMERCE', []);

        // Save option

        //     $option['genooLeadUsercustomer'] = $activeLeadType;

        //  update_option('WPME_ECOMMERCE', $option);
    }

    // Ok, let's see, do the products import, if it ran before, it won't run,

    // if it didn't ran, it will import the products. To achieve this, we save a value

    // that says we just activated this, and the init will check for it and run

    // the code to import.

    // add_option('WPME_WOOCOMMERCE_JUST_ACTIVATED', true);

    //}

    // }
});

/**
 * 
 * 







 * Plugin loaded







 */

add_action('upgrader_process_complete', 'init_plugin_update', 10, 2);
function init_plugin_update($upgrader_object, $options)
{
    error_log('test123');
}

include_once plugin_dir_path(__FILE__) . 'deploy/updater.php';

wpme_woo_com_forms_updater_init(__FILE__);

add_action(
    'wpmktengine_init',

    function ($repositarySettings, $api, $cache) {
        // Variant Cart

        require_once 'libs/WPME/WooCommerce/Product.php';

        require_once 'libs/WPME/WooCommerce/VariantCart.php';

        require_once 'libs/WPME/WooCommerce/Helper.php';

        /**







     * If Woocommerce exits







     */

        if (class_exists('woocommerce') || class_exists('Woocommerce')) {
            /**







         * Init redirect







         */

            add_action(
                'admin_init',

                function () {
                    if (get_option('WPME_WOOCOMMERCE_JUST_ACTIVATED', false)) {
                        delete_option('WPME_WOOCOMMERCE_JUST_ACTIVATED');

                        if (!isset($_GET['activate-multi'])) {
                            // Get if it's WPME or Genoo and find the link redirect

                            if (
                                class_exists('\Genoo\Api') &&
                                class_exists('\Genoo\RepositorySettings')
                            ) {
                                if (
                                    class_exists('\WPME\ApiFactory') &&
                                    class_exists(
                                        '\WPME\RepositorySettingsFactory'
                                    )
                                ) {
                                    \WPMKTENGINE\Wordpress\Redirect::code(
                                        302
                                    )->to(
                                        admin_url(
                                            'admin.php?page=GenooTools&run=WPME_WOOCOMMERCE_JUST_ACTIVATED'
                                        )
                                    );
                                } else {
                                    // depre

                                    \Genoo\Wordpress\Redirect::code(302)->to(
                                        admin_url(
                                            'admin.php?page=GenooTools&run=WPME_WOOCOMMERCE_JUST_ACTIVATED'
                                        )
                                    );
                                }
                            } elseif (
                                class_exists('\WPMKTENGINE\Api') &&
                                class_exists('\WPMKTENGINE\RepositorySettings')
                            ) {
                                \WPMKTENGINE\Wordpress\Redirect::code(302)->to(
                                    admin_url(
                                        'admin.php?page=WPMKTENGINETools&run=WPME_WOOCOMMERCE_JUST_ACTIVATED'
                                    )
                                );
                            }
                        }
                    }
                },

                10,

                1
            );

            /**







         * Add auto-import script







         */

            add_action(
                'admin_head',

                function () {
                    if (
                        isset($_GET) &&
                        is_array($_GET) &&
                        array_key_exists('run', $_GET) &&
                        $_GET['run'] == 'WPME_WOOCOMMERCE_JUST_ACTIVATED'
                    ) {
                        echo '<script type="text/javascript">jQuery(function(){ jQuery(".postboxwoocommerceproductsimport .button").click(); });</script>';
                    }
                },

                10,

                100
            );

            /**







         * Add extensions to the Extensions list







         */

            add_filter(
                'wpmktengine_tools_extensions_widget',

                function ($array) {
                    $array['WooCommerce'] =
                        '<span style="color:green">Active</span>';

                    return $array;
                },

                10,

                1
            );

            /**







         * Add settings page







         *  - if not already in







         */

            add_filter(
                'wpmktengine_settings_sections',

                function ($sections) {
                    if (is_array($sections) && !empty($sections)) {
                        $isEcommerce = false;

                        foreach ($sections as $section) {
                            if ($section['id'] == 'ECOMMERCE') {
                                $isEcommerce = true;

                                break;
                            }
                        }

                        if (!$isEcommerce) {
                            $sections[] = [
                                'id' => 'WPME_ECOMMERCE',

                                'title' => __('Ecommerce', 'wpmktengine'),
                            ];
                        }
                    }

                    return $sections;
                },

                10,

                1
            );

            /**







         * Add fields to settings page







         */

            add_filter(
                'wpmktengine_settings_fields',

                function ($fields) {
                    if (
                        is_array($fields) &&
                        array_key_exists('genooLeads', $fields) &&
                        is_array($fields['genooLeads'])
                    ) {
                        if (!empty($fields['genooLeads'])) {
                            $exists = false;

                            $rolesSave = false;

                            foreach ($fields['genooLeads'] as $key => $role) {
                                if (
                                    $role['type'] == 'select' &&
                                    $role['name'] == 'genooLeadUsercustomer'
                                ) {
                                    // Save

                                    $keyToRemove = $key;

                                    $field = $role;

                                    // Remove from array

                                    unset($fields['genooLeads'][$key]);

                                    // Add field

                                    $field['label'] =
                                        'Save ' . $role['label'] . ' lead as';

                                    $fields['WPME_ECOMMERCE'] = [$field];

                                    $exists = true;

                                    break;
                                }
                            }

                            if (
                                $exists === false &&
                                isset($fields['genooLeads'][1]['options'])
                            ) {
                                $fields['WPME_ECOMMERCE'] = [
                                    [
                                        'label' => 'Save customer lead as',

                                        'name' => 'genooLeadUsercustomer',

                                        'type' => 'select',

                                        'options' =>
                                            $fields['genooLeads'][1]['options'],
                                    ],
                                ];
                            }
                        }
                    }

                    return $fields;
                },

                909,

                1
            );

            /**







         * WooFunnel Upsell plugin







         */

            add_action(
                'wfocu_offer_accepted_and_processed',

                function ($get_offer_id, $get_package, $get_parent_order) use (
                    $api
                ) {
                    // Get order ID

                    $wpmeOrderId = (int) get_post_meta(
                        $get_parent_order->id,

                        WPMKTENGINE_ORDER_KEY,

                        true
                    );

                    if (!is_int($wpmeOrderId) && $wpmeOrderId < 1) {
                        // Don't bother

                        return;
                    }

                    // Ok, get original order and it's info

                    @$order = $get_parent_order;

                    $wpmeLeadEmail = $order->get_billing_email();

                    $wpmeOrderItems = $order->get_items();

                    $wpmeApiOrderItems = [];

                    // Prep array in place, let's iterate through that

                    if (
                        count($wpmeOrderItems) < 1 ||
                        count($get_package['products']) < 1
                    ) {
                        // Don't bother if this happens for some reason

                        return;
                    }

                    try {
                        // We're rolling, let's add those products to order again

                        // and create activity stream types for each upsell

                        foreach ($get_package['products'] as $packageProduct) {
                            $packageProductSingle = $packageProduct['data'];

                            $packageProductName = $packageProductSingle->get_name();

                            // Put it in

                            $api->putActivityByMail(
                                $wpmeLeadEmail,

                                'upsell purchased',

                                $packageProductName,

                                '',

                                ''
                            );
                        }

                        // Prep line items for order update, yay

                        foreach ($wpmeOrderItems as $wpmeOrderItem) {
                            // Changed item hey?

                            $changedItemData = $wpmeOrderItem->get_data();

                            // Let's see if this is in

                            $id = (int) get_post_meta(
                                $changedItemData['product_id'],

                                WPMKTENGINE_PRODUCT_KEY,

                                true
                            );

                            if (is_numeric($id) && $id > 0) {
                                $array['product_id'] = $id;

                                $array['quantity'] =
                                    $changedItemData['quantity'];

                                $array['total_price'] =
                                    $changedItemData['total'];

                                $array['unit_price'] =
                                    $changedItemData['total'] /
                                    $changedItemData['quantity'];

                                $array['external_product_id'] =
                                    $changedItemData['product_id'];

                                $array['name'] = $changedItemData['name'];

                                $wpmeApiOrderItems[] = $array;
                            }
                        }

                        // Cart Order, yay

                        $cartOrder = new \WPME\Ecommerce\CartOrder(
                            $wpmeOrderId
                        );

                        $cartOrder->setApi($api);

                        $cartOrder->total_price = $order->get_total();

                        $cartOrder->setTotal($order->get_total());

                        $cartOrder->tax_amount = $order->get_total_tax();

                        $cartOrder->changed->tax_amount = $order->get_total_tax();

                        $cartOrder->shipping_amount = $order->get_total_shipping();

                        $cartOrder->changed->shipping_amount = $order->get_total_shipping();

                        $cartOrder->addItemsArray($wpmeApiOrderItems);

                        wpme_get_order_stream_decipher($order, $cartOrder);

                        $cartOrder->updateOrder(true);
                    } catch (\Exception $e) {
                        //
                    }
                },

                10,

                3
            );

            add_action(
                'woocommerce_order_status_failed',

                function ($order_id, $ordersdetails) {
                    wpme_simple_log_2('WOSF-1 Payment failed.');

                    // Get API

                    global $WPME_API;

                    // Genoo order ID

                    if (function_exists('wcs_get_subscriptions_for_order')):
                        $subscriptions_ids = wcs_get_subscriptions_for_order(
                            $order_id,

                            [
                                'order_type' => 'any',
                            ]
                        );
                    endif;

                    $id = get_post_meta($order_id, WPMKTENGINE_ORDER_KEY, true);

                    $getrenewal = get_post_meta(
                        $order_id,

                        '_subscription_renewal',

                        true
                    );

                    if (isset($WPME_API) && !empty($id)) {
                        $order = new \WC_Order($order_id);

                        $cartOrder = new \WPME\Ecommerce\CartOrder($id);

                        $cartOrder->setApi($WPME_API);

                        // Total price

                        $cartOrder->total_price = $order->get_total();

                        $cartOrder->tax_amount = $order->get_total_tax();

                        $cartOrder->shipping_amount = $order->get_total_shipping();

                        // Completed?

                        $cartOrder->financial_status = 'declined';

                        $subscription_product_name = get_wpme_subscription_activity_name(
                            $order_id
                        );

                        $subscription_product_name_values = implode(
                            ',' . ' ',

                            $subscription_product_name
                        );

                        $genoo_lead_id = get_wpme_order_lead_id($id);

                        if (!empty($subscriptions_ids) && !$getrenewal):
                            $cartOrder->order_status = 'sub payment failed';

                            $cartOrder->changed->order_status =
                                'sub payment failed';

                            wpme_fire_activity_stream(
                                $genoo_lead_id,

                                'sub payment failed',

                                $subscription_product_name_values, // Title  $order->parent_id

                                $subscription_product_name_values, // Content

                                ' '

                                // Permalink
                            ); // Title  $order->parent_id // Content

                            // Permalink // Title  $order->parent_id // Content

                            // Permalink
                        elseif ($getrenewal):
                            $cartOrder->order_status = 'sub renewal failed';

                            $cartOrder->changed->order_status =
                                'sub renewal failed';

                            wpme_fire_activity_stream(
                                $genoo_lead_id,

                                'sub renewal failed',

                                $subscription_product_name_values,

                                $subscription_product_name_values,

                                ' '
                            );
                        else:
                            $cartOrder->order_status = 'payment failed';

                            $cartOrder->changed->order_status =
                                'payment failed';

                            wpme_fire_activity_stream(
                                $genoo_lead_id,

                                'order payment failed',

                                $subscription_product_name_values,

                                $subscription_product_name_values,

                                ' '
                            );
                        endif;

                        // From email

                        $cartOrderEmail = WPME\WooCommerce\Helper::getEmailFromOrder(
                            $order_id
                        );

                        if ($cartOrderEmail !== false) {
                            $cartOrder->email_ordered_from = $cartOrderEmail;

                            $cartOrder->changed->email_ordered_from = $cartOrderEmail;
                        }

                        $WPME_API->updateCart(
                            $cartOrder->id,

                            (array) $cartOrder->getPayload()
                        );

                        wpme_simple_log_2('WOSF-3A-2 Finished updating order.');

                        wpme_simple_log_2('WOSF-3A-3 Api response:');

                        wpme_simple_log_2($WPME_API->http->response['body']);
                    }

                    // Failed!
                },

                10,

                2
            );

            /**







         * Genoo Leads, recompile to add ecommerce







         */

            add_filter(
                'option_genooLeads',

                function ($array) {
                    if (!is_array($array)) {
                        $array = [];
                    }

                    // Lead type

                    $leadType = 0;

                    // Get saved

                    $leadTypeSaved = get_option('WPME_ECOMMERCE');

                    if (
                        is_array($leadTypeSaved) &&
                        array_key_exists(
                            'genooLeadUsercustomer',

                            $leadTypeSaved
                        )
                    ) {
                        $leadType = $leadTypeSaved['genooLeadUsercustomer'];
                    }

                    $array['genooLeadUsercustomer'] = $leadType;

                    return $array;
                },

                10,

                1
            );

            /**







         * Viewed Product







         * Viewed Lesson (name of Lesson - name of course)(works)







         */

            add_action(
                'wp',

                function () use ($api) {
                    // Get user

                    $user = wp_get_current_user();

                    if (
                        'product' === get_post_type() &&
                        is_singular() &&
                        is_object($user)
                    ) {
                        // Course

                        global $post;

                        wpme_simple_log_2(
                            'Viewed product by email: ' . $user->user_email
                        );

                        $api->putActivityByMail(
                            $user->user_email,

                            'viewed product',

                            '' . $post->post_title . '',

                            '',

                            get_permalink($post->ID)
                        );
                    }
                },

                10
            );

            /**







         * Started Cart







         * Updated Cart







         * - WACT







         */

            //add_action('woocommerce_cart_updated', function(){

            add_action(
                'woocommerce_after_calculate_totals',

                function () {
                    // Api

                    global $WPME_API;

                    // Continue?

                    wpme_simple_log_2('WACT-1 Updated cart start:');

                    if (isset($WPME_API->key) && \WPME\Helper::canContinue()) {
                        wpme_simple_log_2(
                            'WACT-1-1 Has API and lead cookie: ' .
                                (int) \WPME\Helper::loggedInOrCookie()
                        );

                        $session = WC()->session;

                        $cart = WC()->cart;

                        $cartOrder = new \WPME\Ecommerce\CartOrder();

                        $cartOrder->setApi($WPME_API);

                        $cartOrder->setUser(
                            (int) \WPME\Helper::loggedInOrCookie()
                        );

                        $cartContents = \WPME\WooCommerce\VariantCart::convertCartToObject(
                            $cart->cart_contents
                        );

                        $cartTotal = \WPME\WooCommerce\VariantCart::convertTotalFromContents(
                            $cartContents
                        );

                        $cartTotalFinal =
                            $cart->total == 0
                                ? $cartTotal + $cart->tax_total
                                : $cart->total + $cart->tax_total;

                        wpme_simple_log_2(
                            'WACT-1-2 Updating cart. User: ' .
                                (int) \WPME\Helper::loggedInOrCookie()
                        );

                        // Do we have a session?

                        if (isset($session->{WPMKTENGINE_ORDER_KEY})) {
                            if (!empty($cartContents)) {
                                wpme_simple_log_2(
                                    'WACT-1-2A-1 Updating existing cart for User: ' .
                                        (int) \WPME\Helper::loggedInOrCookie()
                                );

                                // Update order only it wasn't empited out.

                                // 21.03.2016 - Kim

                                $cartOrder->setId(
                                    $session->{WPMKTENGINE_ORDER_KEY}
                                );

                                $cartOrder->addItemsArray($cartContents);

                                $cartOrder->setTotal($cartTotalFinal);

                                $updated = $cartOrder->updateOrder();

                                wpme_simple_log_2(
                                    'WACT-1-2A-2 Updated cart ID: ' .
                                        $session->{WPMKTENGINE_ORDER_KEY}
                                );

                                wpme_simple_log_2(
                                    'WACT-1-2A-3 Updated response: ' .
                                        var_export($updated, true)
                                );

                                if ($updated) {
                                    // Updated
                                }
                            }
                        } else {
                            wpme_simple_log_2('WACT-1-2B-1 Starting new cart.');

                            // New cart creation on WPME

                            $cart = WC()->cart;

                            $cartOrder->setTotal($cartTotalFinal);

                            $cartOrder->startCart($cartContents);

                            // After setting a cart we get an order ID

                            $session->{WPMKTENGINE_ORDER_KEY} = $cartOrder->id;

                            $session->set(
                                WPMKTENGINE_ORDER_KEY,

                                $cartOrder->id
                            );

                            wpme_simple_log_2(
                                'WACT-1-2B-2 Started cart : ' . $cartOrder->id
                            );
                        }
                    }
                },

                100,

                1
            );

            /**







         * New customer







         * New lead







         * - WCC







         */

            add_action(
                'woocommerce_created_customer',

                function (
                    $customer_id,

                    $new_customer_data,

                    $password_generated
                ) {
                    // Check if lead eixsts, if not create a lead, add lead_id

                    // We have only email at this point`

                    $email = $new_customer_data['user_email'];

                    // Global api

                    global $WPME_API;

                    wpme_simple_log_2('WCC-1 Creating customer for: ' . $email);

                    wpme_simple_log_2(
                        'WCC-2 Creating customer info: ' .
                            print_r($new_customer_data, true)
                    );

                    if (isset($WPME_API)) {
                        try {
                            wpme_simple_log_2(
                                'WCC-2B-1 Lead not found by email.'
                            );

                            // NO lead, create one

                            //  $leadTypeFirst = wpme_get_customer_lead_type();

                            //$leadType = $WPME_API->settingsRepo->getLeadTypeSubscriber();

                            if (
                                $leadTypeFirst !== false &&
                                !is_null($leadTypeFirst) &&
                                is_numeric($leadTypeFirst)
                            ) {
                                $leadType = $leadTypeFirst;
                            }

                            // First & Last name

                            $lead_first = wpme_get_first_name_from_request();

                            $lead_last = wpme_get_last_name_from_request();

                            wpme_simple_log_2(
                                'WCC-2B-2 Getting First and Last name: ' .
                                    @$lead_first .
                                    ' ' .
                                    @$lead_last
                            );

                            wpme_simple_log_2('WCC-2B-3 Setting a lead.');

                            $atts = apply_filters(
                                'genoo_wpme_lead_creation_attributes',

                                [],

                                'ecommerce-register-new-customer-lead'
                            );

                            $leadNew = $WPME_API->setLead(
                                (int) $leadType,

                                $email,

                                $lead_first,

                                $lead_last,

                                null,

                                true,

                                $atts
                            );

                            wpme_clear_sess();

                            wpme_simple_log_2(
                                'WCC-2B.3B Creating Lead with these attributes: ' .
                                    print_r($atts, true)
                            );

                            wpme_simple_log_2(
                                'WCC-2B.4 Lead response: ' . $leadNew
                            );

                            $leadNew = (int) $leadNew;

                            if (!is_null($leadNew)) {
                                wpme_simple_log_2(
                                    'WCC-2B-4A-1 Lead created OK.'
                                );

                                wpme_simple_log_2(
                                    'WCC-2B-4A-2 Setting user meta & cookie.'
                                );

                                // We have a lead id

                                $lead_id = $leadNew;

                                // Set lead id for user meta

                                \add_user_meta(
                                    (int) $customer_id,

                                    WPMKTENGINE_LEAD_COOKIE,

                                    $lead_id
                                );

                                \update_user_meta(
                                    (int) $customer_id,

                                    WPMKTENGINE_LEAD_COOKIE,

                                    $lead_id
                                );

                                // Set cookie

                                \WPME\Helper::setUserCookie($lead_id);

                                wpme_simple_log_2(
                                    'WCC-2B-4A-3 New customer lead email: ' .
                                        $email
                                );

                                wpme_simple_log_2(
                                    'WCC-2B-4A-4 New customer lead ID: ' .
                                        $lead_id
                                );
                            } else {
                                wpme_simple_log_2(
                                    'WCC-2B-4B-1 Lead not created!'
                                );

                                wpme_simple_log_2('WCC-2B-4B-2 Api response:');

                                wpme_simple_log_2(
                                    $WPME_API->http->response['body']
                                );
                            }
                        } catch (\Exception $e) {
                            wpme_simple_log_2(
                                'WCC-2C-1 - Error while creating & getting a LEAD: ' .
                                    $e->getMessage()
                            );
                        }
                    }
                },

                10,

                3
            );

            /**







         * New order







         */

            add_action(
                'woocommerce_checkout_update_order_meta',

                function ($order_id, $data) {
                    wpme_simple_log_2(
                        'WCUOM-1 Updating order meta after checkout.'
                    );

                    // Global api

                    global $WPME_API;

                    // Let's do this

                    // It might actually never get here ...

                    if (
                        isset($WPME_API) &&
                        isset(WC()->session->{WPMKTENGINE_ORDER_KEY}) &&
                        \WPME\Helper::canContinue()
                    ) {
                        // Changed, always create new lead and new order

                        wpme_simple_log_2(
                            'WCUOM-2A-1 Order object exists (cart), getting ID.'
                        );

                        $order_genoo_id = WC()->session
                            ->{WPMKTENGINE_ORDER_KEY};

                        wpme_simple_log_2(
                            'WCUOM-2A-2 Order found, Genoo order id: ' .
                                $order_genoo_id
                        );

                        wpme_simple_log_2('WCUOM-2A-3 Updating order data.');

                        $order = new \WC_Order($order_id);

                        $cartAddress = $order->get_address('billing');

                        $cartAddress2 = $order->get_address('shipping');

                        $cartOrder = new \WPME\Ecommerce\CartOrder(
                            $order_genoo_id
                        );

                        $cartOrder->setApi($WPME_API);

                        //  $cartOrder->actionNewOrder();

                        $cartOrder->total_price = $order->get_total();

                        $cartOrder->setBillingAddress(
                            $cartAddress['address_1'],

                            $cartAddress['address_2'],

                            $cartAddress['city'],

                            $cartAddress['country'],

                            $cartAddress['phone'],

                            $cartAddress['postcode'],

                            '',

                            $cartAddress['state']
                        );

                        $cartOrder->setShippingAddress(
                            $cartAddress2['address_1'],

                            $cartAddress2['address_2'],

                            $cartAddress2['city'],

                            $cartAddress2['country'],

                            $cartAddress2['phone'],

                            $cartAddress2['postcode'],

                            '',

                            $cartAddress2['state']
                        );

                        $cartOrder->order_number = $order_id;

                        $cartOrder->currency = $order->get_order_currency();

                        $cartOrder->setTotal($order->get_total());

                        // Add email and leadType

                        //ec_lead_type_id = lead type ID

                        //email_ordered_from = email address making the sale

                        $leadTYpe = wpme_get_customer_lead_type();

                        $cartOrder->ec_lead_type_id = wpme_get_customer_lead_type();

                        $cartOrder->changed->ec_lead_type_id = $leadTYpe;

                        $cartOrder->email_ordered_from = $email;

                        $cartOrder->changed->email_ordered_from = $email;

                        $cartOrder->tax_amount = $order->get_total_tax();

                        $cartOrder->changed->tax_amount = $order->get_total_tax();

                        $cartOrder->shipping_amount = $order->get_total_shipping();

                        $cartOrder->changed->shipping_amount = $order->get_total_shipping();

                        // From email

                        $cartOrderEmail = \WPME\WooCommerce\Helper::getEmailFromOrder(
                            $order_id
                        );

                        if ($cartOrderEmail !== false) {
                            $cartOrder->email_ordered_from = $cartOrderEmail;

                            $cartOrder->changed->email_ordered_from = $cartOrderEmail;
                        }

                        wpme_get_order_stream_decipher($order, $cartOrder);

                        $cartOrder->updateOrder(true);

                        wpme_simple_log_2('WCUOM-2A-4 Order updated in APi.');

                        // Order meta

                        // Set order meta

                        \update_post_meta(
                            $order_id,

                            WPMKTENGINE_ORDER_KEY,

                            $order_genoo_id
                        );

                        // Remove session id

                        wpme_simple_log_2(
                            'WCUOM-2A-5 Finished ORDER from CART, Genoo ID:' .
                                WC()->session->{WPMKTENGINE_ORDER_KEY}
                        );

                        wpme_simple_log_2(
                            'WCUOM-2A-6 Finished ORDER from CART, WooCommerce ID:' .
                                $order_id
                        );

                        // Remove session

                        unset(WC()->session->{WPMKTENGINE_ORDER_KEY});
                    } elseif (isset($WPME_API)) {
                        wpme_simple_log_2('WCUOM-2B-1 New order from cart.');

                        // At this point, we need to start a cart, change it to new order, add everything.

                        // and firstly, creat a lead.

                        // 1. Create a lead get if exists

                        // Do we have an email?

                        $email =
                            isset($_POST) &&
                            is_array($_POST) &&
                            array_key_exists('billing_email', $_POST) &&
                            !empty($_POST['billing_email']) &&
                            filter_var(
                                $_POST['billing_email'],

                                FILTER_VALIDATE_EMAIL
                            ) !== false
                                ? $_POST['billing_email']
                                : false;

                        wpme_simple_log_2(
                            'WCUOM-2B-2 New ORDER, creating LEAD for email :' .
                                $email
                        );

                        if ($email !== false) {
                            wpme_simple_log_2(
                                'WCUOM-2B-2A-1 Email exists, getting session data and lead info.'
                            );

                            // Get order & adresses

                            $session = WC()->session;

                            @$order = new \WC_Order($order_id);

                            $cartAddress = $order->get_address('billing');

                            $cartAddress2 = $order->get_address('shipping');

                            @$lead_first = isset($data['billing_first_name'])
                                ? $data['billing_first_name']
                                : null;

                            @$lead_last = isset($data['billing_last_name'])
                                ? $data['billing_last_name']
                                : null;

                            if (empty($lead_first) && empty($lead_last)) {
                                // If both are empty, try from order?

                                @$lead_first = $cartAddress['first_name'];

                                @$lead_last = $cartAddress['last_name'];

                                // If still empty try shipping name?

                                if (empty($lead_first) && empty($lead_last)) {
                                    // If both are empty

                                    @$lead_first = $cartAddress2['first_name'];

                                    @$lead_last = $cartAddress2['last_name'];
                                }

                                if (empty($lead_first) && empty($lead_last)) {
                                    // If both are empty

                                    @$lead_first = isset(
                                        $data['shipping_first_name']
                                    )
                                        ? $data['shipping_first_name']
                                        : null;

                                    @$lead_last = isset(
                                        $data['shipping_last_name']
                                    )
                                        ? $data['shipping_last_name']
                                        : null;
                                }

                                if (empty($lead_first) && empty($lead_last)) {
                                    // If both are empty

                                    @$lead_first = wpme_get_first_name_from_request();

                                    @$lead_last = wpme_get_last_name_from_request();
                                }
                            }

                            wpme_simple_log_2(
                                'WCUOM-2B-2A-2 Tried to get first and last name:' .
                                    $lead_first .
                                    ' ' .
                                    $lead_last
                            );

                            wpme_simple_log_2(
                                'WCUOM-2B-2A-3 Lead info to be created: ' .
                                    print_r(
                                        [
                                            $lead_first,

                                            $lead_last,

                                            $cartAddress,

                                            $cartAddress2,
                                        ],

                                        true
                                    )
                            );

                            // Lead null for now

                            $lead_id = null;

                            try {
                                wpme_simple_log_2(
                                    'WCUOM-2B-2A-3A-1 Trying to get lead by email.'
                                );

                                // Lead exists, ok, set up Lead ID

                                // NO lead, create one

                                $leadTypeFirst = wpme_get_customer_lead_type();

                                wpme_simple_log_2(
                                    'WCUOM-2B-2A-3A-1B-2 Creating one, leadtype: ' .
                                        $leadTypeFirst
                                );

                                $leadType = $WPME_API->settingsRepo->getLeadTypeSubscriber();

                                if (
                                    $leadTypeFirst !== false &&
                                    !is_null($leadTypeFirst) &&
                                    is_numeric($leadTypeFirst)
                                ) {
                                    $leadType = $leadTypeFirst;
                                }

                                $attributes = apply_filters(
                                    'genoo_wpme_lead_creation_attributes',

                                    [
                                        'organization' => '',

                                        'address1' => $cartAddress['address_1'],

                                        'address2' => $cartAddress['address_2'],

                                        'city' => $cartAddress['city'],

                                        'country' => $cartAddress['country'],

                                        'zip' => $cartAddress['postcode'],

                                        'mobilephone' => $cartAddress['phone'],

                                        'source' => 'eCommerce Order',
                                    ],

                                    'ecommerce-new-order-lead'
                                );

                                wpme_clear_sess();

                                wpme_simple_log_2(
                                    'WCUOM-2B-2A-3A-1B-2B Lead Attributes after filter: ' .
                                        print_r($attributes, true)
                                );

                                $leadNew = $WPME_API->setLead(
                                    (int) $leadType,

                                    $email,

                                    $lead_first,

                                    $lead_last,

                                    '',

                                    true,

                                    $attributes
                                );

                                wpme_simple_log_2(
                                    'WCUOM-2B-2A-3A-1B-3 New Lead: ' . $leadNew
                                );

                                $leadNew = (int) $leadNew;

                                if (
                                    function_exists('clearRefferalFromSession')
                                ) {
                                    clearRefferalFromSession();
                                }

                                if (!is_null($leadNew) && $leadNew > 0) {
                                    // We have a lead id

                                    $lead_id = $leadNew;

                                    // Set cookie

                                    \WPME\Helper::setUserCookie($lead_id);

                                    wpme_simple_log_2(
                                        'WCUOM-2B-2A-3A-1B-3A-1 Created NEW LEAD for EMAIL :' .
                                            $email .
                                            ' : LEAD ID ' .
                                            $lead_id
                                    );
                                } else {
                                    wpme_simple_log_2(
                                        'WCUOM-2B-2A-3A-1B-3B-1 Lead not created!'
                                    );

                                    wpme_simple_log_2(
                                        'WCUOM-2B-2A-3A-1B-3A-1 response:'
                                    );

                                    wpme_simple_log_2(
                                        $WPME_API->http->response['body']
                                    );
                                }
                            } catch (\Exception $e) {
                                wpme_simple_log_2(
                                    'WCUOM-2B-2A-3B-1 Error GETTING or CREATING lead by EMAIL :' .
                                        $email .
                                        ' : ' .
                                        $e->getMessage()
                                );
                            }

                            // 2 Start and order if lead not null

                            // 2.1 Set to new order

                            if ($lead_id !== null && $lead_id > 0) {
                                wpme_simple_log_2(
                                    'WCUOM-2B-2A-4-1 Lead exists, creating order. lead id: ' .
                                        $lead_id
                                );

                                $cart = WC()->cart;

                                $cartContents = \WPME\WooCommerce\VariantCart::convertCartToObject(
                                    $cart->cart_contents
                                );

                                $cartOrder = new \WPME\Ecommerce\CartOrder();

                                $cartOrder->setApi($WPME_API);

                                $cartOrder->setUser($lead_id);

                                // $cartOrder->actionNewOrder();

                                $cartOrder->setBillingAddress(
                                    $cartAddress['address_1'],

                                    $cartAddress['address_2'],

                                    $cartAddress['city'],

                                    $cartAddress['country'],

                                    $cartAddress['phone'],

                                    $cartAddress['postcode'],

                                    '',

                                    $cartAddress['state']
                                );

                                $cartOrder->setShippingAddress(
                                    $cartAddress2['address_1'],

                                    $cartAddress2['address_2'],

                                    $cartAddress2['city'],

                                    $cartAddress2['country'],

                                    $cartAddress2['phone'],

                                    $cartAddress2['postcode'],

                                    '',

                                    $cartAddress2['state']
                                );

                                $cartOrder->order_number = $order_id;

                                $cartOrder->currency = $order->get_order_currency();

                                $cartOrder->total_price = $order->get_total();

                                $cartOrder->setTotal($order->get_total());

                                $cartOrder->addItemsArray($cartContents);

                                // Add email and leadType

                                //ec_lead_type_id = lead type ID

                                //email_ordered_from = email address making the sale

                                $leadTYpe = wpme_get_customer_lead_type();

                                $cartOrder->ec_lead_type_id = wpme_get_customer_lead_type();

                                $cartOrder->changed->ec_lead_type_id = $leadTYpe;

                                $cartOrder->email_ordered_from = $email;

                                $cartOrder->changed->email_ordered_from = $email;

                                $cartOrder->total_price = $order->get_total();

                                $cartOrder->tax_amount = $order->get_total_tax();

                                $cartOrder->changed->tax_amount = $order->get_total_tax();

                                $cartOrder->shipping_amount = $order->get_total_shipping();

                                $cartOrder->changed->shipping_amount = $order->get_total_shipping();

                                // From email

                                $cartOrderEmail = \WPME\WooCommerce\Helper::getEmailFromOrder(
                                    $order_id
                                );

                                if ($cartOrderEmail !== false) {
                                    $cartOrder->email_ordered_from = $cartOrderEmail;

                                    $cartOrder->changed->email_ordered_from = $cartOrderEmail;
                                }

                                wpme_get_order_stream_decipher(
                                    $order,

                                    $cartOrder
                                );

                                // Continue

                                $cartOrder->startNewOrder();

                                // Set order meta

                                \update_post_meta(
                                    $order_id,

                                    WPMKTENGINE_ORDER_KEY,

                                    $cartOrder->id
                                );

                                // Remove session id

                                unset(WC()->session->{WPMKTENGINE_ORDER_KEY});

                                // Log

                                wpme_simple_log_2(
                                    'WCUOM-2B-2A-4-2 Finished ORDER, Genoo ID:' .
                                        $cartOrder->id
                                );

                                wpme_simple_log_2(
                                    'WCUOM-2B-2A-4-3 Finished ORDER, WooCommerce ID:' .
                                        $order_id
                                );
                            } else {
                                wpme_simple_log_2(
                                    'WCUOM-2B-2A-5-1 After all attempts no lead created.'
                                );

                                wpme_simple_log_2(
                                    'WCUOM-2B-2A-5-2 Last API response: ' .
                                        print_r($WPME_API->http, true)
                                );
                            }
                        } else {
                            wpme_simple_log_2(
                                'WCUOM-2B-2B-1 No email for order, can\'t continue'
                            );
                        }
                    }
                },

                100,

                2
            );

            /**







         * Order furfilled







         */

            add_action('woocommerce_payment_complete', function ($order_id) {
                wpme_simple_log_2('WPC-1 Payment complete.');

                global $WPME_API;

                // Genoo order ID

                if (function_exists('wcs_get_subscriptions_for_order')):
                    $subscriptions_ids = wcs_get_subscriptions_for_order(
                        $order_id,

                        [
                            'order_type' => 'any',
                        ]
                    );
                endif;

                $getrenewal = get_post_meta(
                    $order_id,

                    '_subscription_renewal',

                    true
                );

                if (!empty($subscriptions_ids) && $getrenewal):
                    foreach ($subscriptions_ids as $subscriptions_id):
                        $manual = get_post_meta(
                            $subscriptions_id->id,

                            '_requires_manual_renewal',

                            true
                        );

                        if ($getrenewal && $manual == 'false'):
                            $get_order = wc_get_order($subscriptions_id->id);

                            foreach ($get_order->get_items() as $item) {
                                $changedItemData = $item->get_data();

                                // Let's see if this is in

                                $id = (int) get_post_meta(
                                    $changedItemData['product_id'],

                                    WPMKTENGINE_PRODUCT_KEY,

                                    true
                                );

                                if (is_numeric($id) && $id > 0) {
                                    $array['product_id'] = $id;

                                    $array['quantity'] =
                                        $changedItemData['quantity'];

                                    $array['total_price'] =
                                        $changedItemData['total'];

                                    $array['unit_price'] =
                                        $changedItemData['total'] /
                                        $changedItemData['quantity'];

                                    $array['external_product_id'] =
                                        $changedItemData['product_id'];

                                    $array['name'] = $changedItemData['name'];

                                    $wpmeApiOrderItems[] = $array;
                                }
                            }

                            $id = get_post_meta(
                                $order_id,

                                WPMKTENGINE_ORDER_KEY,

                                true
                            );

                            wpme_simple_log_2(
                                'WPC-2 Payment Complete for order: ' . $order_id
                            );

                            wpme_simple_log_2(
                                'WPC-3 Woocommerce order: ' . $id
                            );

                            if (isset($WPME_API) && !empty($id)) {
                                wpme_simple_log_2(
                                    'WPC-3A-1 Order found, changing status.'
                                );

                                $order_genoo_id = $id;

                                $cartOrder = new \WPME\Ecommerce\CartOrder(
                                    $order_genoo_id
                                );

                                $cartOrder->setApi($WPME_API);

                                $order = new \WC_Order($order_id);

                                $cartOrder = new \WPME\Ecommerce\CartOrder();

                                $cartOrder->setApi($WPME_API);

                                $cartOrder->total_price = $order->get_total();

                                $cartOrder->setUser($lead_id);

                                $cartOrder->actionNewOrder();

                                $cartOrder->total_price = $order->get_total();

                                $cartAddress = $order->get_address('billing');

                                $cartAddress2 = $order->get_address('shipping');

                                $cartOrder = new \WPME\Ecommerce\CartOrder(
                                    $order_genoo_id
                                );

                                $cartOrder->setApi($WPME_API);

                                // $cartOrder->actionNewOrder();

                                $cartOrder->setBillingAddress(
                                    $cartAddress['address_1'],

                                    $cartAddress['address_2'],

                                    $cartAddress['city'],

                                    $cartAddress['country'],

                                    $cartAddress['phone'],

                                    $cartAddress['postcode'],

                                    '',

                                    $cartAddress['state']
                                );

                                $cartOrder->setShippingAddress(
                                    $cartAddress2['address_1'],

                                    $cartAddress2['address_2'],

                                    $cartAddress2['city'],

                                    $cartAddress2['country'],

                                    $cartAddress2['phone'],

                                    $cartAddress2['postcode'],

                                    '',

                                    $cartAddress2['state']
                                );

                                $cartOrder->order_number = $order_id;

                                $cartOrder->currency = $order->get_order_currency();

                                $cartOrder->setTotal($order->get_total());

                                $cartOrder->addItemsArray($wpmeApiOrderItems);

                                // Add email and leadType

                                //ec_lead_type_id = lead type ID

                                //email_ordered_from = email address making the sale

                                $leadTYpe = wpme_get_customer_lead_type();

                                $cartOrder->ec_lead_type_id = $leadTYpe;

                                $cartOrder->changed->ec_lead_type_id = $leadTYpe;

                                $cartOrder->email_ordered_from = $email;

                                $cartOrder->changed->email_ordered_from = $email;

                                $cartOrder->total_price = $order->get_total();

                                $cartOrder->tax_amount = $order->get_total_tax();

                                $cartOrder->changed->tax_amount = $order->get_total_tax();

                                $cartOrder->shipping_amount = $order->get_total_shipping();

                                $cartOrder->changed->shipping_amount = $order->get_total_shipping();

                                // From email

                                $cartOrderEmail = \WPME\WooCommerce\Helper::getEmailFromOrder(
                                    $order_id
                                );

                                if ($cartOrderEmail !== false) {
                                    $cartOrder->email_ordered_from = $cartOrderEmail;

                                    $cartOrder->changed->email_ordered_from = $cartOrderEmail;
                                }

                                // wpme_get_order_stream_decipher($order, $cartOrder);

                                // Continue

                                $cartOrder->startNewOrder();

                                // Set order meta

                                \update_post_meta(
                                    $order_id,

                                    WPMKTENGINE_ORDER_KEY,

                                    $cartOrder->id
                                );

                                try {
                                    //  wpme_get_order_stream_decipher($order, $cartOrder);

                                    $cartOrder->order_status = 'subrenewal';

                                    $cartOrder->changed->order_status =
                                        'subrenewal';

                                    $cartOrder->financial_status = 'paid';

                                    $WPME_API->updateCart(
                                        $cartOrder->id,

                                        (array) $cartOrder->getPayload()
                                    );

                                    wpme_simple_log_2(
                                        'UPDATED ORDER to PROCESSING :' .
                                            $cartOrder->id .
                                            ' : WOO ID : ' .
                                            $order_id
                                    );
                                } catch (\Exception $e) {
                                    wpme_simple_log_2(
                                        'Processing ORDER, Genoo ID:' .
                                            $cartOrder->id
                                    );

                                    wpme_simple_log_2(
                                        'FAILED to updated order to PROCESSING :' .
                                            $id .
                                            ' : WOO ID : ' .
                                            $order_id .
                                            ' : Because : ' .
                                            $e->getMessage()
                                    );
                                }
                            }
                        endif;
                    endforeach; // Payed!
                endif;
            });

            /**







         * Order Failed







         */

            add_action(
                'woocommerce_order_status_pending',

                function ($order_id) {
                    // Get API

                    global $WPME_API;

                    // Genoo order ID

                    $id = get_post_meta($order_id, WPMKTENGINE_ORDER_KEY, true);

                    if (isset($WPME_API) && !empty($id)) {
                        $order = new \WC_Order($order_id);

                        $cartOrder = new \WPME\Ecommerce\CartOrder($id);

                        $cartOrder->setApi($WPME_API);

                        // Total price

                        $cartOrder->total_price = $order->get_total();

                        $cartOrder->tax_amount = $order->get_total_tax();

                        $cartOrder->shipping_amount = $order->get_total_shipping();

                        // Completed?

                        $cartOrder->order_status = 'order';

                        $cartOrder->changed->order_status = 'order';

                        // From email

                        $cartOrderEmail = WPME\WooCommerce\Helper::getEmailFromOrder(
                            $order_id
                        );

                        if ($cartOrderEmail !== false) {
                            $cartOrder->email_ordered_from = $cartOrderEmail;

                            $cartOrder->changed->email_ordered_from = $cartOrderEmail;
                        }

                        try {
                            wpme_get_order_stream_decipher($order, $cartOrder);

                            $WPME_API->updateCart(
                                $cartOrder->id,

                                (array) $cartOrder->getPayload()
                            );

                            wpme_simple_log_2(
                                'UPDATED ORDER to PROCESSING :' .
                                    $cartOrder->id .
                                    ' : WOO ID : ' .
                                    $order_id
                            );
                        } catch (\Exception $e) {
                            wpme_simple_log_2(
                                'Processing ORDER, Genoo ID:' . $cartOrder->id
                            );

                            wpme_simple_log_2(
                                'FAILED to updated order to PROCESSING :' .
                                    $id .
                                    ' : WOO ID : ' .
                                    $order_id .
                                    ' : Because : ' .
                                    $e->getMessage()
                            );
                        }
                    }
                },

                10,

                1
            );

            /**







         * Order Refunded







         */

            add_action(
                'woocommerce_order_status_refunded',

                function ($order_id) {
                    // Get API

                    global $WPME_API;

                    // Genoo order ID

                    $id = get_post_meta($order_id, WPMKTENGINE_ORDER_KEY, true);

                    if (isset($WPME_API) && !empty($id)) {
                        $order = new \WC_Order($order_id);

                        $cartOrder = new \WPME\Ecommerce\CartOrder($id);

                        $cartOrder->setApi($WPME_API);

                        // Total price

                        $cartOrder->financial_status = 'refunded';

                        // Refunded?

                        $cartOrder->order_status = 'refunded';

                        $cartOrder->changed->order_status = 'refunded';

                        $subscription_product_name = get_wpme_subscription_activity_name(
                            $order_id
                        );

                        $subscription_product_name_values = implode(
                            ',' . ' ',

                            $subscription_product_name
                        );

                        $genoo_lead_id = get_wpme_order_lead_id($id);

                        wpme_fire_activity_stream(
                            $genoo_lead_id,

                            'order refund full',

                            $subscription_product_name_values, // Title  $order->parent_id

                            $subscription_product_name_values, // Content

                            ' '

                            // Permalink
                        );

                        // Completed?

                        $cartOrder->refund_date = \WPME\Ecommerce\Utils::getDateTime();

                        $cartOrder->refund_amount = $order->get_total_refunded();

                        try {
                            $WPME_API->updateCart(
                                $cartOrder->id,

                                (array) $cartOrder->getPayload()
                            );

                            wpme_simple_log_2(
                                'UPDATED ORDER to REFUNDED :' .
                                    $cartOrder->id .
                                    ' : WOO ID : ' .
                                    $order_id
                            );
                        } catch (\Exception $e) {
                            wpme_simple_log_2(
                                'Refunding ORDER, Genoo ID:' . $cartOrder->id
                            );

                            wpme_simple_log_2(
                                'FAILED to update order to REFUNDED :' .
                                    $id .
                                    ' : WOO ID : ' .
                                    $order_id .
                                    ' : Because : ' .
                                    $e->getMessage()
                            );
                        }
                    }
                },

                10,

                1
            );

            /**







         * New product







         * Update product







         */

            add_action(
                'save_post',

                function ($post_id, $post, $update) {
                    // If this isn't product, do nothing

                    if ('product' != $post->post_type) {
                        return;
                    }

                    // Revisons are nono

                    if (wp_is_post_revision($post_id)) {
                        return;
                    }

                    // Get API

                    global $WPME_API;

                    if (isset($WPME_API)) {
                        // Do we have product ID already?

                        $meta = get_post_meta(
                            $post_id,

                            WPMKTENGINE_PRODUCT_KEY,

                            true
                        );

                        $data = \WPME\WooCommerce\Product::convertToProductArray(
                            $post
                        );

                        if (!empty($meta)) {
                            try {
                                // Product exists in api, update

                                $result = $WPME_API->updateProduct(
                                    (int) $meta,

                                    $data
                                );

                                wpme_simple_log_2(
                                    'UPDATING PRODUCT, Genoo ID:' . (int) $meta
                                );
                            } catch (\Exception $e) {
                                wpme_simple_log_2(
                                    'FAILED UPDATING PRODUCT, Genoo ID:' .
                                        (int) $meta .
                                        ' : ' .
                                        $e->getMessage()
                                );
                            }
                        } else {
                            try {
                                $result = $WPME_API->setProduct($data);

                                $result = \WPME\WooCommerce\Product::setProductsIds(
                                    $result
                                );

                                wpme_simple_log_2(
                                    'CREATING PRODUCT, Genoo ID:' . (int) $meta
                                );
                            } catch (\Exception $e) {
                                wpme_simple_log_2(
                                    'FAILED CREATING PRODUCT, Genoo ID:' .
                                        (int) $meta .
                                        ' : ' .
                                        $e->getMessage()
                                );
                            }
                        }
                    }
                },

                10,

                3
            );

            /**







         * Save Order







         */

            add_action(
                'save_post',

                function ($post_id, $post, $update) {
                    global $WPME_API;

                    // If this isn't product, do nothing

                    if ('shop_order' != $post->post_type) {
                        return;
                    }

                    // Revisons are nono

                    if (wp_is_post_revision($post_id)) {
                        return;
                    }

                    // Get API

                    if (isset($WPME_API)) {
                        // Do we have product ID already?

                        $meta = get_post_meta(
                            $post_id,

                            WPMKTENGINE_ORDER_KEY,

                            true
                        );

                        if (empty($meta)) {
                            // Order has not yet been saved into API and that's a shame!

                            // let's create it
                        }
                    }
                },

                10,

                3
            );

            /**







         * Partial Refund







         */

            add_action(
                'woocommerce_order_partially_refunded',

                function ($order_id, $refund_id) {
                    // Get API

                    global $WPME_API;

                    // Genoo order ID

                    $id = get_post_meta($order_id, WPMKTENGINE_ORDER_KEY, true);

                    if (isset($WPME_API) && !empty($id)) {
                        $order = new \WC_Order($order_id);

                        $cartOrder = new \WPME\Ecommerce\CartOrder($id);

                        $cartOrder->setApi($WPME_API);

                        // Total price

                        // Refunded?

                        $cartOrder->financial_status = 'paid';

                        $cartOrder->order_status = 'refund partial';

                        $cartOrder->changed->order_status = 'refund partial';

                        $subscription_product_name = get_wpme_subscription_activity_name(
                            $order_id
                        );

                        $subscription_product_name_values = implode(
                            ',' . ' ',

                            $subscription_product_name
                        );

                        $genoo_lead_id = get_wpme_order_lead_id($id);

                        wpme_fire_activity_stream(
                            $genoo_lead_id,

                            'order refund partial',

                            $subscription_product_name_values, // Title  $order->parent_id

                            $subscription_product_name_values, // Content

                            ' '

                            // Permalink
                        );

                        // Completed?

                        $cartOrder->refund_date = \WPME\Ecommerce\Utils::getDateTime();

                        $cartOrder->refund_amount = $order->get_total_refunded();

                        try {
                            $WPME_API->updateCart(
                                $cartOrder->id,

                                (array) $cartOrder->getPayload()
                            );

                            wpme_simple_log_2(
                                'UPDATED ORDER to REFUNDED :' .
                                    $cartOrder->id .
                                    ' : WOO ID : ' .
                                    $order_id
                            );
                        } catch (\Exception $e) {
                            wpme_simple_log_2(
                                'Refunding ORDER, Genoo ID:' . $cartOrder->id
                            );

                            wpme_simple_log_2(
                                'FAILED to update order to REFUNDED :' .
                                    $id .
                                    ' : WOO ID : ' .
                                    $order_id .
                                    ' : Because : ' .
                                    $e->getMessage()
                            );
                        }
                    }
                },

                10,

                2
            );

            /**







         * Order cancelled







         */

            add_action(
                'woocommerce_order_status_cancelled',

                function ($order_id) {
                    // Get API

                    global $WPME_API;

                    // Genoo order ID

                    $id = get_post_meta($order_id, WPMKTENGINE_ORDER_KEY, true);

                    if (isset($WPME_API) && !empty($id)) {
                        $order = new \WC_Order($order_id);

                        $cartOrder = new \WPME\Ecommerce\CartOrder($id);

                        $cartOrder->setApi($WPME_API);

                        // Total price

                        $cartOrder->financial_status = '';

                        // Refunded?

                        $cartOrder->order_status = 'Order Cancelled';

                        $cartOrder->changed->order_status = 'Order Cancelled';

                        // Completed?

                        $cartOrder->refund_date = \WPME\Ecommerce\Utils::getDateTime();

                        $cartOrder->refund_amount = $order->get_total_refunded();

                        try {
                            wpme_get_order_stream_decipher($order, $cartOrder);

                            $WPME_API->updateCart(
                                $cartOrder->id,

                                (array) $cartOrder->getPayload()
                            );

                            $genoo_lead_id = get_wpme_order_lead_id($id);

                            $subscription_product_name = get_wpme_subscription_activity_name(
                                $order_id
                            );

                            $subscription_product_name_values = implode(
                                ',' . ' ',

                                $subscription_product_name
                            );

                            wpme_fire_activity_stream(
                                $genoo_lead_id,

                                'cancelled order',

                                $subscription_product_name_values, // Title  $order->parent_id

                                $subscription_product_name_values, // Content

                                ' '

                                // Permalink
                            );

                            wpme_simple_log_2(
                                'UPDATED ORDER to REFUNDED :' .
                                    $cartOrder->id .
                                    ' : WOO ID : ' .
                                    $order_id
                            );
                        } catch (\Exception $e) {
                            wpme_simple_log_2(
                                'Refunding ORDER, Genoo ID:' . $cartOrder->id
                            );

                            wpme_simple_log_2(
                                'FAILED to update order to REFUNDED :' .
                                    $id .
                                    ' : WOO ID : ' .
                                    $order_id .
                                    ' : Because : ' .
                                    $e->getMessage()
                            );
                        }
                    }
                },

                10,

                1
            );

            // Not used yet

            add_action('delete_post', function ($post_id) {}, 10, 1);

            add_action('woocommerce_check_new_order_items', function () {});

            add_action('woocommerce_resume_order', function () {});

            add_action('woocommerce_checkout_order_review', function () {});

            add_action('woocommerce_cart_has_errors', function () {});

            add_action('woocommerce_checkout_billing', function () {});

            add_action('woocommerce_checkout_shipping', function () {});

            add_action('woocommerce_checkout_order_review', function () {});

            add_action(
                'woocommerce_cart_contents_review_order',

                function () {}
            );

            add_action('woocommerce_thankyou', function () {});

            add_action('woocommerce_cart_contents', function () {});

            add_action('woocommerce_cart_emptied', function () {}, 10, 1);

            add_action('woocommerce_checkout_update_user_meta', function () {});

            add_action(
                'woocommerce_checkout_update_order_review',

                function ($post_data) {},

                10,

                1
            );

            add_action(
                'woocommerce_customer_save_address',

                function ($user_id, $load_address) {},

                10,

                2
            );

            /**







         * Block duplicate ID







         */

            add_filter(
                'woocommerce_duplicate_product_exclude_meta',

                function ($meta) {
                    $meta[] = 'wpme_product_id';

                    return $meta;
                },

                100,

                1
            );

            /**







         * Add widgets to Tools Page







         */

            add_filter(
                'wpmktengine_tools_widgets',

                function ($page) {
                    $pageImport =
                        '<p>' .
                        __(
                            'Note: Import all your products into your account.',

                            'wpmktengine'
                        ) .
                        '</p>';

                    $pageImport .=
                        '<p><a onclick="Genoo.startProducstImport(event)" class="button button-primary">Import Products</a><p>';

                    $page->widgets = array_merge(
                        [
                            (object) [
                                'title' => 'WooCommerce Products Import',

                                'guts' => $pageImport,
                            ],
                        ],

                        $page->widgets
                    );

                    return $page;
                },

                10,

                1
            );

            /**







         * Add JS







         */

            add_action(
                'admin_enqueue_scripts',

                function () {
                    wp_enqueue_script(
                        'wpmktgengine-woocommerce',

                        WPMKTENGINE_ECOMMERCE_FOLDER .
                            '/wpmktgengine-woocommerce.js',

                        ['Genoo'],

                        WPMKTENGINE_ECOMMERCE_REFRESH
                    );
                },

                10,

                1
            );

            /**







         * Genoo Log







         */

            add_action(
                'admin_head',

                function () {
                    echo '<style> body #genooLog { width: 90%; clear: both; margin-left: 7.5px; display: block; }</style>';
                },

                10,

                1
            );

            /**







         * Add Ajax







         */

            /**







         * Start products import







         */

            add_action(
                'wp_ajax_wpme_import_products_count',

                function () {
                    $args = [
                        'posts_per_page' => -1,

                        'post_type' => 'product',

                        'cache_results' => false,

                        'post_status' => 'publish',
                    ];

                    $posts = get_posts($args);

                    $total_post_count = count($posts);

                    if ($total_post_count > 0) {
                        genoo_wpme_on_return([
                            'found' => $total_post_count,
                        ]);
                    }

                    genoo_wpme_on_return([
                        'error' => 'No published products found.',
                    ]);
                },

                10
            );

            /**







         * Import of the products







         */

            add_action(
                'wp_ajax_wpme_import_products',

                function () {
                    // Things

                    global $WPME_API;

                    $offest = $_REQUEST['offest'];

                    $per = $_REQUEST['per'] === null ? 0 : $_REQUEST['per'];

                    // Api?

                    if (isset($WPME_API)) {
                        // Get products

                        $productsImport = [];

                        $products = get_posts([
                            'posts_per_page' => $per,

                            'offset' => $offest,

                            'post_type' => 'product',

                            'post_status' => 'publish',

                            'orderby' => 'ID',

                            'order' => 'ASC',
                        ]);

                        if (!empty($products)) {
                            foreach ($products as $product) {
                                // If it has id, does not need importing

                                $meta = \get_post_meta(
                                    $product->ID,

                                    WPMKTENGINE_PRODUCT_KEY
                                );

                                if (empty($meta)) {
                                    $productArray = \WPME\WooCommerce\Product::convertToProductArray(
                                        $product
                                    );

                                    $productsImport[] = $productArray;
                                }
                            }
                        }

                        if (!empty($productsImport)) {
                            try {
                                // Send products

                                $updated = $WPME_API->setProducts(
                                    $productsImport
                                );

                                if (!empty($updated)) {
                                    foreach ($updated as $updatedProduct) {
                                        // Set product ID as product meta

                                        if (
                                            $updatedProduct->result == 'success'
                                        ) {
                                            // Add message

                                            $messages[] =
                                                'Product ID: ' .
                                                $updatedProduct->external_product_id .
                                                ' imported.';

                                            // Update post meta

                                            update_post_meta(
                                                $updatedProduct->external_product_id,

                                                WPMKTENGINE_PRODUCT_KEY,

                                                $updatedProduct->product_id
                                            );
                                        } else {
                                            $messages[] =
                                                'Product ID: ' .
                                                $updatedProduct->external_product_id .
                                                ' not imported. Result: ' .
                                                print_r($updatedProduct, true);
                                        }
                                    }
                                }
                            } catch (\Exception $e) {
                                $messages =
                                    'Error occured: ' . $e->getMessage();

                                $messages .= ' at ' . $WPME_API->lastQuery;
                            }
                        } else {
                            $messages = 'No products to be imported.';
                        }

                        genoo_wpme_on_return([
                            'messages' => $messages,
                        ]);
                    } else {
                        genoo_wpme_on_return([
                            'messages' => 'Error: API not found.',
                        ]);
                    }
                },

                10
            );
        }
    },

    10,

    3
);

/**







 * Genoo / WPME deactivation function







 */

if (!function_exists('genoo_wpme_deactivate_plugin')) {
    /**







     * @param $file







     * @param $message







     * @param string $recover







     */

    function genoo_wpme_deactivate_plugin($file, $message, $recover = '')
    {
        // Require files

        require_once ABSPATH . 'wp-admin/includes/plugin.php';

        // Deactivate plugin

        deactivate_plugins($file);

        unset($_GET['activate']);

        // Recover link

        if (empty($recover)) {
            $recover =
                '</p><p><a href="' .
                admin_url('plugins.php') .
                '">&laquo; ' .
                __('Back to plugins.', 'wpmktengine') .
                '</a>';
        }

        // Die with a message

        wp_die($message . $recover);

        exit();
    }
}

/**







 * Genoo / WPME json return data







 */

if (!function_exists('genoo_wpme_on_return')) {
    /**







     * @param $data







     */

    function genoo_wpme_on_return($data)
    {
        @error_reporting(0); // don't break json

        header('Content-type: application/json');

        die(json_encode($data));
    }
}

if (!function_exists('wpme_get_customer_lead_type')) {
    /**







     * Get Customer Lead Type







     *







     * @return bool|int







     */

    function wpme_get_customer_lead_type()
    {
        $leadType = false;

        $leadTypeSaved = get_option('WPME_ECOMMERCE');

        if (
            is_array($leadTypeSaved) &&
            array_key_exists('genooLeadUsercustomer', $leadTypeSaved)
        ) {
            $leadType = (int) $leadTypeSaved['genooLeadUsercustomer'];
        }

        return $leadType === 0 ? false : $leadType;
    }
}

if (!function_exists('wpme_can_continue_cookie_email')) {
    /**







     * Can continue with lead cookie, and email?







     *







     * @param $api







     * @param $email







     * @return bool







     */

    function wpme_can_continue_cookie_email($api, $email)
    {
        $can = \WPME\Helper::canContinue();

        if ($can == true) {
            $id = (int) \WPME\Helper::loggedInOrCookie();

            $lead = $api->getLead($id);

            if (is_object($lead) && isset($lead->lead->email)) {
                $leadEmail = $lead->lead->email;

                return (string) $leadEmail == (string) $email;
            }

            return false;
        }

        return false;
    }
}

if (!function_exists('wpme_simple_log_2')) {
    /**







     * @param        $msg







     * @param string $filename







     * @param bool   $dir







     */

    function wpme_simple_log_2($msg, $filename = 'log.log', $dir = false)
    {
        return;

        @date_default_timezone_set('UTC');

        @$time = date('Y-M-D h:i:s');

        @$time = '[' . $time . '] ';

        @$saveDir = WPMKTENGINE_ECOMMERCE_LOG_FOLDER;

        if (is_array($msg) || is_object($msg)) {
            $msg = print_r($msg, true);
        }

        @error_log($time . $msg . "\n", 3, './log.log');

        $log_file_data = './log.log';

        // if you don't add `FILE_APPEND`, the file will be erased each time you add a log

        // file_put_contents($log_file_data, $msg . "\n", FILE_APPEND);
    }
}

if (!function_exists('wpme_get_first_name_from_request')) {
    /**







     * Get First name from request







     *







     * @return null|string







     */

    function wpme_get_first_name_from_request()
    {
        if (isset($_POST)) {
            @$first = isset($_POST['billing_first_name'])
                ? $_POST['billing_first_name']
                : null;

            if ($first === null) {
                @$first = isset($_POST['shipping_first_name'])
                    ? $_POST['shipping_first_name']
                    : null;

                if ($first === null) {
                    @$first = isset($_POST['first_name'])
                        ? $_POST['first_name']
                        : null;
                }
            }

            return $first === null ? '' : $first;
        }

        return '';
    }
}

if (!function_exists('wpme_get_last_name_from_request')) {
    /**







     * Get Last name from request







     *







     * @return null|string







     */

    function wpme_get_last_name_from_request()
    {
        if (isset($_POST)) {
            @$first = isset($_POST['billing_last_name'])
                ? $_POST['billing_last_name']
                : null;

            if ($first === null) {
                @$first = isset($_POST['shipping_last_name'])
                    ? $_POST['shipping_last_name']
                    : null;

                if ($first === null) {
                    @$first = isset($_POST['last_name'])
                        ? $_POST['last_name']
                        : null;
                }
            }

            return $first === null ? '' : $first;
        }

        return '';
    }
}

if (!function_exists('wpme_clear_sess')) {
    function wpme_clear_sess()
    {
        return;

        @setcookie(
            'c00referred_by_affiliate_id',

            false,

            -1,

            COOKIEPATH,

            COOKIE_DOMAIN
        );

        unset($_COOKIE['c00referred_by_affiliate_id']);

        unset($_SESSION['c00referred_by_affiliate_id_date']);

        @setcookie(
            'c00referred_by_affiliate_id_date',

            false,

            -1,

            COOKIEPATH,

            COOKIE_DOMAIN
        );

        unset($_COOKIE['c00referred_by_affiliate_id_date']);

        unset($_SESSION['c00referred_by_affiliate_id_date']);

        @setcookie(
            'c00sold_by_affiliate_id',

            false,

            -1,

            COOKIEPATH,

            COOKIE_DOMAIN
        );

        unset($_COOKIE['c00sold_by_affiliate_id']);

        unset($_SESSION['c00sold_by_affiliate_id']);

        if (function_exists('clearRefferalFromSession')) {
            clearRefferalFromSession();
        }

        if (headers_sent()) {
            // Clear using js
        }
    }
}

/**







 * Activity Stream Helper







 */

function wpme_fire_activity_stream(
    $lead_id = null,

    $activityType = '',

    $activityName = '',

    $activityDescription = '',

    $activityURL = ''
) {
    wpme_on_wpme_api_set();

    // Get API and exit if not present

    global $WPME_API;

    if (!$WPME_API) {
        wpme_simple_log_2('WSC-05 - Putting Activity: - no api found');

        return;
    }

    wpme_simple_log_2(
        'WSC-05 - Putting Activity: ' .
            var_export(
                [
                    $lead_id,

                    $activityType,

                    $activityName,

                    $activityDescription,

                    $activityURL,
                ],

                true
            )
    );

    try {
        //$utc = 'now';

        // $time = strtotime($utc); //returns an integer epoch time: 1401339270

        $date = new DateTime('now', new DateTimeZone('America/Chicago'));

        $dater = $date->format('Y-m-d H:i:s');

        $WPME_API->putActivity(
            $lead_id,

            $dater,

            $activityType,

            $activityName,

            $activityDescription,

            $activityURL
        );
    } catch (\Exception $e) {
        wpme_simple_log_2(
            'WSC-05 - Error updating activity' . $e->getMessage()
        );

        wpme_simple_log_2(var_export($WPME_API->callstack));

        wpme_simple_log_2(var_export($WPME_API->http));
    }
}

/**







 * This utility function has been created after some back







 * and forth feedback and helps to decide what the correct







 * activity stream type should be for each action, name etc.







 */

function wpme_get_order_stream_decipher(
    \WC_Order $order,

    &$cartOrder,

    $givenOrderStatus = false
) {
    /**







     * Order Status Change - Regular Order







     */

    $getrenewal = get_post_meta($order->id, '_subscription_renewal', true);

    if (function_exists('wcs_get_subscriptions_for_order')):
        $subscriptions_ids = wcs_get_subscriptions_for_order($order->id, [
            'order_type' => 'any',
        ]);
    endif;

    $orderStatus = $givenOrderStatus ? $givenOrderStatus : $order->get_status();

    /**







     * 1. Go through normal status







     * payment declined(renewal failed and payment failed)







     */

    switch ($orderStatus) {
        case 'processing':
            $cartOrder->order_status = 'New Order';

            if (empty($subscriptions_ids) && !$getrenewal):
                $cartOrder->changed->order_status = 'New Order';
            elseif (!empty($subscriptions_ids)):
                $cartOrder->changed->order_status = 'sub payment';
            endif;

            $cartOrder->financial_status = 'paid';

            $cartOrder->changed->financial_status = 'paid';

            $cartOrder->action = 'new order';

            $cartOrder->changed->action = 'new order';

            break;

        case 'completed':
            $cartOrder->order_status = 'Completed Order';

            $cartOrder->changed->order_status = 'Completed Order';

            $cartOrder->financial_status = 'paid';

            $cartOrder->changed->financial_status = 'paid';

            $cartOrder->action = 'completed order';

            $cartOrder->changed->action = 'completed order';

            break;

        case 'cancelled':
            $cartOrder->order_status = 'Order Cancelled';

            $cartOrder->changed->order_status = 'Order Cancelled';

            $cartOrder->financial_status = '';

            $cartOrder->changed->financial_status = '';

            $cartOrder->action = 'cancelled order';

            $cartOrder->changed->action = 'cancelled order';

            break;

        case 'partially_refunded':
            // Search for: @@ PART REFUND

            break;
    }
}

/**







     * Returns original Genoo Order Id







     */

function get_wpme_order_from_woo_order($order)
{
    wpme_simple_log_2('WSC-05 - Get order ' . var_export($order->id, true));

    // https://docs.woocommerce.com/document/subscriptions/develop/functions/

    $ids = [];

    if ($order instanceof \WC_Subscription) {
        $ids = $order->get_related_orders('ids', 'parent');

        wpme_simple_log_2('WSC-05-A - Get order IDS ' . var_export($ids, true));
    }

    if (!is_array($ids) || count($ids) < 1) {
        wpme_simple_log_2('WSC-05-B - RETURN , no IDS');

        return false;
    }

    $order_id = $ids[key($ids)];

    wpme_simple_log_2('WSC-05-C - RETURN, id' . $order_id);

    $genoo_id = get_post_meta($order->id, WPMKTENGINE_ORDER_KEY, true);

    wpme_simple_log_2('WSC-05-D - RETURN, genoo id ' . $genoo_id);

    return $genoo_id;
}

/**







     * Get Lead ID from order







     */

function get_wpme_order_lead_id($genoo_id)
{
    // Api

    wpme_on_wpme_api_set();

    global $WPME_API;

    if (!isset($WPME_API)) {
        return false;
    }

    $order = false;

    try {
        $order = $WPME_API->callCustom('/wpmeorders[S]', 'GET', $genoo_id);
    } catch (\Exception $e) {
        return false;
    }

    return $order !== false ? $order->user_lid : false;
}

function get_wpme_subscription_activity_name($subscription_id)
{
    if (!$subscription_id) {
        return;
    }

    // Get the WC_Subscription object (if needed)

    $subscription = wc_get_order($subscription_id); // Or: new WC_Subscription($subscription_id);

    if (!$subscription) {
        return;
    }

    // Iterating through subscription items

    foreach ($subscription->get_items() as $item_id => $product_subscription) {
        // Get the name

        $return[] = $product_subscription->get_name();
    }

    return $return;
}

add_action(
    'woocommerce_order_status_processing',

    function ($order_id) {
        // Get API

        global $WPME_API;

        // Genoo order ID

        $id = get_post_meta($order_id, WPMKTENGINE_ORDER_KEY, true);

        $getrenewal = get_post_meta($order_id, '_subscription_renewal', true);

        if (function_exists('wcs_get_subscriptions_for_order')):
            $subscriptions_ids = wcs_get_subscriptions_for_order($order_id, [
                'order_type' => 'any',
            ]);
        endif;

        if (!$getrenewal):
            if (isset($WPME_API) && !empty($id)) {
                $order = new \WC_Order($order_id);

                $cartOrder = new \WPME\Ecommerce\CartOrder($id);

                $cartOrder->setApi($WPME_API);

                // Total price

                $cartOrder->total_price = $order->get_total();

                $cartOrder->tax_amount = $order->get_total_tax();

                $cartOrder->total_price = $order->get_total();

                $cartOrder->shipping_amount = $order->get_total_shipping();

                // Completed?

                if (!empty($subscriptions_ids)):
                    $cartOrder->order_status = 'subpayment';

                    $cartOrder->changed->order_status = 'subpayment';
                else:
                    $cartOrder->order_status = 'order';

                    $cartOrder->changed->order_status = 'order';
                endif;

                $cartOrder->financial_status = 'paid';

                // From email

                $cartOrderEmail = WPME\WooCommerce\Helper::getEmailFromOrder(
                    $order_id
                );

                if ($cartOrderEmail !== false) {
                    $cartOrder->email_ordered_from = $cartOrderEmail;

                    $cartOrder->changed->email_ordered_from = $cartOrderEmail;
                }

                try {
                    $WPME_API->updateCart(
                        $cartOrder->id,

                        (array) $cartOrder->getPayload()
                    );

                    wpme_simple_log_2(
                        'UPDATED ORDER to PROCESSING :' .
                            $cartOrder->id .
                            ' : WOO ID : ' .
                            $order_id
                    );
                } catch (\Exception $e) {
                    wpme_simple_log_2(
                        'Processing ORDER, Genoo ID:' . $cartOrder->id
                    );

                    wpme_simple_log_2(
                        'FAILED to updated order to PROCESSING :' .
                            $id .
                            ' : WOO ID : ' .
                            $order_id .
                            ' : Because : ' .
                            $e->getMessage()
                    );
                }
            }
        endif;
    },

    10,

    1
);

add_action(
    'woocommerce_subscription_payment_complete',

    function ($subscription) use ($api) {
        global $WPME_API;

        $leadType = wpme_get_customer_lead_type();

        $id = get_post_meta($subscription->id, WPMKTENGINE_ORDER_KEY, true);

        $genoo_id = get_wpme_order_from_woo_order($subscription);

        $genoo_lead_id = get_wpme_order_lead_id($genoo_id);

        if (!$genoo_lead_id) {
            return;
        }

        $order = new \WC_Order($subscription->id);

        $is_renewal = get_post_meta(
            $order->id,

            '_subscription_renewal_order_ids_cache',

            true
        );

        if (empty($is_renewal)):
            //subscription started

            $subscription_product_name = get_wpme_subscription_activity_name(
                $subscription->id
            );

            $subscription_product_name_values = implode(
                ',' . ' ',

                $subscription_product_name
            );

            wpme_fire_activity_stream(
                $genoo_lead_id,

                'subscription started',

                $subscription_product_name_values, // Title  $order->parent_id

                $subscription_product_name_values, // Content

                ' '

                // Permalink
            );

            if (isset($WPME_API) && !empty($id)) {
                $order = new \WC_Order($order->id);

                $cartAddress = $order->get_address('billing');

                $cartAddress2 = $order->get_address('shipping');

                $cartOrder = new \WPME\Ecommerce\CartOrder($order_genoo_id);

                $cartOrder->setApi($WPME_API);

                $cartOrder->total_price = $order->get_total();

                $cartOrder->setBillingAddress(
                    $cartAddress['address_1'],

                    $cartAddress['address_2'],

                    $cartAddress['city'],

                    $cartAddress['country'],

                    $cartAddress['phone'],

                    $cartAddress['postcode'],

                    '',

                    $cartAddress['state']
                );

                $cartOrder->setShippingAddress(
                    $cartAddress2['address_1'],

                    $cartAddress2['address_2'],

                    $cartAddress2['city'],

                    $cartAddress2['country'],

                    $cartAddress2['phone'],

                    $cartAddress2['postcode'],

                    '',

                    $cartAddress2['state']
                );

                $cartOrder->order_number = $order_id;

                $cartOrder->currency = $order->get_order_currency();

                $cartOrder->setTotal($order->get_total());

                // Add email and leadType

                $leadTYpe = wpme_get_customer_lead_type();

                $cartOrder->ec_lead_type_id = wpme_get_customer_lead_type();

                $cartOrder->changed->ec_lead_type_id = $leadTYpe;

                $cartOrder->email_ordered_from = $email;

                $cartOrder->changed->email_ordered_from = $email;

                $cartOrder->tax_amount = $order->get_total_tax();

                $cartOrder->changed->tax_amount = $order->get_total_tax();

                $cartOrder->shipping_amount = $order->get_total_shipping();

                $cartOrder->changed->shipping_amount = $order->get_total_shipping();

                // From email

                $cartOrderEmail = \WPME\WooCommerce\Helper::getEmailFromOrder(
                    $order_id
                );

                // Completed?

                $cartOrder->order_status = 'subpayment';

                $cartOrder->changed->order_status = 'subpayment';

                // From email

                $cartOrderEmail = WPME\WooCommerce\Helper::getEmailFromOrder(
                    $subscription->id
                );

                if ($cartOrderEmail !== false) {
                    $cartOrder->email_ordered_from = $cartOrderEmail;

                    $cartOrder->changed->email_ordered_from = $cartOrderEmail;

                    if (!is_null($leadNew) && $leadNew > 0) {
                        // We have a lead id

                        $lead_id = $leadNew;

                        // Set cookie

                        \WPME\Helper::setUserCookie($lead_id);

                        wpme_simple_log_2(
                            'WCUOM-2B-2A-3A-1B-3A-1 Created NEW LEAD for EMAIL :' .
                                $email .
                                ' : LEAD ID ' .
                                $lead_id
                        );
                    }
                }

                try {
                    $cartOrder->order_status = 'subpayment';

                    $cartOrder->changed->order_status = 'subpayment';

                    $WPME_API->updateCart(
                        $cartOrder->id,

                        (array) $cartOrder->getPayload()
                    );

                    wpme_simple_log_2(
                        'UPDATED ORDER to PROCESSING :' .
                            $cartOrder->id .
                            ' : WOO ID : ' .
                            $subscription->id
                    );
                } catch (\Exception $e) {
                    wpme_simple_log_2(
                        'Processing ORDER, Genoo ID:' . $cartOrder->id
                    );

                    wpme_simple_log_2(
                        'FAILED to updated order to PROCESSING :' .
                            $id .
                            ' : WOO ID : ' .
                            $subscription->id .
                            ' : Because : ' .
                            $e->getMessage()
                    );
                }
            }
        endif;
    },

    10,

    1
);

/**







         * Order Completed







         */

add_action(
    'woocommerce_order_status_completed',

    function ($order_id) {
        // Get API

        global $WPME_API;

        // Genoo order ID

        if (function_exists('wcs_get_subscriptions_for_order')):
            $subscriptions_ids = wcs_get_subscriptions_for_order($order_id, [
                'order_type' => 'any',
            ]);
        endif;

        if (empty($subscriptions_ids)):
            $id = get_post_meta($order_id, WPMKTENGINE_ORDER_KEY, true);

            wpme_simple_log_2(
                'Woocommerce order completed. Genoo order id: ' . $id
            );

            $order = new \WC_Order($order_id);

            if (isset($WPME_API) && !empty($id)) {
                $cartOrder = new \WPME\Ecommerce\CartOrder($id);

                $cartOrder->setApi($WPME_API);

                $cartOrder->actionOrderFullfillment();

                // Total price

                $cartOrder->total_price = $order->get_total();

                $cartOrder->tax_amount = $order->get_total_tax();

                $cartOrder->shipping_amount = $order->get_total_shipping();

                $cartOrder->financial_status = 'paid';

                // Completed?

                $cartOrder->completed_date = \WPME\Ecommerce\Utils::getDateTime();

                $cartOrder->changed->completed_date = \WPME\Ecommerce\Utils::getDateTime();

                // Completed?

                $cartOrder->order_status = 'completed';

                $cartOrder->changed->order_status = 'completed';

                // From email

                $cartOrderEmail = WPME\WooCommerce\Helper::getEmailFromOrder(
                    $order_id
                );

                if ($cartOrderEmail !== false) {
                    $cartOrder->email_ordered_from = $cartOrderEmail;

                    $cartOrder->changed->email_ordered_from = $cartOrderEmail;
                }

                try {
                    $result = $WPME_API->updateCart(
                        $cartOrder->id,

                        (array) $cartOrder->getPayload()
                    );

                    wpme_simple_log_2(
                        'UPDATED ORDER to COMPLETED :' .
                            $cartOrder->id .
                            ' : WOO ID : ' .
                            $order_id
                    );
                } catch (\Exception $e) {
                    wpme_simple_log_2(
                        'Finished ORDER, Genoo ID:' . $cartOrder->id
                    );

                    wpme_simple_log_2(
                        'FAILED to updated order to COMPLETED :' .
                            $id .
                            ' : WOO ID : ' .
                            $order_id .
                            ' : Because : ' .
                            $e->getMessage()
                    );
                }
            } elseif (isset($WPME_API)) {
                // New order? ok create it

                wpme_simple_log_2('WCUOM-2B-1 New order from cart.');

                // At this point, we need to start a cart, change it to new order, add everything.

                // and firstly, creat a lead.

                // 1. Create a lead get if exists

                // Do we have an email?

                @$order = new \WC_Order($order_id);

                $email = $order->get_billing_email();

                wpme_simple_log_2(
                    'WCUOM-2B-2 New ORDER, creating LEAD for email :' . $email
                );

                if ($email !== false) {
                    wpme_simple_log_2(
                        'WCUOM-2B-2A-1 Email exists, getting session data and lead info.'
                    );

                    // Get order & adresses

                    $session = WC()->session;

                    @$order = new \WC_Order($order_id);

                    $cartAddress = $order->get_address('billing');

                    $cartAddress2 = $order->get_address('shipping');

                    @$lead_first = isset($data['billing_first_name'])
                        ? $data['billing_first_name']
                        : null;

                    @$lead_last = isset($data['billing_last_name'])
                        ? $data['billing_last_name']
                        : null;

                    if (empty($lead_first) && empty($lead_last)) {
                        // If both are empty, try from order?

                        @$lead_first = $cartAddress['first_name'];

                        @$lead_last = $cartAddress['last_name'];

                        // If still empty try shipping name?

                        if (empty($lead_first) && empty($lead_last)) {
                            // If both are empty

                            @$lead_first = $cartAddress2['first_name'];

                            @$lead_last = $cartAddress2['last_name'];
                        }

                        if (empty($lead_first) && empty($lead_last)) {
                            // If both are empty

                            @$lead_first = isset($data['shipping_first_name'])
                                ? $data['shipping_first_name']
                                : null;

                            @$lead_last = isset($data['shipping_last_name'])
                                ? $data['shipping_last_name']
                                : null;
                        }

                        if (empty($lead_first) && empty($lead_last)) {
                            // If both are empty

                            @$lead_first = wpme_get_first_name_from_request();

                            @$lead_last = wpme_get_last_name_from_request();
                        }
                    }

                    wpme_simple_log_2(
                        'WCUOM-2B-2A-2 Tried to get first and last name:' .
                            $lead_first .
                            ' ' .
                            $lead_last
                    );

                    wpme_simple_log_2(
                        'WCUOM-2B-2A-3 Lead info to be created: ' .
                            print_r(
                                [
                                    $lead_first,

                                    $lead_last,

                                    $cartAddress,

                                    $cartAddress2,
                                ],

                                true
                            )
                    );

                    // Lead null for now

                    $lead_id = null;

                    try {
                        wpme_simple_log_2(
                            'WCUOM-2B-2A-3A-1 Trying to get lead by email.'
                        );

                        // Lead exists, ok, set up Lead ID

                        // NO lead, create one

                        $leadTypeFirst = wpme_get_customer_lead_type();

                        wpme_simple_log_2(
                            'WCUOM-2B-2A-3A-1B-2 Creating one, leadtype: ' .
                                $leadTypeFirst
                        );

                        $leadType = $WPME_API->settingsRepo->getLeadTypeSubscriber();

                        if (
                            $leadTypeFirst !== false &&
                            !is_null($leadTypeFirst) &&
                            is_numeric($leadTypeFirst)
                        ) {
                            $leadType = $leadTypeFirst;
                        }

                        $attributes = apply_filters(
                            'genoo_wpme_lead_creation_attributes',

                            [
                                'organization' => '',

                                'address1' => $cartAddress['address_1'],

                                'address2' => $cartAddress['address_2'],

                                'city' => $cartAddress['city'],

                                'country' => $cartAddress['country'],

                                'zip' => $cartAddress['postcode'],

                                'mobilephone' => $cartAddress['phone'],

                                'source' => 'eCommerce Order',
                            ],

                            'ecommerce-new-order-lead'
                        );

                        wpme_clear_sess();

                        wpme_simple_log_2(
                            'WCUOM-2B-2A-3A-1B-2B Lead Attributes after filter: ' .
                                print_r($attributes, true)
                        );

                        $leadNew = $WPME_API->setLead(
                            (int) $leadType,

                            $email,

                            $lead_first,

                            $lead_last,

                            '',

                            true,

                            $attributes
                        );

                        wpme_simple_log_2(
                            'WCUOM-2B-2A-3A-1B-3 New Lead: ' . $leadNew
                        );

                        $leadNew = (int) $leadNew;

                        if (function_exists('clearRefferalFromSession')) {
                            clearRefferalFromSession();
                        }

                        if (!is_null($leadNew) && $leadNew > 0) {
                            // We have a lead id

                            $lead_id = $leadNew;

                            // Set cookie

                            \WPME\Helper::setUserCookie($lead_id);

                            wpme_simple_log_2(
                                'WCUOM-2B-2A-3A-1B-3A-1 Created NEW LEAD for EMAIL :' .
                                    $email .
                                    ' : LEAD ID ' .
                                    $lead_id
                            );
                        } else {
                            wpme_simple_log_2(
                                'WCUOM-2B-2A-3A-1B-3B-1 Lead not created!'
                            );

                            wpme_simple_log_2(
                                'WCUOM-2B-2A-3A-1B-3A-1 response:'
                            );

                            wpme_simple_log_2(
                                $WPME_API->http->response['body']
                            );
                        }
                    } catch (\Exception $e) {
                        wpme_simple_log_2(
                            'WCUOM-2B-2A-3B-1 Error GETTING or CREATING lead by EMAIL :' .
                                $email .
                                ' : ' .
                                $e->getMessage()
                        );
                    }

                    // 2 Start and order if lead not null

                    // 2.1 Set to new order

                    if ($lead_id !== null && $lead_id > 0) {
                        wpme_simple_log_2(
                            'WCUOM-2B-2A-4-1 Lead exists, creating order. lead id: ' .
                                $lead_id
                        );

                        $cart = WC()->cart;

                        $cartContents = \WPME\WooCommerce\VariantCart::convertCartToObject(
                            $cart->cart_contents
                        );

                        $cartOrder = new \WPME\Ecommerce\CartOrder();

                        $cartOrder->setApi($WPME_API);

                        $cartOrder->setUser($lead_id);

                        $cartOrder->actionNewOrder();

                        $cartOrder->setBillingAddress(
                            $cartAddress['address_1'],

                            $cartAddress['address_2'],

                            $cartAddress['city'],

                            $cartAddress['country'],

                            $cartAddress['phone'],

                            $cartAddress['postcode'],

                            '',

                            $cartAddress['state']
                        );

                        $cartOrder->setShippingAddress(
                            $cartAddress2['address_1'],

                            $cartAddress2['address_2'],

                            $cartAddress2['city'],

                            $cartAddress2['country'],

                            $cartAddress2['phone'],

                            $cartAddress2['postcode'],

                            '',

                            $cartAddress2['state']
                        );

                        $cartOrder->order_number = $order_id;

                        $cartOrder->currency = $order->get_order_currency();

                        $cartOrder->setTotal($order->get_total());

                        $cartOrder->addItemsArray($cartContents);

                        // Add email and leadType

                        $leadTYpe = wpme_get_customer_lead_type();

                        $cartOrder->ec_lead_type_id = wpme_get_customer_lead_type();

                        $cartOrder->changed->ec_lead_type_id = $leadTYpe;

                        $cartOrder->email_ordered_from = $email;

                        $cartOrder->changed->email_ordered_from = $email;

                        $cartOrder->tax_amount = $order->get_total_tax();

                        $cartOrder->changed->tax_amount = $order->get_total_tax();

                        $cartOrder->shipping_amount = $order->get_total_shipping();

                        $cartOrder->changed->shipping_amount = $order->get_total_shipping();

                        // From email

                        $cartOrderEmail = \WPME\WooCommerce\Helper::getEmailFromOrder(
                            $order_id
                        );

                        if ($cartOrderEmail !== false) {
                            $cartOrder->email_ordered_from = $cartOrderEmail;

                            $cartOrder->changed->email_ordered_from = $cartOrderEmail;
                        }

                        wpme_get_order_stream_decipher($order, $cartOrder);

                        // Continue

                        $cartOrder->startNewOrder();

                        // Set order meta

                        \update_post_meta(
                            $order_id,

                            WPMKTENGINE_ORDER_KEY,

                            $cartOrder->id
                        );

                        // Remove session id

                        unset(WC()->session->{WPMKTENGINE_ORDER_KEY});

                        // Log

                        wpme_simple_log_2(
                            'WCUOM-2B-2A-4-2 Finished ORDER, Genoo ID:' .
                                $cartOrder->id
                        );

                        wpme_simple_log_2(
                            'WCUOM-2B-2A-4-3 Finished ORDER, WooCommerce ID:' .
                                $order_id
                        );
                    } else {
                        wpme_simple_log_2(
                            'WCUOM-2B-2A-5-1 After all attempts no lead created.'
                        );

                        wpme_simple_log_2(
                            'WCUOM-2B-2A-5-2 Last API response: ' .
                                print_r($WPME_API->http, true)
                        );
                    }
                } else {
                    wpme_simple_log_2(
                        'WCUOM-2B-2B-1 No email for order, can\'t continue'
                    );
                }
            }
        endif;
    },

    10,

    1
);

//reactivate the order from hold

add_action(
    'woocommerce_subscription_status_on-hold_to_active',

    'on_reactive',

    10,

    2
);

function on_reactive($subscription)
{
    if ($subscription->suspension_count != 0):
        $genoo_id = get_wpme_order_from_woo_order($subscription);

        if (!$genoo_id) {
            return;
        }

        wpme_simple_log_2(
            'WSC-01-A - Subscription activated- Genoo ID: ' . $genoo_id
        );

        $genoo_lead_id = get_wpme_order_lead_id($genoo_id);

        if (!$genoo_lead_id) {
            return;
        }

        wpme_simple_log_2(
            'WSC-01-B - Subscription activated - Lead ID: ' . $genoo_lead_id
        );

        //subscription reactivated

        $subscription_product_name = get_wpme_subscription_activity_name(
            $subscription->id
        );

        $subscription_product_name_values = implode(
            ',' . ' ',

            $subscription_product_name
        );

        wpme_fire_activity_stream(
            $genoo_lead_id,

            'subscription reactivated',

            $subscription_product_name_values, // Title

            $subscription_product_name_values, // Content

            ' '

            // Permalink
        );
    endif;
}

//reactivate the order from pending-cancel

add_action(
    'woocommerce_subscription_status_pending-cancel_to_active',

    'pending_cancel',

    10,

    2
);

function pending_cancel($subscription)
{
    $genoo_id = get_wpme_order_from_woo_order($subscription);

    if (!$genoo_id) {
        return;
    }

    wpme_simple_log_2(
        'WSC-01-A - Subscription activated- Genoo ID: ' . $genoo_id
    );

    $genoo_lead_id = get_wpme_order_lead_id($genoo_id);

    if (!$genoo_lead_id) {
        return;
    }

    wpme_simple_log_2(
        'WSC-01-B - Subscription activated - Lead ID: ' . $genoo_lead_id
    );

    $subscription_product_name = get_wpme_subscription_activity_name(
        $subscription->id
    );

    $subscription_product_name_values = implode(
        ',' . ' ',

        $subscription_product_name
    );

    wpme_fire_activity_stream(
        $genoo_lead_id,

        'subscription reactivated',

        $subscription_product_name_values, // Title

        $subscription_product_name_values, // Content

        ' '

        // Permalink
    );
}

//woocommerce order status as hold woocommerce_customer_changed_subscription_to_on-hold woocommerce_subscription_status_on-hold

add_action(
    'woocommerce_subscription_status_on-hold',

    'on_hold_subscription',

    10,

    2
);

function on_hold_subscription($subscription)
{
    $user = wp_get_current_user();

    $user_meta = get_userdata($user->ID);

    $user_roles = $user->roles;

    $genoo_id = get_wpme_order_from_woo_order($subscription);

    $order = new \WC_Order($subscription->id);

    if (!$genoo_id) {
        return;
    }

    wpme_simple_log_2(
        'WSC-01-A - Subscription on - hold - Genoo ID: ' . $genoo_id
    );

    $genoo_lead_id = get_wpme_order_lead_id($genoo_id);

    if (!$genoo_lead_id) {
        return;
    }

    wpme_simple_log_2(
        'WSC-01-B - Subscription on - hold - Lead ID: ' . $genoo_lead_id
    );

    $subscription_product_name = get_wpme_subscription_activity_name(
        $subscription->id
    );

    $subscription_product_name_values = implode(
        ',' . ' ',

        $subscription_product_name
    );

    if (in_array('administrator', $user_roles)):
        wpme_fire_activity_stream(
            $genoo_lead_id,

            'subscription on hold',

            $subscription_product_name_values, // Title

            $subscription_product_name_values, // Content

            ' '

            // Permalink
        );
    endif;
}

//customer chaged order status as on hold

add_action(
    'woocommerce_customer_changed_subscription_to_on-hold',

    'customer_on_hold_subscription',

    10,

    2
);

function customer_on_hold_subscription($subscription)
{
    $genoo_id = get_wpme_order_from_woo_order($subscription);

    $order = new \WC_Order($subscription->id);

    if (!$genoo_id) {
        return;
    }

    wpme_simple_log_2(
        'WSC-01-A - Subscription on - hold - Genoo ID: ' . $genoo_id
    );

    $genoo_lead_id = get_wpme_order_lead_id($genoo_id);

    if (!$genoo_lead_id) {
        return;
    }

    wpme_simple_log_2(
        'WSC-01-B - Subscription on - hold - Lead ID: ' . $genoo_lead_id
    );

    $subscription_product_name = get_wpme_subscription_activity_name(
        $subscription->id
    );

    $subscription_product_name_values = implode(
        ',' . ' ',

        $subscription_product_name
    );

    wpme_fire_activity_stream(
        $genoo_lead_id,

        'subscription on hold',

        $subscription_product_name_values, // Title

        $subscription_product_name_values, // Content

        ' '

        // Permalink
    );
}

// Activity |> subscription cancelled

add_action(
    'woocommerce_subscription_status_cancelled',

    function ($subscription) {
        wpme_simple_log_2(
            'WSC-01 - Subscription Cancelled: ' .
                var_export($subscription->id, true)
        );

        $genoo_id = get_wpme_order_from_woo_order($subscription);

        $order = new \WC_Order($subscription->id);

        if (!$genoo_id) {
            return;
        }

        wpme_simple_log_2(
            'WSC-01-A - Subscription Cancelled - Genoo ID: ' . $genoo_id
        );

        $genoo_lead_id = get_wpme_order_lead_id($genoo_id);

        if (!$genoo_lead_id) {
            return;
        }

        wpme_simple_log_2(
            'WSC-01-B - Subscription Cancelled - Lead ID: ' . $genoo_lead_id
        );

        $subscription_product_name = get_wpme_subscription_activity_name(
            $subscription->id
        );

        $subscription_product_name_values = implode(
            ',' . ' ',

            $subscription_product_name
        );

        wpme_fire_activity_stream(
            $genoo_lead_id,

            'subscription cancelled',

            $subscription_product_name_values, // Title

            $subscription_product_name_values, // Content

            ' '

            // Permalink
        );
    },

    10,

    1
);

//pending cancel subscription

add_action(
    'woocommerce_subscription_status_pending-cancel',

    'pending_cancel_subscription',

    10,

    1
);

function pending_cancel_subscription($subscription)
{
    $order = new \WC_Order($subscription->id);

    wpme_simple_log_2(
        'WSC-01 -  Subscription Pending Cancellation: ' .
            var_export($subscription->id, true)
    );

    $genoo_id = get_wpme_order_from_woo_order($subscription);

    $order = new \WC_Order($subscription->id);

    if (!$genoo_id) {
        return;
    }

    wpme_simple_log_2(
        'WSC-01-A -  Subscription Pending Cancellation - Genoo ID: ' . $genoo_id
    );

    $genoo_lead_id = get_wpme_order_lead_id($genoo_id);

    if (!$genoo_lead_id) {
        return;
    }

    wpme_simple_log_2(
        'WSC-01-B -  Subscription Pending Cancellation - Lead ID: ' .
            $genoo_lead_id
    );

    $subscription_product_name = get_wpme_subscription_activity_name(
        $subscription->id
    );

    $subscription_product_name_values = implode(
        ',' . ' ',

        $subscription_product_name
    );

    wpme_fire_activity_stream(
        $genoo_lead_id,

        'Subscription Pending Cancellation',

        'subscription cancelled',

        $subscription_product_name_values, // Title

        $subscription_product_name_values, // Content

        ' '

        // Permalink

        // Permalink
    );
}

//subscription expired

add_action(
    'woocommerce_subscription_status_expired',

    'my_on_subscription_expired',

    10
);

function my_on_subscription_expired($subscription)
{
    $order = new \WC_Order($subscription->id);

    $genoo_id = get_wpme_order_from_woo_order($subscription);

    if (!$genoo_id) {
        return;
    }

    wpme_simple_log_2(
        'WSC-01-A - Subscription activated- Genoo ID: ' . $genoo_id
    );

    $genoo_lead_id = get_wpme_order_lead_id($genoo_id);

    $subscription_product_name = get_wpme_subscription_activity_name(
        $subscription->id
    );

    $subscription_product_name_values = implode(
        ',' . ' ',

        $subscription_product_name
    );

    wpme_fire_activity_stream(
        $genoo_lead_id,

        'subscription completed',

        $subscription_product_name_values, // Title

        $subscription_product_name_values, // Content

        ' '

        // Permalink
    );
}

//completed the subscription renewal payment

add_action(
    'woocommerce_subscription_renewal_payment_complete',

    function ($subscription, $order) {
        global $WPME_API;

        $manual = get_post_meta(
            $subscription->id,

            '_requires_manual_renewal',

            true
        );

        if ($manual == 'true'):
            $id = get_post_meta($order->id, WPMKTENGINE_ORDER_KEY, true);

            if (isset($WPME_API) && !empty($id)) {
                $order = new \WC_Order($order->id);

                $cartAddress = $order->get_address('billing');

                $cartAddress2 = $order->get_address('shipping');

                $cartOrder = new \WPME\Ecommerce\CartOrder($id);

                $cartOrder->setApi($WPME_API);

                $get_order = wc_get_order($order->id);

                foreach ($get_order->get_items() as $item) {
                    $changedItemData = $item->get_data();

                    // Let's see if this is in

                    $id = (int) get_post_meta(
                        $changedItemData['product_id'],

                        WPMKTENGINE_PRODUCT_KEY,

                        true
                    );

                    if (is_numeric($id) && $id > 0) {
                        $array['product_id'] = $id;

                        $array['quantity'] = $changedItemData['quantity'];

                        $array['total_price'] = $changedItemData['total'];

                        $array['unit_price'] =
                            $changedItemData['total'] /
                            $changedItemData['quantity'];

                        $array['external_product_id'] =
                            $changedItemData['product_id'];

                        $array['name'] = $changedItemData['name'];

                        $wpmeApiOrderItems[] = $array;
                    }
                }

                $cartOrder->setBillingAddress(
                    $cartAddress['address_1'],

                    $cartAddress['address_2'],

                    $cartAddress['city'],

                    $cartAddress['country'],

                    $cartAddress['phone'],

                    $cartAddress['postcode'],

                    '',

                    $cartAddress['state']
                );

                $cartOrder->setShippingAddress(
                    $cartAddress['address_1'],

                    $cartAddress['address_2'],

                    $cartAddress['city'],

                    $cartAddress['country'],

                    $cartAddress['phone'],

                    $cartAddress['postcode'],

                    '',

                    $cartAddress['state']
                );

                $cartOrder->setTotal($order->get_total());

                $cartOrder->total_price = $order->get_total();

                $cartOrder->tax_amount = $order->get_total_tax();

                $cartOrder->shipping_amount = $order->get_total_shipping();

                $cartOrder->order_status = 'subrenewal';

                $cartOrder->changed->order_status = 'subrenewal';

                $cartOrder->financial_status = 'paid';

                $cartOrder->action = 'subscription Renewal';

                $cartOrder->changed->action = 'subscription Renewal';

                $cartOrder->addItemsArray($wpmeApiOrderItems);

                // Completed?

                // From email

                $cartOrderEmail = WPME\WooCommerce\Helper::getEmailFromOrder(
                    $order->id
                );

                if ($cartOrderEmail !== false) {
                    $cartOrder->email_ordered_from = $cartOrderEmail;

                    $cartOrder->changed->email_ordered_from = $cartOrderEmail;
                }

                try {
                    $WPME_API->updateCart(
                        $cartOrder->id,

                        (array) $cartOrder->getPayload()
                    );

                    wpme_simple_log_2(
                        'UPDATED ORDER to PROCESSING :' .
                            $cartOrder->id .
                            ' : WOO ID : ' .
                            $order->id
                    );
                } catch (\Exception $e) {
                    wpme_simple_log_2(
                        'Processing ORDER, Genoo ID:' . $cartOrder->id
                    );

                    wpme_simple_log_2(
                        'FAILED to updated order to PROCESSING :' .
                            $id .
                            ' : WOO ID : ' .
                            $order->id .
                            ' : Because : ' .
                            $e->getMessage()
                    );
                }
            }
        endif;
    },

    10,

    2
);

add_filter(
    'upgrader_post_install',
    function ($response, $hook_extra, $result) use ($file) {
        custom_logs('fffffffffffffffffffffffffff');
    },
    10,
    3
);
function custom_logs($message)
{
    if (is_array($message)) {
        $message = json_encode($message);
    }
    $file = fopen('../custom_logs.log', 'a');
    echo fwrite($file, "\n" . date('Y-m-d h:i:s') . ' :: ' . $message);
    fclose($file);
}
