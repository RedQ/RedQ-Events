<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="rq_event_ticket wc-metabox closed">
	<h3>
		<button type="button" class="remove_event_ticket button" rel="<?php echo esc_attr( $ticket_type_id ); ?>"><?php _e( 'Remove', 'redq-events' ); ?></button>
		<div class="handlediv" title="<?php _e( 'Click to toggle', 'redq-events' ); ?>"></div>

		<strong>#<?php echo esc_html( $ticket_type_id ); ?> &mdash; <span class="ticket_name"><?php echo $ticket_type->post_title; ?></span></strong>

		<input type="hidden" name="ticket_id[<?php echo $loop; ?>]" value="<?php echo esc_attr( $ticket_type_id ); ?>" />
		<input type="hidden" class="ticket_menu_order" name="ticket_menu_order[<?php echo $loop; ?>]" value="<?php echo $loop; ?>" />
	</h3>
	<table cellpadding="0" cellspacing="0" class="wc-metabox-content">
		<tbody>
			<tr>
				<td>
					<label><?php _e( 'Ticket Type Name', 'redq-events' ); ?>:</label>
					<input type="text" class="short ticket_name" name="ticket_name[<?php echo $loop; ?>]" value="<?php echo esc_attr( $ticket_type->post_title ); ?>" placeholder="<?php _e( 'Ticket Type #', 'redq-events' ) . $loop; ?>" />
				</td>
				<td>
					<label><?php _e( 'Base Cost', 'redq-events' ); ?>:</label>
					<input type="number" class="short" name="ticket_cost[<?php echo $loop; ?>]" value="<?php echo esc_attr( $ticket_type->cost ); ?>" placeholder="0.00" step="0.01" />
				</td>
			</tr>
		</tbody>
	</table>
</div>
