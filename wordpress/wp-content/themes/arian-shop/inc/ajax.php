<?php
/**
 * درخواست‌های AJAX قالب (جستجوی زنده + پیش‌نمایش سریع محصول)
 *
 * @package arian-shop
 */

defined( 'ABSPATH' ) || exit;

/**
 * جستجوی زنده محصولات
 */
add_action( 'wp_ajax_arian_search', 'arian_ajax_search' );
add_action( 'wp_ajax_nopriv_arian_search', 'arian_ajax_search' );
function arian_ajax_search() {
	check_ajax_referer( 'arian_nonce', 'nonce' );

	$term = isset( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';
	if ( mb_strlen( $term ) < 2 ) {
		wp_send_json_success( array( 'items' => array() ) );
	}

	$products = array();
	if ( function_exists( 'wc_get_products' ) ) {
		$products = wc_get_products(
			array(
				's'     => $term,
				'limit' => 8,
				'status' => 'publish',
			)
		);
	}

	$items = array();
	foreach ( $products as $product ) {
		$items[] = array(
			'id'    => $product->get_id(),
			'title' => $product->get_name(),
			'url'   => $product->get_permalink(),
			'price' => wp_strip_all_tags( $product->get_price_html() ),
			'img'   => wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' ),
		);
	}

	wp_send_json_success( array( 'items' => $items ) );
}

/**
 * پیش‌نمایش سریع محصول
 */
add_action( 'wp_ajax_arian_quick_view', 'arian_ajax_quick_view' );
add_action( 'wp_ajax_nopriv_arian_quick_view', 'arian_ajax_quick_view' );
function arian_ajax_quick_view() {
	check_ajax_referer( 'arian_nonce', 'nonce' );

	$product_id = isset( $_GET['product_id'] ) ? absint( $_GET['product_id'] ) : 0;
	$product    = $product_id ? wc_get_product( $product_id ) : null;

	if ( ! $product ) {
		wp_send_json_error( array( 'message' => 'محصول پیدا نشد.' ) );
	}

	ob_start();
	do_action( 'arian_quick_view_content', $product );
	$html = ob_get_clean();

	wp_send_json_success( array( 'html' => $html ) );
}

/**
 * رندر محتوای پیش‌نمایش سریع
 */
add_action( 'arian_quick_view_content', 'arian_quick_view_render' );
function arian_quick_view_render( $product ) {
	$img   = wp_get_attachment_image( $product->get_image_id(), 'arian-card' );
	$price = $product->get_price_html();
	?>
	<div class="qv-media">
		<?php echo wp_kses_post( $img ); ?>
		<?php echo wp_kses_post( arian_discount_badge( $product ) ); ?>
	</div>
	<div class="qv-body">
		<h3 class="qv-title"><?php echo esc_html( $product->get_name() ); ?></h3>
		<div class="qv-rating">
			<?php echo wp_kses_post( wc_get_rating_html( $product->get_average_rating() ) ); ?>
			<span class="qv-reviews"><?php echo esc_html( arian_fa_digits( $product->get_review_count() ) ); ?> نظر</span>
		</div>
		<div class="qv-price"><?php echo wp_kses_post( $price ); ?></div>
		<div class="qv-excerpt"><?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?></div>
		<div class="qv-actions">
			<?php if ( $product->is_type( 'simple' ) && $product->is_in_stock() ) : ?>
				<a href="<?php echo esc_url( '?add-to-cart=' . $product->get_id() ); ?>"
				   data-quantity="1" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
				   class="button ajax_add_to_cart add_to_cart_button qv-add"><?php echo esc_html( arian_add_to_cart_text( 'افزودن به سبد', $product ) ); ?></a>
			<?php else : ?>
				<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="button qv-view">مشاهده کامل</a>
			<?php endif; ?>
		</div>
		<a class="qv-more" href="<?php echo esc_url( $product->get_permalink() ); ?>">صفحه محصول <i class="arian-icon" data-icon="chev-left"></i></a>
	</div>
	<?php
}
