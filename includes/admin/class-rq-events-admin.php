<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Event admin
 */
class RQ_Events_Admin {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'product_type_options', array( $this, 'product_type_options' ) );
		add_filter( 'product_type_selector' , array( $this, 'product_type_selector' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'styles_and_scripts' ) );
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_tab' ), 5 );
		add_action( 'woocommerce_product_write_panels', array( $this, 'event_panels' ) );
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'event_data' ) );
		add_action( 'woocommerce_process_product_meta', array( $this,'save_product_data' ), 20 );

		add_action( 'wp_ajax_rq_event_add_ticket', array( $this, 'add_event_ticket' ) );
		add_action( 'wp_ajax_woocommerce_rq_event_remove_ticket', array( $this, 'remove_event_ticket' ) );
	}

	/**
	 * Tweak product type options
	 * @param  array $options
	 * @return array
	 */
	public function product_type_options( $options ) {
		$options['virtual']['wrapper_class'] .= ' show_if_event';
		return $options;
	}

	/**
	 * Add the event product type
	 */
	public function product_type_selector( $types ) {
		$types[ 'event' ] = __( 'Event product', 'redq-events' );
		return $types;
	}

	/**
	 * Show the event tab
	 */
	public function add_tab() {
		include( 'views/html-event-tab.php' );
	}

	/**
	 * Show the event data view
	 */
	public function event_data() {
		global $post;
		$post_id = $post->ID;
		include( 'views/html-event-data.php' );
	}

	/**
	 * Show the event panels views
	 */
	public function event_panels() {
		global $post;

		$post_id = $post->ID;

		wp_enqueue_script( 'rq_events_writepanel_js' );
		
		include( 'views/html-event-tickets.php' );
	}

	/**
	 * Add admin styles
	 */
	public function styles_and_scripts() {
		global $post, $woocommerce, $wp_scripts;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'rq_events_admin_styles', RQ_EVENTS_PLUGIN_URL . '/assets/css/admin.css', null, RQ_EVENTS_VERSION );

		if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '<' ) ) {
			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

			wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css', null, WC_VERSION );
			wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css' );
		}

		wp_enqueue_script('redq_events_maps_google', 'http://maps.google.com/maps/api/js?sensor=false', array('jquery'), false, true);

		wp_register_script( 'rq_events_writepanel_js', RQ_EVENTS_PLUGIN_URL . '/assets/js/writepanel' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'redq_events_maps_google' ), RQ_EVENTS_VERSION, true );

		$params = array(
			'i18n_remove_ticket'     => esc_js( __( 'Are you sure you want to remove this ticket type?', 'redq-events' ) ),
			'nonce_delete_ticket'    => wp_create_nonce( 'delete-event-ticket' ),
			'nonce_add_ticket'       => wp_create_nonce( 'add-event-ticket' ),

			'post'                   => isset( $post->ID ) ? $post->ID : '',
			'plugin_url'             => $woocommerce->plugin_url(),
			'ajax_url'               => admin_url( 'admin-ajax.php' ),
			'calendar_image'         => $woocommerce->plugin_url() . '/assets/images/calendar.png',
		);

		wp_localize_script( 'rq_events_writepanel_js', 'rq_events_writepanel_js_params', $params );
	}

	/**
	 * Add ticket type
	 */
	public function add_event_ticket() {
		global $woocommerce;

		check_ajax_referer( 'add-event-ticket', 'security' );

		$post_id = intval( $_POST['post_id'] );
		$loop    = intval( $_POST['loop'] );

		$ticket_type = array(
			'post_title'   => sprintf( __( 'Ticket Type #%d', 'redq-events' ), ( $loop + 1 ) ),
			'post_content' => '',
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id(),
			'post_parent'  => $post_id,
			'post_type'    => 'event_ticket',
			'menu_order'   => $loop
		);

		$ticket_type_id = wp_insert_post( $ticket_type );

		if ( $ticket_type_id ) {
			$ticket_type = get_post( $ticket_type_id );

			include( 'views/html-event-ticket.php' );
		}

		die();
	}

	/**
	 * Remove ticket type
	 */
	public function remove_event_ticket() {
		check_ajax_referer( 'delete-event-ticket', 'security' );
		$ticket_type_id = intval( $_POST['ticket_id'] );
		$ticket_type    = get_post( $ticket_type_id );

		if ( $ticket_type && 'event_ticket' == $ticket_type->post_type ) {
			wp_delete_post( $ticket_type_id );
		}

		die();
	}

	/**
	 * Save Event data for the product
	 *
	 * @param  int $post_id
	 */
	public function save_product_data( $post_id ) {
		global $wpdb;

		$product_type         = empty( $_POST['product-type'] ) ? 'simple' : sanitize_title( stripslashes( $_POST['product-type'] ) );
		$has_additional_costs = false;

		if ( 'event' !== $product_type ) {
			return;
		}

		// Save meta
		$meta_to_save = array(
			'_rq_event_start_date'   => '',
			'_rq_event_start_time'   => '',
			'_rq_event_stop_time'    => '',
			'_rq_event_country_name' => '',
			'_rq_event_region_name'  => '',
			'_rq_event_address_name' => '',
			'_rq_event_zip_code'     => '',
			'_rq_event_lat_name'     => '',
			'_rq_event_lon_name'     => '',
		);

		foreach ( $meta_to_save as $meta_key => $sanitize ) {
			$value = ! empty( $_POST[ $meta_key ] ) ? $_POST[ $meta_key ] : '';
			switch ( $sanitize ) {
				case 'int' :
					$value = absint( $value );
					break;
				case 'float' :
					$value = floatval( $value );
					break;
				case 'yesno' :
					$value = $value == 'yes' ? 'yes' : 'no';
					break;
				case 'issetyesno' :
					$value = $value ? 'yes' : 'no';
					break;
				case 'max_date' :
					$value = absint( $value );
					if ( $value == 0 )
						$value = 1;
					break;
				default :
					$value = sanitize_text_field( $value );
			}
			update_post_meta( $post_id, $meta_key, $value );
		}

		// Ticket Types
		if ( isset( $_POST['ticket_id'] ) ) {
			$ticket_ids         = $_POST['ticket_id'];
			$ticket_menu_order  = $_POST['ticket_menu_order'];
			$ticket_name        = $_POST['ticket_name'];
			$ticket_cost        = $_POST['ticket_cost'];

			$max_loop = max( array_keys( $_POST['ticket_id'] ) );

			for ( $i = 0; $i <= $max_loop; $i ++ ) {
				if ( ! isset( $ticket_ids[ $i ] ) ) {
					continue;
				}

				$ticket_id = absint( $ticket_ids[ $i ] );

				if ( empty( $ticket_name[ $i ] ) ) {
					$ticket_name[ $i ] = sprintf( __( 'Ticket Type #%d', 'redq-events' ), ( $i + 1 ) );
				}

				$wpdb->update(
					$wpdb->posts,
					array(
						'post_title'   => stripslashes( $ticket_name[ $i ] ),
						'menu_order'   => $ticket_menu_order[ $i ] ),
					array(
						'ID' => $ticket_id
					),
					array(
						'%s',
						'%d'
					),
					array( '%d' )
				);

				update_post_meta( $ticket_id, 'cost', woocommerce_clean( $ticket_cost[ $i ] ) );
			}
		} // end if

		update_post_meta( $post_id, '_regular_price', '' );
		update_post_meta( $post_id, '_sale_price', '' );

		// Set price so filters work
		update_post_meta( $post_id, '_price', '' );
	}

}

new RQ_Events_Admin();