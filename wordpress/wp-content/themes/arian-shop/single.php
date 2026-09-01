<?php
/**
 * قالب تک‌نوشته
 *
 * @package arian-shop
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="container page-single">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class( 'single-post' ); ?>>
			<div class="page-head">
				<h1 class="page-title"><?php the_title(); ?></h1>
				<div class="post-meta single-meta">
					<span><?php echo arian_icon( 'clock' ); ?> <?php echo esc_html( get_the_date() ); ?></span>
					<span><?php echo arian_icon( 'user' ); ?> <?php the_author(); ?></span>
				</div>
			</div>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="single-featured"><?php the_post_thumbnail( 'large' ); ?></div>
			<?php endif; ?>
			<div class="page-content prose">
				<?php the_content(); ?>
			</div>
			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</article>
	<?php endwhile; ?>
</div>
<?php get_footer(); ?>
