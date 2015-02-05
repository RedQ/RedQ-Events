<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for the event product type
 */
class WC_Product_Event extends WC_Product {

	/**
	 * Constructor
	 */
	public function __construct( $product ) {
		$this->product_type = 'event';
		parent::__construct( $product );
	}

    /**
	 * Events can always be purchased regardless of price
	 * @return bool
	 */
	public function is_purchasable() {
        return true;
	}

    public function get_ticket_base_cost($post_id) {
        return get_post_meta($post_id, 'cost', true);
    }

	/**
     * Get all ticket types
     * @return array of WP_Post objects
     */
    public function get_ticket_types() {
        $tickets = get_posts( array(
            'post_parent'    => $this->id,
            'post_type'      => 'event_ticket',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'asc'
        ) );

        return $tickets;
    }

    /**
     * Get the event date
     * @return string
     */
    public function get_event_date() {

    	return date( get_option('date_format'), strtotime($this->rq_event_start_date) );
    }

    /**
     * Display the event date and time for the current product.
     */
    public function the_event_date_time() {
        $css_class = 'rq-events-date-time';
        $event_date  = $this->get_event_date();
        $event_time  = $this->get_event_time();

        echo apply_filters( 'rq_events_the_event_date_time_html', sprintf( '<p class="%s">%s ' . __('@', 'redq-events') . ' %s</p>', $css_class, $event_date, $event_time ), $event_date, $event_time, $this->id, $css_class );
    }

    /**
     * Display the event address for the current product.
     */
    public function the_event_address() {
        $css_class = 'rq-events-address';
        $event_address  = $this->make_event_address();

        echo apply_filters( 'rq_events_the_event_address_html', sprintf( '<p class="%s">' . __('%s', 'redq-events') . '</p>', $css_class, $event_address ), $event_address, $this->id, $css_class );
    }

    /**
     * Check the event date expiray
     * @return bool
     */
    public function is_not_expired() {
        $event_date = strtotime($this->rq_event_start_date);

        $current_date = time();

        if( $current_date > $event_date ) {
            return false;
        }

        return true;
    }

    /**
     * Get the event start time
     * @return string
     */
    public function get_event_start_time() {

        return $this->rq_event_start_time;
    }

    /**
     * Get the event end time
     * @return string
     */
    public function get_event_end_time() {

        return $this->rq_event_stop_time;

    }

    public function get_event_time() {
        
        $start_time = $this->get_event_start_time();
        $end_time = $this->get_event_end_time();

        $time_format = get_option('time_format');

        if( !empty($start_time) && !empty($end_time)) {
            
            $event_time = sprintf( '%s - %s', date( $time_format, strtotime( $start_time ) ), date( $time_format, strtotime( $end_time ) ) );

        } elseif( !empty($start_time) && empty($end_time)) {

            $event_time = sprintf('%s',date( $time_format, strtotime( $start_time ) ) );

        } elseif( empty($start_time) && !empty($end_time)) {

            $event_time = sprintf('%s',date( $time_format, strtotime( $end_time ) ) );
            
        } else {
            $event_time = __('all day', 'redq-events');
        }

        return $event_time;
    }

    /**
     * Get the event country name
     * @return string
     */
    public function get_event_country_name() {
    	return $this->rq_event_country_name;
    }

    /**
     * Get the event region name
     * @return string
     */
    public function get_event_region_name() {
    	return $this->rq_event_region_name;
    }

    /**
     * Get the event address name
     * @return string
     */
    public function get_event_address_name() {
    	return $this->rq_event_address_name;
    }

    /**
     * Get the event zip code
     * @return string
     */
    public function get_event_zip_code() {
    	return $this->rq_event_zip_code;
    }

    /**
     * Get the event lat name
     * @return string
     */
    public function get_event_lat_name() {
    	return $this->rq_event_lat_name;
    }

    /**
     * Get the event lon name
     * @return string
     */
    public function get_event_lon_name() {
    	return $this->rq_event_lon_name;
    }

    /**
     * Make the event full address
     * @return string
     */
    public function make_event_address() {

        $address = $this->get_event_address_name() . ', ';
        $address .= $this->get_event_region_name() . ', ';
        $address .= $this->get_event_country_name() . ' - ';
        $address .= $this->get_event_zip_code();
    	   
        return $address;
    }

    /**
     * Check the selected ticket_id is valid
     * @return bool
     */
    public function is_valid_ticket_id( $ticket_id ) {
        
        $parent_id = wp_get_post_parent_id($ticket_id);

        if($parent_id === $this->id) {
            return true;
        }

        return false;
    }

    /**
     * Get the object of the ticket
     * @return object
     */
    public function get_ticket_types_data() {
        $ticket_type = array();
            
        $tickets = $this->get_ticket_types();

        foreach ($tickets as $ticket) {

            $ticket_type[] = (object) array(
				'id'          => $ticket->ID,
				'name'        => $ticket->post_title,
				'cost'        => $ticket->cost,
            );
        }

        return $ticket_type;
    }
}