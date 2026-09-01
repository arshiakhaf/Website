<?php
/**
 * هدر سایت
 *
 * @package arian-shop
 */

defined( 'ABSPATH' ) || exit;

$arian_contact = arian_contact_data();
$arian_wc      = function_exists( 'WC' );
$arian_shop_url  = $arian_wc ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
$arian_cart_url  = $arian_wc ? wc_get_cart_url() : home_url( '/cart/' );
$arian_acc_url   = $arian_wc ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#content">پرش به محتوا</a>

<header class="site-header" id="site-header">

	<!-- نوار بالایی -->
	<div class="topbar">
		<div class="container topbar-inner">
			<div class="topbar-contact">
				<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $arian_contact['phone'] ) ); ?>">
					<?php echo arian_icon( 'phone' ); ?><span><?php echo esc_html( $arian_contact['phone'] ); ?></span>
				</a>
				<a href="mailto:<?php echo esc_attr( $arian_contact['email'] ); ?>">
					<?php echo arian_icon( 'mail' ); ?><span><?php echo esc_html( $arian_contact['email'] ); ?></span>
				</a>
			</div>
			<nav class="topbar-links" aria-label="منوی بالا">
				<?php if ( is_user_logged_in() ) : ?>
					<a class="topbar-user" href="<?php echo esc_url( $arian_acc_url ); ?>">
						<?php echo arian_icon( 'user' ); ?>
						<span><?php echo esc_html( wp_get_current_user()->display_name ); ?></span>
					</a>
					<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'خروج', 'arian-shop' ); ?></a>
				<?php else : ?>
					<a href="<?php echo esc_url( $arian_acc_url ); ?>"><?php esc_html_e( 'ورود / ثبت‌نام', 'arian-shop' ); ?></a>
				<?php endif; ?>
				<a href="<?php echo esc_url( home_url( '/order-tracking/' ) ); ?>"><?php esc_html_e( 'پیگیری سفارش', 'arian-shop' ); ?></a>
			</nav>
		</div>
	</div>

	<!-- نوار اصلی -->
	<div class="mainbar">
		<div class="container mainbar-inner">
			<button class="menu-toggle" aria-label="باز کردن منو" aria-expanded="false" data-open-menu>
				<?php echo arian_icon( 'menu' ); ?>
			</button>

			<div class="site-logo">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo-link" aria-label="آرین‌شاپ">
						<span class="logo-mark" aria-hidden="true">آ</span>
						<span class="logo-text">آرین<span>‌شاپ</span></span>
					</a>
				<?php endif; ?>
			</div>

			<form role="search" method="get" class="header-search" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-searchbox>
				<input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>"
					   placeholder="جستجوی محصول، برند یا دسته..." autocomplete="off" data-search-input>
				<button type="submit" aria-label="جستجو"><?php echo arian_icon( 'search' ); ?></button>
				<div class="search-results" data-search-results></div>
			</form>

			<div class="header-actions">
				<a class="head-act act-wishlist" href="<?php echo esc_url( $arian_shop_url ); ?>" title="علاقه‌مندی‌ها" data-wishlist-link>
					<?php echo arian_icon( 'heart' ); ?>
					<span class="act-badge wl-count" data-wl-count>۰</span>
				</a>
				<a class="head-act" href="<?php echo esc_url( $arian_acc_url ); ?>" title="حساب کاربری">
					<?php echo arian_icon( 'user' ); ?>
				</a>
				<button class="head-act cart-btn" data-toggle-cart aria-expanded="false" title="سبد خرید">
					<?php echo arian_icon( 'cart' ); ?>
					<span class="act-badge cart-count" data-cart-count><?php echo esc_html( arian_fa_digits( $arian_wc && WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ) ); ?></span>
				</button>
				<div class="cart-mini" data-cart-panel hidden>
					<div class="cart-mini-head">
						<span>سبد خرید</span>
						<button class="cart-mini-close" data-close-cart aria-label="بستن"><?php echo arian_icon( 'x' ); ?></button>
					</div>
					<div class="widget_shopping_cart_content">
						<?php if ( $arian_wc ) { woocommerce_mini_cart(); } ?>
					</div>
				</div>
			</div>
		</div>

		<!-- منوی اصلی -->
		<nav class="primary-nav" aria-label="منوی اصلی">
			<div class="container nav-inner">
				<div class="nav-categories" data-cats>
					<button class="cat-toggle" data-toggle-cats>
						<?php echo arian_icon( 'menu' ); ?>
						<span>دسته‌بندی محصولات</span>
						<?php echo arian_icon( 'chev-down' ); ?>
					</button>
					<div class="cats-panel" data-cats-panel hidden>
						<?php
						if ( function_exists( 'get_terms' ) && taxonomy_exists( 'product_cat' ) ) {
							$arian_cats = get_terms(
								array(
									'taxonomy'   => 'product_cat',
									'hide_empty' => false,
									'number'     => 8,
								)
							);
							if ( ! is_wp_error( $arian_cats ) ) {
								foreach ( $arian_cats as $arian_cat ) {
									$arian_cat_img = get_term_meta( $arian_cat->term_id, 'thumbnail_id', true );
									echo '<a class="cat-item" href="' . esc_url( get_term_link( $arian_cat ) ) . '">';
									if ( $arian_cat_img ) {
										echo wp_get_attachment_image( $arian_cat_img, 'thumbnail', false, array( 'class' => 'cat-thumb' ) );
									}
									echo '<span class="cat-name">' . esc_html( $arian_cat->name ) . '</span>';
									echo arian_icon( 'chev-left' );
									echo '</a>';
								}
							}
						}
						?>
					</div>
				</div>

				<ul class="nav-menu">
					<li><a class="nav-home" href="<?php echo esc_url( home_url( '/' ) ); ?>">خانه</a></li>
					<li><a href="<?php echo esc_url( $arian_shop_url ); ?>">فروشگاه</a></li>
					<li><a href="<?php echo esc_url( add_query_arg( 'on_sale', '1', $arian_shop_url ) ); ?>" class="nav-hot">تخفیف‌ها <?php echo arian_icon( 'flame' ); ?></a></li>
					<li><a href="<?php echo esc_url( $arian_shop_url . '#categories' ); ?>">دسته‌بندی‌ها</a></li>
					<?php
					if ( has_nav_menu( 'primary' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'primary',
								'container'      => false,
								'items_wrap'     => '%3$s',
								'depth'          => 2,
								'fallback_cb'    => false,
							)
						);
					}
					?>
				</ul>

				<div class="nav-support">
					<span class="support-label">پشتیبانی ۷/۲۴</span>
					<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $arian_contact['phone'] ) ); ?>">
						<strong><?php echo esc_html( $arian_contact['phone'] ); ?></strong>
					</a>
				</div>
			</div>
		</nav>
	</div>
