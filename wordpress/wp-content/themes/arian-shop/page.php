<?php
/**
 * قالب صفحه ثابت
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
		<div class="page-head">
			<h1 class="page-title"><?php the_title(); ?></h1>
		</div>
		<div class="page-content prose">
			<?php the_content(); ?>
		</div>
	<?php endwhile; ?>
</div>
<?php get_footer(); ?>
