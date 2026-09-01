<?php
/**
 * صفحه اصلی فروشگاه
 *
 * @package arian-shop
 */

defined( 'ABSPATH' ) || exit;

get_header();

$arian_wc    = function_exists( 'WC' );
$hero_img_id = absint( arian_opt( 'arian_hero_image', 0 ) );
$arian_shop  = $arian_wc ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

/**
 * تیتر بخش صفحه اصلی
 */
function arian_section_head( $title, $sub = '', $more = '' ) {
	echo '<div class="section-head"><div class="section-head-text">';
	echo '<h2 class="section-title">' . esc_html( $title ) . '</h2>';
	if ( $sub ) {
		echo '<p class="section-sub">' . esc_html( $sub ) . '</p>';
	}
	echo '</div>';
	if ( $more ) {
		echo '<a class="section-more" href="' . esc_url( $more ) . '">مشاهده همه ' . arian_icon( 'chev-left' ) . '</a>';
	}
	echo '</div>';
}
?>

<!-- ===================== بنر اصلی ===================== -->
<section class="hero">
	<div class="container hero-inner">
		<div class="hero-copy">
			<span class="hero-badge"><?php echo esc_html( arian_opt( 'arian_hero_badge', '🔥 تخفیف ویژه پاییزی تا ۴۰٪' ) ); ?></span>
			<h1 class="hero-title"><?php echo esc_html( arian_opt( 'arian_hero_title', 'خرید هوشمند، با یک کلیک' ) ); ?></h1>
			<p class="hero-sub"><?php echo esc_html( arian_opt( 'arian_hero_subtitle', 'جدیدترین کالاهای دیجیتال و خانگی با گارانتی اصالت و ارسال سریع' ) ); ?></p>
			<div class="hero-actions">
				<a class="btn btn-primary btn-lg" href="<?php echo esc_url( arian_opt( 'arian_hero_btn_url', $arian_shop ) ); ?>">
					<?php echo esc_html( arian_opt( 'arian_hero_btn', 'مشاهده فروشگاه' ) ); ?>
					<?php echo arian_icon( 'arrow-left' ); ?>
				</a>
				<?php if ( $arian_wc ) : ?>
					<a class="btn btn-ghost btn-lg" href="<?php echo esc_url( add_query_arg( 'on_sale', '1', $arian_shop ) ); ?>"><?php echo arian_icon( 'flame' ); ?> تخفیف‌های ویژه</a>
				<?php endif; ?>
			</div>
			<div class="hero-stats">
				<div><strong>+۱۲هزار</strong><span>کالای متنوع</span></div>
				<div><strong>%۹۸</strong><span>رضایت مشتری</span></div>
				<div><strong>۷/۲۴</strong><span>پشتیبانی</span></div>
			</div>
		</div>
		<div class="hero-media">
			<?php if ( $hero_img_id ) : ?>
				<?php echo wp_get_attachment_image( $hero_img_id, 'arian-hero', false, array( 'class' => 'hero-img' ) ); ?>
			<?php else : ?>
				<img src="<?php echo esc_url( arian_asset( 'assets/img/hero.jpg' ) ); ?>" alt="فروشگاه اینترنتی آرین‌شاپ" class="hero-img">
			<?php endif; ?>
			<div class="hero-float float-1">
				<span class="float-ico"><?php echo arian_icon( 'truck' ); ?></span>
				<div><strong>ارسال سریع</strong><small>به سراسر کشور</small></div>
			</div>
			<div class="hero-float float-2">
				<span class="float-ico"><?php echo arian_icon( 'shield' ); ?></span>
				<div><strong>ضمانت اصالت</strong><small>کالای اورجینال</small></div>
			</div>
		</div>
	</div>
</section>

<?php if ( $arian_wc ) : ?>

<!-- ===================== دسته‌بندی‌ها ===================== -->
<section class="home-section categories-section" id="categories">
	<div class="container">
		<?php arian_section_head( 'خرید بر اساس دسته‌بندی', 'هر آنچه نیاز دارید، یکجا' ); ?>
		<div class="cat-circles">
			<?php
			$arian_cats = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => true,
					'number'     => 10,
				)
			);
			if ( ! is_wp_error( $arian_cats ) ) :
				foreach ( $arian_cats as $arian_cat ) :
					$arian_thumb_id = get_term_meta( $arian_cat->term_id, 'thumbnail_id', true );
					$arian_thumb    = $arian_thumb_id ? wp_get_attachment_image( $arian_thumb_id, 'thumbnail' ) : '<span class="cat-circle-fallback">' . arian_icon( 'sparkle' ) . '</span>';
					?>
					<a class="cat-circle" href="<?php echo esc_url( get_term_link( $arian_cat ) ); ?>">
						<span class="cat-circle-img"><?php echo wp_kses_post( $arian_thumb ); ?></span>
						<span class="cat-circle-name"><?php echo esc_html( $arian_cat->name ); ?></span>
					</a>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</section>

