<?php
/**
 * Event form class
 */
class RQ_Event_Form {

	/**
	 * Event product data.
	 * @var WC_Product_Event
	 */
	public $product;

	/**
	 * Event fields.
	 * @var array
	 */
	private $fields;

	/**
	 * Constructor
	 * @param $product WC_Product_Event
	 */
	public function __construct( $product ) {
		$this->product = $product;
	}

	/**
	 * Event form scripts
	 */
	public function scripts() {
		global $wp_locale, $woocommerce;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'rq-events-google-map-form', 'https://maps.google.com/maps/api/js?sensor=false', array( 'jquery' ), RQ_EVENTS_VERSION, true );
		wp_enqueue_script( 'rq-events-richmarker', RQ_EVENTS_PLUGIN_URL . '/assets/js/richmarker' . $suffix . '.js', array( 'jquery' ), RQ_EVENTS_VERSION, true );
		
		wp_enqueue_script( 'rq-events-event-form', RQ_EVENTS_PLUGIN_URL . '/assets/js/event-form' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'rq-events-richmarker' ), RQ_EVENTS_VERSION, true );

		// Variables for JS scripts
		$event_form_params = array(
			'ajax_url'              => $woocommerce->ajax_url(),
			'ajax_loader_url'       => apply_filters( 'woocommerce_ajax_loader_url', $woocommerce->plugin_url() . '/assets/images/ajax-loader@2x.gif' ),
			'i18n_date_unavailable' => __( 'This date is unavailable', 'redq-events' ),
			'map_marker'            => $this->get_map_marker()
		);

		wp_localize_script( 'rq-events-event-form', 'event_form_params', apply_filters( 'event_form_params', $event_form_params ) );
	}

	/**
	 * Prepare fields for the event form
	 */
	public function prepare_fields() {
		// Destroy existing fields
		$this->reset_fields();

		// Add fields in order
		$this->ticket_field();
	}

	/**
	 * Reset fields array
	 */
	public function reset_fields() {
		$this->fields = array();
	}

	/**
	 * Get map marker info
	 * @return array
	 */
	public function get_map_marker() {
		return array(
			'latitude' => $this->product->get_event_lat_name(),
			'longitude' => $this->product->get_event_lon_name(),
			'html' => $this->product->get_event_address_name(),
		);
	}

	/**
	 * Add tickets field
	 */
	private function ticket_field() {
		// Tickets field
		$ticket_types = $this->product->get_ticket_types_data();
		$ticket_options = array();
		
		foreach ( $ticket_types as $ticket_type ) {
			$ticket_options[]	= array(
				'id'   => $ticket_type->id,
				'name' => $ticket_type->name,
				'cost' => $ticket_type->cost,
			);
		}

		$this->add_field( array(
			'type'    => 'ticket-form',
			'name'    => 'ticket_type',
			'label'   => __( 'Ticket Type', 'redq-events' ),
			'options' => $ticket_options
		) );
	}

	/**
	 * Add address field
	 */
	private function address_field() {

		$this->add_field( array(
			'type'  => 'address',
			'name'  => 'address_name',
			'label'    => $this->product->make_event_address()
		) );
	}

	/**
	 * Add Field
	 * @param  array $field
	 * @return void
	 */
	public function add_field( $field ) {
		$default = array(
			'name'  => '',
			'class' => array(),
			'label' => '',
			'type'  => 'text'
		);

		$field = wp_parse_args( $field, $default );

		if ( ! $field['name'] || ! $field['type'] ) {
			return;
		}

		$nicename = 'rq_event_field_' . sanitize_title( $field['name'] );

		$field['name']    = $nicename;
		$field['class'][] = $nicename;

		$this->fields[ sanitize_title( $field['name'] ) ] = $field;
	}

	public function output() {
		$this->scripts();
		$this->prepare_fields();

		foreach ( $this->fields as $key => $field ) {
			
			woocommerce_get_template( 'event-form/' . $field['type'] . '.php', array( 'field' => $field ), 'redq-events', RQ_EVENTS_TEMPLATE_PATH );
		}
	}

	/**
	 * Get posted form data into a neat array
	 * @param  array $posted
	 * @return array
	 */
	public function get_posted_data( $posted = array() ) {
		if ( empty( $posted ) ) {
			$posted = $_POST;
		}

		$data = array(
			'_ticket_id'    => '',
			'_ticket_name'  => '',
			'_base_cost'    => '',
			'_event_date'   => '',
			'_event_time'   => '',
			'_quantity'     => '',
		);

		if ( ! empty( $posted['quantity'] ) && ! empty( $posted['ticket_name'] ) && ! empty( $posted['base_cost'] ) ){
			$data['_ticket_id']  = absint( $posted['ticket_name'] );
			$data['_event_date'] = $this->product->get_event_date();
			$data['_event_time'] = $this->product->get_event_time();
			$data['_quantity']   = absint( $posted['quantity'] );
			$data['_base_cost']  = $this->product->get_ticket_base_cost($data['_ticket_id']);
			$data['event_date']  = $data['_event_date'];
			$data['event_time']  = $data['_event_time'];
			$data['type']        = get_the_title( absint( $posted['ticket_name'] ) );
		}

		return $data;
	}

	/**
	 * Checks ticket data is correctly set, and that the chosen blocks are indeed available.
	 *
	 * @param  array $data
	 * @return WP_Error on failure, true on success
	 */
	public function is_passed_validation( $data ) {
		if ( empty( $data['_ticket_id'] ) ) {
			return new WP_Error( 'Error', __( 'Please choose a ticket type', 'redq-events' ) );
		}

		if ( empty( $data['_quantity'] ) ) {
			return new WP_Error( 'Error', __( 'Please select a ticket spots', 'redq-events' ) );
		}

		$is_valid_ticket_id = $this->product->is_valid_ticket_id( $data['_ticket_id'] );

		if ( $is_valid_ticket_id === false ) {
			return new WP_Error( 'Error', __( 'No ticket type found', 'redq-events' ) );	
		}

		return true;
	}

	/**
	 * Calculate costs from posted values
	 * @param  array $posted
	 * @return string cost
	 */
	public function calculate_ticket_cost( $posted ) {
		if ( ! empty( $this->ticket_cost ) ) {
			return $this->ticket_cost;
		}

		$data         = $this->get_posted_data( $posted );

		// Dynamic base costs
		if ( isset( $data['_ticket_id'] ) && isset( $data['_quantity'] ) ) {

			$base_cost          = $this->product->get_ticket_base_cost($data['_ticket_id']);

			$ticket_cost = $base_cost;
			
		}
		

		$this->ticket_cost = $ticket_cost;

		return apply_filters( 'event_form_calculated_ticket_cost', $this->ticket_cost, $this, $posted );

	}
}