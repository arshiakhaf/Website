<?php
/**
 * نتایج جستجو
 *
 * @package arian-shop
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="container page-layout">
	<div class="page-main">
		<div class="page-head">
			<h1 class="page-title">نتایج جستجو: «<?php echo esc_html( get_search_query() ); ?>»</h1>
		</div>
		<?php if ( have_posts() ) : ?>
			<div class="post-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class( 'post-card' ); ?>>
						<a class="post-thumb" href="<?php the_permalink(); ?>">
							<?php
							if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'medium_large' );
							} else {
								echo '<span class="post-thumb-fallback">' . arian_icon( 'sparkle' ) . '</span>';
							}
							?>
						</a>
						<div class="post-body">
							<h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
							<a class="post-more" href="<?php the_permalink(); ?>">ادامه مطلب <?php echo arian_icon( 'chev-left' ); ?></a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<div class="pagination-wrap">
				<?php the_posts_pagination( array( 'prev_text' => 'قبلی', 'next_text' => 'بعدی' ) ); ?>
			</div>
		<?php else : ?>
			<div class="empty-box">
				<?php echo arian_icon( 'search' ); ?>
				<p>نتیجه‌ای برای جستجوی شما پیدا نشد.</p>
				<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">بازگشت به خانه</a>
			</div>
		<?php endif; ?>
	</div>
	<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
