<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Event admin
 */
class RQ_Events_Ajax {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp_ajax_calculate_cost', array( $this, 'calculate_cost' ) );
		add_action( 'wp_ajax_nopriv_calculate_cost', array( $this, 'calculate_cost' ) );
	}

	public function calculate_cost() {
		$ticket_id = $_POST['ticket_id'];
		$cost = get_post_meta( $ticket_id, 'cost', true );

       	$html = '<input type="hidden" name="base_cost" value="'.$cost.'" />
       			<input type="hidden" name="ticket_total_cost" value="'.$cost.'" />
       			<input type="hidden" name="currency_symbol" value="'.get_woocommerce_currency_symbol().'" />';
       	$html_arr = array();

       	$html_arr[] = array(
       		'input' => $html,
       		'cost'  => get_woocommerce_currency_symbol() . ' ' . ($cost)
       	);

       	echo json_encode($html_arr);

        die();
	}

}

new RQ_Events_Ajax();