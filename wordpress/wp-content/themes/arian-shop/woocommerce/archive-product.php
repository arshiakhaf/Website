<?php
/**
 * صفحه فروشگاه (آرشیو محصولات)
 *
 * @package arian-shop
 */

defined( 'ABSPATH' ) || exit;

get_header();

$arian_title = woocommerce_page_title( false );
$arian_is_sale = isset( $_GET['on_sale'] ); // phpcs:ignore WordPress.Security.NonceVerification
?>
<div class="page-head shop-head">
	<div class="container">
		<a class="crumb-home" href="<?php echo esc_url( home_url( '/' ) ); ?>">خانه</a>
		<span class="crumb-sep">/</span>
		<span><?php echo esc_html( $arian_is_sale ? 'تخفیف‌ها' : $arian_title ); ?></span>
	</div>
</div>

<div class="container shop-layout">

	<aside class="shop-sidebar" id="shop-sidebar">
		<div class="sidebar-title">
			<h3>فیلترها</h3>
			<button class="sidebar-close" data-close-filters aria-label="بستن"><?php echo arian_icon( 'x' ); ?></button>
		</div>

		<?php if ( is_active_sidebar( 'shop-sidebar' ) ) : ?>
			<?php dynamic_sidebar( 'shop-sidebar' ); ?>
		<?php else : ?>
			<div class="widget">
				<h3 class="widget-title">دسته‌بندی‌ها</h3>
				<ul class="filter-list">
					<?php
					$arian_terms = get_terms(
						array(
							'taxonomy'   => 'product_cat',
							'hide_empty' => true,
						)
					);
					if ( ! is_wp_error( $arian_terms ) ) {
						foreach ( $arian_terms as $arian_term ) {
							echo '<li><a href="' . esc_url( get_term_link( $arian_term ) ) . '">'
								. esc_html( $arian_term->name )
								. ' <span class="count">' . esc_html( arian_fa_digits( $arian_term->count ) ) . '</span></a></li>';
						}
					}
					?>
				</ul>
			</div>
			<div class="widget widget-sale">
				<h3 class="widget-title">فروش ویژه</h3>
				<a class="sale-link" href="<?php echo esc_url( add_query_arg( 'on_sale', '1', $arian_title ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/shop/' ) ) ); ?>">
					<?php echo arian_icon( 'flame' ); ?> محصولات تخفیف‌دار
				</a>
			</div>
			<?php if ( shortcode_exists( 'woocommerce_maybe_show_product_attributes' ) ) : ?>
				<?php echo do_shortcode( '[woocommerce_product_filter_price]' ); ?>
			<?php endif; ?>
		<?php endif; ?>
	</aside>

	<div class="shop-main">
		<div class="shop-toolbar">
			<h1 class="shop-title"><?php echo esc_html( $arian_is_sale ? 'تخفیف‌های ویژه' : $arian_title ); ?></h1>
			<div class="toolbar-actions">
				<?php woocommerce_result_count(); ?>
				<?php woocommerce_catalog_ordering(); ?>
			</div>
		</div>

		<?php do_action( 'woocommerce_before_main_content' ); ?>

		<?php if ( woocommerce_product_loop() ) : ?>

			<?php do_action( 'woocommerce_before_shop_loop' ); ?>

			<?php woocommerce_product_loop_start(); ?>

			<?php
			while ( have_posts() ) :
				the_post();
				wc_get_template_part( 'content', 'product' );
			endwhile;
			?>

			<?php woocommerce_product_loop_end(); ?>

			<?php do_action( 'woocommerce_after_shop_loop' ); ?>

		<?php else : ?>
			<div class="empty-shop">
				<?php echo arian_icon( 'search' ); ?>
				<p>محصولی مطابق انتخاب شما پیدا نشد.</p>
				<a class="btn btn-primary" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">مشاهده همه محصولات</a>
			</div>
		<?php endif; ?>

		<?php do_action( 'woocommerce_after_main_content' ); ?>
	</div>

	<button class="mobile-filters-btn" data-open-filters><?php echo arian_icon( 'menu' ); ?> فیلترها</button>
</div>

<?php
get_footer();
