<?php
/*
Plugin Name: RedQ Events
Plugin URI: https://github.com/RedQ/RedQ-Events
Description: Event Management System. Buy sell event tickets online with WooCommerce
Version: 1.0.0
Author: Faysal Haque
Author URI: http://faysalhaque.github.io/
License: GPLv2 or later
Domain Path: /languages
Text Domain: redq-events
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	/**
	 * RQ Events class
	 */
	class RQ_Events {

		/**
		 * Constructor
		 */
		public function __construct() {
			define( 'RQ_EVENTS_VERSION', '1.0.0' );
			define( 'RQ_EVENTS_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );
			define( 'RQ_EVENTS_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
			define( 'RQ_EVENTS_MAIN_FILE', __FILE__ );
			
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'event_form_styles' ) );

			if ( is_admin() ) {
				$this->admin_includes();
			}

			add_action( 'woocommerce_loaded', array( $this, 'includes' ) );
			add_action( 'init', array( $this, 'init_post_types' ) );

			// Init core classes
			include( 'includes/class-rq-events-cart.php' );
			include( 'includes/class-rq-events-ajax.php' );
		}

		/**
		 * Load Classes
		 */
		public function includes() {
			include( 'includes/rq-events-functions.php' );
			include( 'includes/class-wc-product-event.php' );
			include( 'includes/class-rq-event-form.php' );
			include( 'includes/class-rq-events-widgets.php' );
		}


		/**
		 * Include admin
		 */
		public function admin_includes() {
			include( 'includes/admin/class-rq-events-admin.php' );
		}

		/**
		 * Localisation
		 */
		public function load_plugin_textdomain() {
			$locale = apply_filters( 'plugin_locale', get_locale(), 'redq-events' );
			$dir    = trailingslashit( WP_LANG_DIR );

			load_textdomain( 'redq-events', $dir . 'redq-events/redq-events-' . $locale . '.mo' );
			load_plugin_textdomain( 'redq-events', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Init post types
		 */
		public function init_post_types() {
			register_post_type( 'event_ticket',
				apply_filters( 'redq_register_post_type_event_ticket',
					array(
						'label'        => __( 'Ticket Type', 'redq-events' ),
						'public'       => true,
						'hierarchical' => false,
						'supports'     => false
					)
				)
			);
		}

		/**
		 * Frontend event form scripts
		 */
		public function event_form_styles() {
			wp_enqueue_style( 'rq-events-styles', RQ_EVENTS_PLUGIN_URL . '/assets/css/frontend.css', null, RQ_EVENTS_VERSION );
		}
	}

	$GLOBALS['rq_events'] = new RQ_Events();

} else {
    function rq_events_admin_notice() { ?>
        <div class="error">
            <p><?php _e( 'Please Install WooCommerce First before activating this Plugin. You can download WooCommerce from <a href="http://wordpress.org/plugins/woocommerce/">here</a>.', 'redq-events' ); ?></p>
        </div>
    <?php }

    add_action( 'admin_notices', 'rq_events_admin_notice' );
}