<!-- ===================== پیشنهاد ویژه ===================== -->
<?php $arian_featured = arian_get_products( 'featured', 8 ); ?>
<?php if ( $arian_featured ) : ?>
<section class="home-section featured-section">
	<div class="container">
		<?php arian_section_head( 'پیشنهاد ویژه آرین‌شاپ', 'منتخب پرفروش‌ترین کالاها', $arian_shop ); ?>
		<div class="product-row" data-slider>
			<?php foreach ( $arian_featured as $arian_featured_product ) : ?>
				<?php $GLOBALS['product'] = $arian_featured_product; ?>
				<?php wc_get_template_part( 'content', 'product' ); ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- ===================== تخفیف‌های شگفت‌انگیز ===================== -->
<?php $arian_deals = arian_get_products( 'sale', 6 ); ?>
<?php if ( $arian_deals ) : ?>
<section class="home-section deals-section">
	<div class="container">
		<div class="deals-head">
			<div class="deals-title">
				<span class="deals-flame"><?php echo arian_icon( 'flame' ); ?></span>
				<div>
					<h2>تخفیف‌های شگفت‌انگیز</h2>
					<p>تا پایان این بازه زمانی فرصت دارید</p>
				</div>
			</div>
			<div class="countdown" data-countdown="<?php echo esc_attr( time() + ( 2 * DAY_IN_SECONDS ) ); ?>">
				<span class="cd-box"><strong data-cd-days>۰</strong><small>روز</small></span>
				<i>:</i>
				<span class="cd-box"><strong data-cd-hours>۰</strong><small>ساعت</small></span>
				<i>:</i>
				<span class="cd-box"><strong data-cd-mins>۰</strong><small>دقیقه</small></span>
				<i>:</i>
				<span class="cd-box"><strong data-cd-secs>۰</strong><small>ثانیه</small></span>
			</div>
		</div>
		<div class="product-row products-deals" data-slider>
			<?php foreach ( $arian_deals as $arian_deal_product ) : ?>
				<?php $GLOBALS['product'] = $arian_deal_product; ?>
				<?php wc_get_template_part( 'content', 'product' ); ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- ===================== بنرهای تبلیغاتی ===================== -->
<section class="home-section banners-section">
	<div class="container banners-grid">
		<a class="banner banner-1" href="<?php echo esc_url( $arian_shop ); ?>">
			<span class="banner-tag">دنیای دیجیتال</span>
			<strong>ابزارهای هوشمند<br>برای زندگی مدرن</strong>
			<span class="banner-cta">مشاهده محصولات <?php echo arian_icon( 'chev-left' ); ?></span>
		</a>
		<a class="banner banner-2" href="<?php echo esc_url( $arian_shop ); ?>">
			<span class="banner-tag">خانه و آشپزخانه</span>
			<strong>راحتی و زیبایی<br>در هر گوشه خانه</strong>
			<span class="banner-cta">مشاهده محصولات <?php echo arian_icon( 'chev-left' ); ?></span>
		</a>
	</div>
</section>

<!-- ===================== جدیدترین محصولات ===================== -->
<?php $arian_new = arian_get_products( 'recent', 8 ); ?>
<?php if ( $arian_new ) : ?>
<section class="home-section newest-section">
	<div class="container">
		<?php arian_section_head( 'جدیدترین محصولات', 'تازه‌های فروشگاه آرین‌شاپ', $arian_shop ); ?>
		<div class="product-grid products-home">
			<?php foreach ( $arian_new as $arian_new_product ) : ?>
				<?php $GLOBALS['product'] = $arian_new_product; ?>
				<?php wc_get_template_part( 'content', 'product' ); ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php endif; // wc ?>

<!-- ===================== مزیت‌ها ===================== -->
<section class="home-section features-section">
	<div class="container features-grid">
		<div class="feature">
			<span class="feature-ico"><?php echo arian_icon( 'truck' ); ?></span>
			<div><strong>ارسال سریع رایگان</strong><p>برای خریدهای بالای ۵۰۰ هزار تومان</p></div>
		</div>
		<div class="feature">
			<span class="feature-ico"><?php echo arian_icon( 'shield' ); ?></span>
			<div><strong>ضمانت اصالت کالا</strong><p>تضمین اورجینال بودن همه محصولات</p></div>
		</div>
		<div class="feature">
			<span class="feature-ico"><?php echo arian_icon( 'credit' ); ?></span>
			<div><strong>پرداخت امن</strong><p>درگاه بانکی و پرداخت در محل</p></div>
		</div>
		<div class="feature">
			<span class="feature-ico"><?php echo arian_icon( 'headset' ); ?></span>
			<div><strong>پشتیبانی ۷/۲۴</strong><p>پاسخگویی تلفنی، واتساپ و ایمیل</p></div>
		</div>
	</div>
