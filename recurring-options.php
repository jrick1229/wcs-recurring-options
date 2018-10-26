<?php
/**
 * Plugin Name: WCS Recurring Options
 * Plugin URI:  https://github.com/jrick1229/wcs-recurring-options
 * Description: Give customers the option between manual and automatic renewals.
 * Author:      jrick1229
 * Author URI:  http://github.com/jrick1229
 * Version:     v1.0.0
 * License:     GPLv3
 *
 * GitHub Plugin URI: jrick1229/wcs-recurring-options
 * GitHub Branch: master
 *
 * Copyright 2018 Prospress, Inc.  (email : freedoms@prospress.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package WooCommerce Subscriptions
 * @author  Prospress Inc.
 * @since   1.0.0
 */

/* 
 *
 * Add select field to the checkout page
 *
 */

add_action( 'woocommerce_before_order_notes', 'recurring_options' );

function recurring_options( $checkout ) { 
    echo '<h2>Renewal method:</h2>';
	woocommerce_form_field( 'renewal_method', array(
	    'type'          => 'select',
	    'class'         => array( 'wps-drop' ),
	    'label'         => __( 'Renewal Method' ),
	    'options'       => array(
            'blank'		=> __( 'Select a renewal type', 'wps' ),
	        'manual_renewal'	=> __( 'Manual Renewals', 'wps' ),
	    	'automatic_renewal'		=> __( 'Automatic Renewals', 'wps' )
	    )
    ),
	$checkout->get_value( 'renewal_method' ));
}

/* 
 *
 * Process the checkout
 *
 */

add_action('woocommerce_checkout_process', 'recurring_options_process');

function recurring_options_process() {
    global $woocommerce;
    // Check if set, if its not set add an error.
    if ($_POST['renewal_method'] == "blank") {
        wc_add_notice( '<strong>Please select a renewal type.</strong>', 'error' );
    }
}

/* 
 *
 * Update the order meta with field value
 *
 */

add_action('woocommerce_checkout_update_order_meta', 'recurring_options_field_update_order_meta');

function recurring_options_field_update_order_meta( $order_id ) {
    if ($_POST['renewal_method']) {
        update_post_meta( $order_id, 'renewal_method', esc_attr($_POST['renewal_method']));
    }
}

/* 
 *
 * Display field value on the order edition page
 *
 */

add_action( 'woocommerce_admin_order_data_after_billing_address', 'recurring_options_select_checkout_field_display_admin_order_meta', 10, 1 );

function recurring_options_select_checkout_field_display_admin_order_meta($order){
	echo '<p><strong>'.__('Renewal Type').':</strong> ' . get_post_meta( $order->id, 'renewal_method', true ) . '</p>';
}

/* 
 *
 * Add selection field value to emails
 *
 */

add_filter('woocommerce_email_order_meta_keys', 'recurring_options_select_order_meta_keys');

function recurring_options_select_order_meta_keys( $keys ) {
	$keys['renewal_method:'] = 'renewal_method';
	return $keys;
}

/*
 *
 * Set renewal type in created subscription
 *
 */

//function recurring_options_set_subscription_renewal_type( $order_id, $subscription_id, $is_manual ) {
//    if ($_POST['renewal_method'] == 'manual_renewal') {
//        wcs_get_subscription( $subscription_id );
//        $subscription_id WC_Subscription::update_manual( $is_manual == true);
//        return $is_manual;
//    } else {}
//    //return $is_manual;
//}

