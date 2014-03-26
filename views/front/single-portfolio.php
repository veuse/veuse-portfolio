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

			<?php if ( have_posts() ) : while ( have_posts() ):   the_post(); ?>
				<h1><?php the_title();?></h1>
				<?php the_content(); ?>

			<?php endwhile; endif;// end of the loop. ?>
				</div>
			</article>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>