</section>

<!-- ===================== نظرات مشتریان ===================== -->
<section class="home-section testimonials-section">
	<div class="container">
		<?php arian_section_head( 'نظرات مشتریان', 'تجربه خرید شما برای ما ارزشمند است' ); ?>
		<div class="testi-row" data-slider>
			<figure class="testi-card">
				<div class="testi-stars"><?php echo arian_icon( 'star' ); ?><?php echo arian_icon( 'star' ); ?><?php echo arian_icon( 'star' ); ?><?php echo arian_icon( 'star' ); ?><?php echo arian_icon( 'star' ); ?></div>
				<blockquote>«کیفیت هدفون عالیه و بسته‌بندی خیلی تمیز بود. برای اولین خریدم از آرین‌شاپ، تجربه‌ی خوبی داشتم.»</blockquote>
				<figcaption><span class="testi-avatar">س</span><div><strong>سارا احمدی</strong><small>خریدار هدفون S9 Pro</small></div></figcaption>
			</figure>
			<figure class="testi-card">
				<div class="testi-stars"><?php echo arian_icon( 'star' ); ?><?php echo arian_icon( 'star' ); ?><?php echo arian_icon( 'star' ); ?><?php echo arian_icon( 'star' ); ?><?php echo arian_icon( 'star' ); ?></div>
				<blockquote>«کفش ورزشی را با تخفیف خریدم؛ هم قیمت منصفانه بود هم ارسال سریع. حتماً دوباره خرید می‌کنم.»</blockquote>
				<figcaption><span class="testi-avatar">م</span><div><strong>مریم کریمی</strong><small>خریدار کفش Breeze</small></div></figcaption>
			</figure>
			<figure class="testi-card">
				<div class="testi-stars"><?php echo arian_icon( 'star' ); ?><?php echo arian_icon( 'star' ); ?><?php echo arian_icon( 'star' ); ?><?php echo arian_icon( 'star' ); ?><?php echo arian_icon( 'star' ); ?></div>
				<blockquote>«پشتیبانی واتساپ خیلی سریع جواب داد و جاروبرقی رباتیک دقیقاً مثل توضیحات بود. ممنون از شما.»</blockquote>
				<figcaption><span class="testi-avatar">ن</span><div><strong>نگار حسینی</strong><small>خریدار جاروبرقی S5</small></div></figcaption>
			</figure>
		</div>
	</div>
</section>

<!-- ===================== بلاگ ===================== -->
<?php
$arian_posts = get_posts(
	array(
		'posts_per_page' => 3,
		'post_status'    => 'publish',
	)
);
?>
<?php if ( $arian_posts ) : ?>
<section class="home-section blog-section">
	<div class="container">
		<?php arian_section_head( 'خواندنی‌های آرین‌شاپ', 'راهنمای خرید و تازه‌های دنیای تکنولوژی', get_permalink( get_page_by_path( 'blog' ) ?: 0 ) ); ?>
		<div class="blog-grid">
			<?php foreach ( $arian_posts as $arian_post ) : ?>
				<article class="blog-card">
					<a class="blog-thumb" href="<?php echo esc_url( get_permalink( $arian_post ) ); ?>">
						<?php if ( has_post_thumbnail( $arian_post ) ) : ?>
							<?php echo get_the_post_thumbnail( $arian_post, 'medium_large' ); ?>
						<?php else : ?>
							<span class="blog-thumb-fallback"><?php echo arian_icon( 'sparkle' ); ?></span>
						<?php endif; ?>
					</a>
					<div class="blog-body">
						<span class="blog-date"><?php echo esc_html( get_the_date( 'j F Y', $arian_post ) ); ?></span>
						<h3 class="blog-title"><a href="<?php echo esc_url( get_permalink( $arian_post ) ); ?>"><?php echo esc_html( get_the_title( $arian_post ) ); ?></a></h3>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt( $arian_post ), 14 ) ); ?></p>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- ===================== خبرنامه ===================== -->
<section class="home-section newsletter-section">
	<div class="container newsletter-inner">
		<div class="newsletter-copy">
			<span class="newsletter-ico"><?php echo arian_icon( 'gift' ); ?></span>
			<div>
				<h2>خبرنامه آرین‌شاپ</h2>
				<p><?php echo esc_html( arian_opt( 'arian_newsletter_text', 'از تخفیف‌ها زودتر باخبر شوید؛ خبرنامه آرین‌شاپ را دنبال کنید.' ) ); ?></p>
			</div>
		</div>
		<form class="newsletter-form" data-newsletter>
			<input type="email" name="email" placeholder="ایمیل خود را وارد کنید..." required>
			<button type="submit" class="btn btn-light">عضویت</button>
		</form>
	</div>
</section>

<?php get_footer(); ?>
