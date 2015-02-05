<div id="events_tickets" class="woocommerce_options_panel panel wc-metaboxes-wrapper">

	<div class="options_group" id="tickets-types">

		<div class="toolbar">
			<h3><?php _e( 'Ticket types', 'redq-events' ); ?></h3>
			<a href="#" class="close_all"><?php _e( 'Close all', 'redq-events' ); ?></a><a href="#" class="expand_all"><?php _e( 'Expand all', 'redq-events' ); ?></a>
		</div>

		<div class="rq_events_tickets wc-metaboxes">

			<?php
				global $post;

				$ticket_types = get_posts( array(
					'post_type'      => 'event_ticket',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'orderby'        => 'menu_order',
					'order'          => 'asc',
					'post_parent'    => $post->ID
				) );

				if ( sizeof( $ticket_types ) == 0 ) {
					echo '<div id="message" class="inline woocommerce-message" style="margin: 1em 0;">';
						echo '<div class="squeezer">';
							echo '<h4>' . __( 'Ticket types allow you to offer different costs for different types of individuals, for Regular, V.I.P and Elite.', 'redq-events' ) . '</h4>';
						echo '</div>';
					echo '</div>';
				}

				if ( $ticket_types ) {
					$loop = 0;

					foreach ( $ticket_types as $ticket_type ) {
						$ticket_type_id = absint( $ticket_type->ID );
						include( 'html-event-ticket.php' );
						$loop++;
					}
				}
			?>
		</div>

		<p class="toolbar">
			<button type="button" class="button button-primary add_ticket"><?php _e( 'Add Ticket Type', 'redq-events' ); ?></button>
		</p>
	</div>
</div>