</header>

<!-- منوی موبایل -->
<div class="mobile-menu" data-mobile-menu hidden>
	<div class="mobile-menu-head">
		<span class="logo-mark" aria-hidden="true">آ</span>
		<strong>آرین‌شاپ</strong>
		<button class="mobile-close" data-close-menu aria-label="بستن منو"><?php echo arian_icon( 'x' ); ?></button>
	</div>
	<div class="mobile-menu-body">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'mobile-links',
				'fallback_cb'    => false,
			)
		);
		?>
		<div class="mobile-cats">
			<h4>دسته‌بندی‌ها</h4>
			<?php
			if ( taxonomy_exists( 'product_cat' ) ) {
				$arian_mcats = get_terms(
					array(
						'taxonomy'   => 'product_cat',
						'hide_empty' => false,
						'number'     => 12,
					)
				);
				if ( ! is_wp_error( $arian_mcats ) ) {
					foreach ( $arian_mcats as $arian_mcat ) {
						echo '<a href="' . esc_url( get_term_link( $arian_mcat ) ) . '">' . esc_html( $arian_mcat->name ) . '</a>';
					}
				}
			}
			?>
		</div>
		<a class="mobile-cta" href="<?php echo esc_url( add_query_arg( 'on_sale', '1', $arian_shop_url ) ); ?>"><?php echo arian_icon( 'flame' ); ?> مشاهده تخفیف‌ها</a>
	</div>
</div>
<div class="overlay" data-overlay hidden></div>

<main id="content" class="site-main">
