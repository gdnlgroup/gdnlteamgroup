<?php
/**
 * Template Name: Affecter Template
 * Description: A Page Template that showcases Sticky Posts, Asides, and Blog Posts
 *
 * The showcase template in Twenty Eleven consists of a featured posts section using sticky posts,
 * another recent posts area (with the latest post shown in full and the rest as a list)
 * and a left sidebar holding aside posts.
 *
 * We are creating two queries to fetch the proper posts and a custom widget for the sidebar.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

// Enqueue showcase script for the slider
wp_enqueue_script( 'twentyeleven-showcase', get_template_directory_uri() . '/js/showcase.js', array( 'jquery' ), '2011-04-28' );?>


<?php

	 
get_header(); ?>

		<div id="primary" class="showcase">
			<div id="content" role="main">
                        
                <div>
                        <?php echo do_shortcode('[wpuf_dashboardaffecter]'); ?>
                </div>
			</div><!-- #content -->
		</div><!-- #primary -->
                        
<?php get_footer(); ?>