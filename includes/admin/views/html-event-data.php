<div class="options_group show_if_event">
	<div class="toolbar">
		<h3><?php _e( 'Event Date Time', 'redq-events' ); ?></h3>
	</div>

	<p class="form-field _rq_event_start_date_field">
		<label for="_rq_event_start_date"><?php _e( 'Date starts at...', 'redq-events' ); ?></label>
		<input type="text" name="_rq_event_start_date" id="_rq_event_start_date" value="<?php echo get_post_meta( $post_id, '_rq_event_start_date', true ); ?>" placeholder="YYYY-MM-DD" />
	</p>
	<p class="form-field _rq_event_start_time_field">
		<label for="_rq_event_start_time"><?php _e( 'Time starts at...', 'redq-events' ); ?></label>
		<input type="time" name="_rq_event_start_time" id="_rq_event_start_time" value="<?php echo get_post_meta( $post_id, '_rq_event_start_time', true ); ?>" placeholder="HH:MM" />
	</p>
	<p class="form-field _rq_event_stop_time_field">
		<label for="_rq_event_stop_time"><?php _e( 'Time stops at...', 'redq-events' ); ?></label>
		<input type="time" name="_rq_event_stop_time" id="_rq_event_stop_time" value="<?php echo get_post_meta( $post_id, '_rq_event_stop_time', true ); ?>" placeholder="HH:MM" />
	</p>

	<script type="text/javascript">
		jQuery('._tax_status_field').closest('.show_if_simple').addClass('show_if_event');
		jQuery('.inventory_tab').addClass('show_if_event');
		jQuery('#inventory_product_data').addClass('show_if_event');
	</script>
</div>

<div class="options_group show_if_event">
	<div class="toolbar">
		<h3><?php _e( 'Event Location', 'redq-events' ); ?></h3>
	</div>
	
	<p class="form-field _rq_event_country_name_field">
		<label for="_rq_event_country_name"><?php _e( 'Country name', 'redq-events' ); ?></label>
		<input type="text" name="_rq_event_country_name" id="_rq_event_country_name" value="<?php echo get_post_meta( $post_id, '_rq_event_country_name', true ); ?>" />
	</p>
	<p class="form-field _rq_event_region_name_field">
		<label for="_rq_event_region_name"><?php _e( 'Region name', 'redq-events' ); ?></label>
		<input type="text" name="_rq_event_region_name" id="_rq_event_region_name" value="<?php echo get_post_meta( $post_id, '_rq_event_region_name', true ); ?>" />
	</p>
	<p class="form-field _rq_event_region_name_field">
		<label for="_rq_event_address_name"><?php _e( 'Address name', 'redq-events' ); ?></label>
		<input type="text" name="_rq_event_address_name" id="_rq_event_address_name" value="<?php echo get_post_meta( $post_id, '_rq_event_address_name', true ); ?>" />
	</p>
	<p class="form-field _rq_event_zip_code_field">
		<label for="_rq_event_zip_code"><?php _e( 'Zip code', 'redq-events' ); ?></label>
		<input type="text" name="_rq_event_zip_code" id="_rq_event_zip_code" value="<?php echo get_post_meta( $post_id, '_rq_event_zip_code', true ); ?>" />
	</p>
	<?php woocommerce_wp_checkbox( array( 'id' => '_redq_event_convert_zip', 'label' => __( 'Click to lat & long', 'redq-events' ), 'description' => __( 'Type any field to your address and click to convert to get google map location', 'redq-events' ), 'desc_tip' => true, 'value' => get_post_meta( $post_id, '_redq_event_convert_zip', true ) ) ); ?>
	<div id="convert_gps_log"></div>
	<p class="form-field _rq_event_lat_name_field">
		<label for="_rq_event_lat_name"><?php _e( 'Latitude', 'redq-events' ); ?></label>
		<input type="text" name="_rq_event_lat_name" id="_rq_event_lat_name" value="<?php echo get_post_meta( $post_id, '_rq_event_lat_name', true ); ?>" />
	</p>
	<p class="form-field _rq_event_lon_name_field">
		<label for="_rq_event_lon_name"><?php _e( 'Longitude', 'redq-events' ); ?></label>
		<input type="text" name="_rq_event_lon_name" id="_rq_event_lon_name" value="<?php echo get_post_meta( $post_id, '_rq_event_lon_name', true ); ?>" />
	</p>

	<div id="map_canvas"></div>
</div>