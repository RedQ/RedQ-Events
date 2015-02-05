<?php
/**
 * Event product add to cart
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $woocommerce, $product;

if ( ! $product->is_purchasable() ) {
	return;
}

$product->the_event_date_time();
$product->the_event_address(); ?>

<div id="single_map_canvas"></div>
<?php
do_action( 'woocommerce_before_add_to_cart_form' ); ?>
<noscript><?php _e( 'Your browser must support JavaScript in order to buy a ticket.', 'redq-events' ); ?></noscript>
<form class="cart" method="post" enctype='multipart/form-data'>
	<?php $event_form->output(); ?>
  <?php
    // Availability
    $availability = $product->get_availability();

    if ( $availability['availability'] )
      echo apply_filters( 'woocommerce_stock_html', '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>', $availability['availability'] );
  ?>
	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
	<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->id ); ?>" />
	<button type="submit" class="single_add_to_cart_button button alt"><?php echo $product->single_add_to_cart_text(); ?></button>
	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
</form>
<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>