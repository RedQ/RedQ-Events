<?php
// Creating the widget 
class rq_events_upcoming_events extends WP_Widget {

	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'rq_events_upcoming_events', 

			// Widget name will appear in UI
			__('RedQ: Upcoming Events', 'redq-events'), 

			// Widget description
			array( 'description' => __( 'Show upcoming events widget', 'redq-events' ), ) 
		);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$no_of_events = $instance[ 'no_of_events' ];
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title']; ?>
			<ul class="rq-events-upcoming-events events-widget">
							
			<?php

			$args = array(
	            'post_type' => 'product',
	            'posts_per_page' => $no_of_events,
	            'orderby' => 'meta_value',
	            'order' => 'ASC',
	            'meta_query' => array(
	                    array(
	                        'key' => '_rq_event_start_date',
	                        'value' => date('Y-m-d'),
	                        'compare' => '>='
	                    )                   

	                ),

	        ); 
	        $upcoming_events = new WP_Query($args);

			while ( $upcoming_events->have_posts() ) : $upcoming_events->the_post(); ?>

				<li>
					<a href="<?php the_permalink(); ?>">
						<?php the_title(); ?>
						<?php _e(' (', 'redq-events'); ?>
						<?php echo esc_attr( date( get_option('date_format'), strtotime( get_post_meta( get_the_ID(), '_rq_event_start_date', true ) ) ) ); ?>
						<?php _e(' )', 'redq-events'); ?>
					</a>
				</li>

			<?php endwhile; ?>
			</ul> 
		<?php echo $args['after_widget'];
	}
		
	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) && isset( $instance[ 'no_of_events' ] ) ) {
			$title = $instance[ 'title' ];
			$no_of_events = $instance[ 'no_of_events' ];
		}
		else {
			$title = __( 'Upcoming Events', 'redq-events' );
			$no_of_events = 4;
		}
	// Widget admin form
	?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'no_of_events' ); ?>"><?php _e( 'Number of events:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'no_of_events' ); ?>" name="<?php echo $this->get_field_name( 'no_of_events' ); ?>" type="number" value="<?php echo esc_attr( $no_of_events ); ?>" />
	</p>
	<?php
	}
	
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
	$instance = array();
	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	$instance['no_of_events'] = ( ! empty( $new_instance['no_of_events'] ) ) ? strip_tags( $new_instance['no_of_events'] ) : '';
	return $instance;
}
} // Class rq_events_upcoming_events ends here

// Register and load the widget
function rq_events_upcoming_events() {
	register_widget( 'rq_events_upcoming_events' );
}
add_action( 'widgets_init', 'rq_events_upcoming_events' );