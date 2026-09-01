<?php
/**
 * صفحه تک‌محصول
 *
 * @package arian-shop
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	global $product;

	$arian_gallery = $product ? $product->get_gallery_image_ids() : array();
	$arian_main_id = $product ? $product->get_image_id() : 0;
	?>

	<div class="container sp-layout">
		<?php do_action( 'woocommerce_before_main_content' ); ?>

		<article id="product-<?php the_ID(); ?>" <?php post_class( 'single-product' ); ?>>

			<div class="sp-gallery" data-sp-gallery>
				<div class="sp-mainimg">
					<?php if ( $arian_main_id ) : ?>
						<?php echo wp_get_attachment_image( $arian_main_id, 'large', false, array( 'class' => 'sp-img sp-img-active', 'data-src' => wp_get_attachment_image_url( $arian_main_id, 'full' ) ) ); ?>
					<?php else : ?>
						<span class="sp-noimg"><?php echo arian_icon( 'box' ); ?></span>
					<?php endif; ?>
					<?php echo wp_kses_post( arian_discount_badge( $product ) ); ?>
				</div>
				<?php if ( $arian_gallery ) : ?>
					<div class="sp-thumbs">
						<?php if ( $arian_main_id ) : ?>
							<button class="sp-thumb is-active" data-full="<?php echo esc_url( wp_get_attachment_image_url( $arian_main_id, 'full' ) ); ?>">
								<?php echo wp_get_attachment_image( $arian_main_id, 'thumbnail' ); ?>
							</button>
						<?php endif; ?>
						<?php foreach ( $arian_gallery as $arian_gid ) : ?>
							<button class="sp-thumb" data-full="<?php echo esc_url( wp_get_attachment_image_url( $arian_gid, 'full' ) ); ?>">
								<?php echo wp_get_attachment_image( $arian_gid, 'thumbnail' ); ?>
							</button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="sp-summary">
				<?php
				/**
				 * woocommerce_before_single_product_summary hook.
				 *
				 * @hooked woocommerce_show_product_sale_flash - 10
				 */
				do_action( 'woocommerce_before_single_product_summary' );
				?>

				<div class="sp-summary-head">
					<?php
					woocommerce_template_single_title();
					woocommerce_template_single_rating();
					woocommerce_template_single_price();
					?>
				</div>

				<div class="sp-excerpt">
					<?php if ( $product->get_short_description() ) : ?>
						<?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?>
					<?php endif; ?>
				</div>

				<div class="sp-cart-area">
					<?php woocommerce_template_single_add_to_cart(); ?>
					<?php if ( $product->is_type( 'simple' ) && $product->is_in_stock() ) : ?>
						<a class="btn btn-buynow" href="<?php echo esc_url( add_query_arg( array( 'buy-now' => $product->get_id() ), wc_get_checkout_url() ) ); ?>">
							<?php echo arian_icon( 'sparkle' ); ?> خرید فوری
						</a>
					<?php endif; ?>
				</div>

				<ul class="sp-meta">
					<li><span>کد کالا:</span> <strong><?php echo esc_html( $product->get_sku() ? $product->get_sku() : '—' ); ?></strong></li>
					<li><span>دسته‌بندی:</span> <?php echo wp_kses_post( wc_get_product_category_list( $product->get_id(), '، ' ) ); ?></li>
					<?php
					$arian_brand_terms = get_the_terms( $product->get_id(), 'pa_brand' );
					if ( $arian_brand_terms && ! is_wp_error( $arian_brand_terms ) ) :
						?>
						<li><span>برند:</span> <?php echo esc_html( implode( '، ', wp_list_pluck( $arian_brand_terms, 'name' ) ) ); ?></li>
					<?php endif; ?>
				</ul>

				<div class="sp-share">
					<span>اشتراک‌گذاری:</span>
					<a href="https://t.me/share/url?url=<?php echo esc_url( rawurlencode( get_permalink() ) ); ?>&text=<?php echo esc_attr( $product->get_name() ); ?>" target="_blank" rel="noopener"><?php echo arian_icon( 'telegram' ); ?></a>
					<a href="https://wa.me/?text=<?php echo esc_url( rawurlencode( $product->get_name() . ' — ' . get_permalink() ) ); ?>" target="_blank" rel="noopener"><?php echo arian_icon( 'whatsapp' ); ?></a>
					<a href="https://www.instagram.com/" target="_blank" rel="noopener"><?php echo arian_icon( 'instagram' ); ?></a>
				</div>

				<?php do_action( 'woocommerce_single_product_summary' ); ?>
			</div>
		</article>

		<?php do_action( 'woocommerce_after_single_product_summary' ); ?>

		<?php do_action( 'woocommerce_after_main_content' ); ?>
	</div>

	<?php
endwhile;

get_footer();
