<?php

/**
 * Convert key to a nice readable label
 * @param  string $key
 * @return string
 */
function get_rq_event_data_label( $key, $product ) {
	switch ( $key ) {
		case "type" :
			return __( 'Ticket type', 'redq-events' );
		case "event_date" :
			return __( 'Event date', 'redq-events' );
		case "event_time" :
			return __( 'Event time', 'redq-events' );
		case "no_of_ticket" :
			return __( 'No of ticket(s)', 'redq-events' );
		default :
			return $key;
	}
}