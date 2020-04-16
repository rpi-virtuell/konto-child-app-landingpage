<?php
/**
 * Template Name: Login
 */

get_header(); ?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<?php
			while ( have_posts() ) : the_post();

				?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php
					/**
					 * Before Page entry content
					 *
					 * @hooked app_landing_page_page_content_image
					 */
					do_action( 'app_landing_page_before_page_entry_content' );
					?>
                    <div class="text-holder">
                        <div class="entry-content">
							<?php
							the_content();
							wp_login_form('/profile');
							?>
                        </div><!-- .entry-content -->
                    </div>
                    <footer class="entry-footer">
						<?php
						edit_post_link(
							sprintf(
							/* translators: %s: Name of current post */
								esc_html__( 'Edit %s', 'app-landing-page' ),
								the_title( '<span class="screen-reader-text">"', '"</span>', false )
							),
							'<span class="edit-link">',
							'</span>'
						);
						?>
                    </footer><!-- .entry-footer -->
                </article>
                <?php

			endwhile; // End of the loop.
			?>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
