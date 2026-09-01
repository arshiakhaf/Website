<?php
/**
 * قالب پیش‌فرض (فهرست نوشته‌ها)
 *
 * @package arian-shop
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="container page-layout">
	<div class="page-main">
		<?php arian_page_header( get_the_archive_title() ?: get_the_archive_description(), 'وبلاگ آرین‌شاپ' ); ?>

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
							<div class="post-meta">
								<span><?php echo arian_icon( 'clock' ); ?> <?php echo esc_html( get_the_date() ); ?></span>
								<span><?php echo esc_html( get_the_author() ); ?></span>
							</div>
							<h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
							<a class="post-more" href="<?php the_permalink(); ?>">ادامه مطلب <?php echo arian_icon( 'chev-left' ); ?></a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>

			<div class="pagination-wrap">
				<?php the_posts_pagination( array( 'mid_size' => 2, 'prev_text' => 'قبلی', 'next_text' => 'بعدی' ) ); ?>
			</div>
		<?php else : ?>
			<div class="empty-box"><?php echo arian_icon( 'search' ); ?><p>محتوایی پیدا نشد.</p></div>
		<?php endif; ?>
	</div>
	<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
