<?php
/**
 * The Template for displaying single posts 
 * of post-type portfolio 
 *
 */

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="entry-content">

					<?php
					
					 while ( have_posts() ):   the_post(); 
												
						 the_content(); 

					 endwhile; // end of the loop. ?>
			
				</div>
			</article>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>