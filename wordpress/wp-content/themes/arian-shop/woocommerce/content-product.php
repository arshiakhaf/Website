<?php
/**
 * کارت محصول در فهرست‌ها
 *
 * @package arian-shop
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$arian_link      = $product->get_permalink();
$arian_title     = $product->get_name();
$arian_img_id    = $product->get_image_id();
$arian_gallery   = $product->get_gallery_image_ids();
$arian_hover_img = $arian_gallery ? $arian_gallery[0] : 0;
$arian_type      = $product->is_type( 'simple' ) && $product->is_in_stock() ? 'simple' : ( $product->is_type( 'variable' ) ? 'variable' : 'other' );
?>
<li <?php post_class( 'product-item' ); ?>>

	<div class="pi-media">
		<a class="pi-thumb" href="<?php echo esc_url( $arian_link ); ?>" aria-label="<?php echo esc_attr( $arian_title ); ?>">
			<?php if ( $arian_img_id ) : ?>
				<?php echo wp_get_attachment_image( $arian_img_id, 'arian-card', false, array( 'class' => 'pi-img pi-img-main' ) ); ?>
			<?php else : ?>
				<span class="pi-noimg"><?php echo arian_icon( 'box' ); ?></span>
			<?php endif; ?>
			<?php if ( $arian_hover_img ) : ?>
				<?php echo wp_get_attachment_image( $arian_hover_img, 'arian-card', false, array( 'class' => 'pi-img pi-img-hover' ) ); ?>
			<?php endif; ?>
		</a>

		<?php echo wp_kses_post( arian_discount_badge( $product ) ); ?>

		<?php if ( ! $product->is_in_stock() ) : ?>
			<span class="badge badge-stock">ناموجود</span>
		<?php endif; ?>

		<div class="pi-actions">
			<button class="pi-wish" data-wishlist="<?php echo esc_attr( $product->get_id() ); ?>" aria-label="افزودن به علاقه‌مندی‌ها" title="علاقه‌مندی">
				<?php echo arian_icon( 'heart' ); ?>
			</button>
			<?php if ( 'simple' === $arian_type ) : ?>
				<a href="<?php echo esc_url( '?add-to-cart=' . $product->get_id() ); ?>"
				   data-quantity="1" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
				   data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
				   class="button pi-quick product_type_simple add_to_cart_button ajax_add_to_cart"
				   rel="nofollow" aria-label="<?php echo esc_attr( $arian_title ); ?>">
					<?php echo arian_icon( 'cart' ); ?>
				</a>
			<?php elseif ( 'variable' === $arian_type ) : ?>
				<a href="<?php echo esc_url( $arian_link ); ?>" class="pi-quick product_type_variable" aria-label="<?php echo esc_attr( $arian_title ); ?>">
					<?php echo arian_icon( 'cart' ); ?>
				</a>
			<?php else : ?>
				<a href="<?php echo esc_url( $arian_link ); ?>" class="pi-quick" aria-label="مشاهده محصول">
					<?php echo arian_icon( 'eye' ); ?>
				</a>
			<?php endif; ?>
			<button class="pi-search" data-quickview="<?php echo esc_attr( $product->get_id() ); ?>" aria-label="پیش‌نمایش سریع" title="پیش‌نمایش سریع">
				<?php echo arian_icon( 'eye' ); ?>
			</button>
		</div>
	</div>

	<div class="pi-body">
		<?php
		$arian_cats = wc_get_product_category_list( $product->get_id(), ', ' );
		if ( $arian_cats ) :
			?>
			<span class="pi-cat"><?php echo wp_kses_post( $arian_cats ); ?></span>
		<?php endif; ?>

		<h3 class="pi-title"><a href="<?php echo esc_url( $arian_link ); ?>"><?php echo esc_html( $arian_title ); ?></a></h3>

		<div class="pi-rating">
			<?php echo wp_kses_post( wc_get_rating_html( $product->get_average_rating() ) ); ?>
			<span class="pi-reviews">(<?php echo esc_html( arian_fa_digits( $product->get_review_count() ) ); ?>)</span>
		</div>

		<div class="pi-bottom">
			<div class="pi-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
			<?php if ( 'simple' === $arian_type ) : ?>
				<a href="<?php echo esc_url( '?add-to-cart=' . $product->get_id() ); ?>"
				   data-quantity="1" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
				   class="button pi-add add_to_cart_button ajax_add_to_cart" rel="nofollow">
					<?php echo arian_icon( 'cart' ); ?><span><?php esc_html_e( 'افزودن به سبد', 'arian-shop' ); ?></span>
				</a>
			<?php else : ?>
				<a href="<?php echo esc_url( $arian_link ); ?>" class="button pi-add"><?php esc_html_e( 'انتخاب گزینه', 'arian-shop' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</li>
