<?php
/**
 * نظرات
 *
 * @package arian-shop
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<div id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h3 class="comments-title">
			<?php
			$arian_count = get_comments_number();
			/* translators: %s: تعداد نظرات */
			printf( esc_html__( 'نظرات (%s)', 'arian-shop' ), esc_html( arian_fa_digits( $arian_count ) ) );
			?>
		</h3>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>

	<?php comment_form(); ?>
</div>
