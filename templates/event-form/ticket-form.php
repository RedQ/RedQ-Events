<?php global $product; if ( $product->is_not_expired() ) : extract( $field ); ?>
	<table class="rq_events_tickets_table">
		<thead>
			<tr>
				<th><?php _e('Ticket type', 'redq-events'); ?></th>
				<th><?php _e('Spots', 'redq-events'); ?></th>
				<th><?php _e('Total', 'redq-events'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<select id="ticket_type" name="ticket_name">
						<option><?php _e('Please select ticket', 'redq-events'); ?></option>
						<?php foreach ($options as $option) : ?>
							<?php echo apply_filters( 'redq_events_ticket_type_options', sprintf( '<option value="%d">%s ( %s%.2f )</option>', $option["id"], $option["name"], get_woocommerce_currency_symbol(), $option["cost"] ), $option["id"] ); ?>
						<?php endforeach; ?>
					</select>		
				</td>
				<td>
					<div id="results"></div>
					<?php
			 			woocommerce_quantity_input( array(
			 				'min_value' => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
			 				'max_value' => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product )
			 			) );
				 	?>
				</td>
				<td>
					<div id="total_cost" style="display: none;"></div>
				</td>
			</tr>
		</tbody>
	</table>
<?php else : ?>
	<?php _e('Event is expired', 'redq-events'); ?>
<?php endif; ?>