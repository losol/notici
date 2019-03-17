<?php
/**
 * The template for displaying event archive
 *
 * @package Notici
 * @since 1.0.0
 */
get_header();
?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main">

		<?php
		if ( have_posts() ) :
			?>

			<header class="page-header">
				<?php
					the_archive_title( '<h1 class="page-title">', '</h1>' );
				?>
			</header><!-- .page-header -->

			<?php
			// Start the Loop.
			$args = [
				'post_status'    => 'publish',
				'post_type'      => 'notici',
				'posts_per_page' => 50,
				'orderby'        => 'publish_date',
				'order'          => 'DESC',
			];

			$posts         = new WP_Query( $args );
			while ( $posts->have_posts() ) {
			?>
			<article id="event-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="entry-content">
				<?php
				$posts->the_post();
				$post_id = get_the_id();
				$post = get_post( $post_id );
				$content = $post->post_content;

				if ( is_sticky() && is_home() && ! is_paged() ) {
					printf( '<span class="sticky-post">%s</span>', _x( 'Featured', 'post', 'notici' ) );
				}
				echo '<b>' . get_the_title() . '</b>';
				echo $post->post_content;
				echo '<a href="' . esc_url( get_permalink() ) . '" class="event-details-link">' . 'Link' . '</a>';


				?>
				</div>
			</article>
			<?php
			}
			wp_reset_postdata();

			// If no content, include the "No posts found" template.
		else :
			?>
			<section class="no-results not-found">
				<header class="page-header">
					<h1 class="page-title"><?php _e( 'No notices yet!', 'notici' ); ?></h1>
				</header><!-- .page-header -->

				<div class="page-content">
					<?php
					if ( current_user_can( 'publish_posts' ) ) :

						printf(
							'<p>' . wp_kses(
								/* translators: 1: link to WP admin new post page. */
								__( 'Ready to publish your first event? <a href="%1$s">Get started here</a>.', 'notici' ),
								array(
									'a' => array(
										'href' => array(),
									),
								)
							) . '</p>',
							esc_url( admin_url( 'post-new.php?post_type=notici' ) )
						);
					else :
						?>

						<p><?php _e( 'It seems we can&rsquo;t find any notices.', 'notici' ); ?></p>
						<?php

					endif;
					?>
				</div><!-- .page-content -->
			</section><!-- .no-results -->
			<?php
		endif;
		?>
		</main><!-- #main -->
	</section><!-- #primary -->

<?php
get_footer();
