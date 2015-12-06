<?php
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * WC_Events_Cart class.
 */
class WC_Events_Cart {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'woocommerce_event_add_to_cart', array( $this, 'add_to_cart' ), 30 );

		add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 10, 1 );
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 10, 2 );
		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_cart_item' ), 10, 3 );
		add_action( 'woocommerce_add_order_item_meta', array( $this, 'order_item_meta' ), 10, 2 );
		// add_action( 'woocommerce_cart_item_quantity', array( $this, 'cart_item_quantity' ), 10, 2 );

		add_filter( 'woocommerce_get_price_html', array($this, 'rq_events_price_html'), 100, 2 );
		// add_action('woocommerce_before_shop_loop_item_title', array($this, 'rq_events_flash_message'));
		add_action('woocommerce_after_shop_loop_item_title', array($this, 'rq_events_flash_message'));
		
	}

	public function rq_events_flash_message() {
		global $product, $post;

		if( $product->product_type !== 'event') {
			return;
		}

		echo apply_filters( 'rq_events_flash_message', '<span class="onsale bg_primary headerfont">' . __( 'Event!', 'redq-events' ) . '</span>', $post, $product );
	}


	public function rq_events_price_html( $price, $product ) {
		global  $woocommerce;
	    if($product->product_type !== 'event') {
	    	return $price;
	    }

	    $args = array(
					'post_parent' => $product->id,
					'post_type'   => 'any', 
					'posts_per_page' => -1,
					'post_status' => 'publish'
				);

	    $children_array = get_children( $args );
	    if (! empty( $children_array ) ) {
			$price           = array();
			$currency_symbol = get_woocommerce_currency_symbol();

		    foreach ($children_array as $children) {
		    	$price[] = get_post_meta($children->ID, 'cost', true);
		    }

		    $min = min( $price );
		    $max = max( $price );
		    
		    if ( count( $price ) === 1 ) {
		    	
		    	$price_html = sprintf( __('%s%.2f', 'redq-events'), $currency_symbol, $min );
		    }

		    if ( $min === $max ) {
				$price_html = sprintf( __('%s%.2f', 'redq-events'), $currency_symbol, $min );

		    } else {
		    	$price_html = sprintf( __('%s%.2f - %s%.2f', 'redq-events'), $currency_symbol, $min, $currency_symbol, $max );
		    }
	    }

	    return apply_filters( 'rq_events_price_html', sprintf( __('%s', 'redq-events'), $price_html ), $price, $product );

	}

	// public function cart_item_quantity( $product_quantity, $cart_item_key ) {
	// 	global $woocommerce;
		
	// 	$cart_details = $woocommerce->cart->cart_contents;
		
	// 	if(isset($cart_details)){
	// 		foreach ($cart_details as $key => $value) {
	// 			if($key === $cart_item_key){
	// 				$product_id = $value['product_id'];

	// 				$product_type = get_product($product_id)->product_type;

	// 				if($product_type === 'event'){
	// 					return sprintf( _n( '%d ticket.', '%d tickets.', $value['event']['no_of_ticket'], 'redq-events' ), $value['event']['no_of_ticket'] );
	// 				} else {
	// 					return $product_quantity;
	// 				}
	// 			}
	// 		}
	// 	}
	// }

	/**
	 * Add to cart for events
	 */
	public function add_to_cart() {
		global $product;

		// Prepare form
		$event_form = new RQ_Event_Form( $product );

		// Get template
		woocommerce_get_template( 'single-product/add-to-cart/event.php', array( 'event_form' => $event_form ), 'redq-events', RQ_EVENTS_TEMPLATE_PATH );
	}

	/**
	 * When a ticket is added to the cart, validate it
	 *
	 * @param mixed $passed
	 * @param mixed $product_id
	 * @param mixed $qty
	 * @return bool
	 */
	public function validate_add_cart_item( $passed, $product_id, $qty ) {
		global $woocommerce;

		$product      = get_product( $product_id );

		if ( $product->product_type !== 'event' ) {
			return $passed;
		}

		$event_form = new RQ_Event_Form( $product );
		$data         = $event_form->get_posted_data();
		$validate     = $event_form->is_passed_validation( $data );

		if ( is_wp_error( $validate ) ) {
			wc_add_notice( $validate->get_error_message(), 'error' );
			return false;
		}

		return $passed;
	}

	/**
	 * Adjust the price of the event product based on ticket properties
	 *
	 * @access public
	 * @param mixed $cart_item
	 * @return array cart item
	 */
	public function add_cart_item( $cart_item ) {

		if ( ! empty( $cart_item['event'] ) && ! empty( $cart_item['event']['_cost'] ) ) {
			$cart_item['data']->set_price( $cart_item['event']['_cost'] );
		}
		
		return $cart_item;

	}

	/**
	 * Get data from the session and add to the cart item's meta
	 *
	 * @access public
	 * @param mixed $cart_item
	 * @param mixed $values
	 * @return array cart item
	 */
	public function get_cart_item_from_session( $cart_item, $values ) {
		if ( ! empty( $values['event'] ) ) {
			$cart_item['event'] = $values['event'];
			$cart_item          = $this->add_cart_item( $cart_item );
		}
		return $cart_item;
	}

	/**
	 * Add posted data to the cart item
	 *
	 * @access public
	 * @param mixed $cart_item_meta
	 * @param mixed $product_id
	 * @return void
	 */
	public function add_cart_item_data( $cart_item_meta, $product_id ) {
		$product = get_product( $product_id );

		if ( 'event' !== $product->product_type ) {
			return $cart_item_meta;
		}

		$event_form                       = new RQ_Event_Form( $product );
		$cart_item_meta['event']          = $event_form->get_posted_data( $_POST );
		$cart_item_meta['event']['_cost'] = $event_form->calculate_ticket_cost( $_POST );
		
		return $cart_item_meta;
	}

	/**
	 * Put meta data into format which can be displayed
	 *
	 * @access public
	 * @param mixed $other_data
	 * @param mixed $cart_item
	 * @return array meta
	 */
	public function get_item_data( $other_data, $cart_item ) {

		$product = get_product( $cart_item['product_id'] );

		if ( 'event' !== $product->product_type ) {
			return;
		}
		
		if ( ! empty( $cart_item['event'] ) ) {
			foreach ( $cart_item['event'] as $key => $value ) {

				if ( substr( $key, 0, 1 ) !== '_' )
					$other_data[] = array(
						'name'    => get_rq_event_data_label( $key, $cart_item['data'] ),
						'value'   => $value,
						'display' => ''
					);
			}
		}
		$other_data[] = array(
			'name'    => __( 'No of ticket(s)', 'redq-events' ),
			'value'   => $cart_item['quantity'],
			'display' => ''
		);
		return $other_data;
	}

	/**
	 * order_item_meta function.
	 *
	 * @param mixed $item_id
	 * @param mixed $values
	 */
	public function order_item_meta( $item_id, $values ) {

		
		if ( ! empty( $values['event'] ) ) {
			
			$product = $values['data'];
			$values['event']['no_of_ticket'] = $values['quantity'];

			// Add summary of details to line item
			foreach ( $values['event'] as $key => $value ) {
				echo $key; echo $value;
				if ( strpos( $key, '_' ) !== 0 ) {
					woocommerce_add_order_item_meta( $item_id, get_rq_event_data_label( $key, $product ), $value );
				}
			}
		}
	}

}

new WC_Events_Cart();