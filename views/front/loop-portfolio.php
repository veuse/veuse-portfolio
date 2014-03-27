<?php

/* The loop that displays portfolio-posts */

$portfolio = new VeusePortfolio();

global $post, $categories;

if($columns == '1') { $imagesize = array( 'width' => 960, 'height' => 960 * 0.6);}
if($columns == '2') { $imagesize = array( 'width' => 480, 'height' => 480 * 0.6);}
if($columns == '3') { $imagesize = array( 'width' => 320, 'height' => 320 * 0.6);}
if($columns == '4') { $imagesize = array( 'width' => 225, 'height' => 225 * 0.6);}
if($columns == '5') { $imagesize = array( 'width' => 200, 'height' => 200 * 0.6);}

$taxonomy = 'portfolio-category';

require($portfolio->veuse_portfolio_locate_part('loop-portfolio-filter'));
		
		?>
		
		<ul class="veuse-portfolio-list small-block-portfolio-grid-2 large-block-portfolio-grid-<?php echo $columns;?>">

		<?php

		/* The loop */

		$plugin_options = get_option('veuse_portfolio_options');

		$i = 0;

		if(have_posts()): while (have_posts()): the_post();

		$i++;
		
		
		/* Generate term lists */
		
		$post_terms = get_the_terms( $post->ID, $taxonomy);
		$count = count($post_terms);

		if ( $count > 0 ){

		$post_term_names = '';
		$post_term_list = '';

			if ( $post_terms && ! is_wp_error( $post_terms ) ) :
	
		       foreach ( $post_terms as $term ) {
		       	   	$post_term_list .= $term->slug  . ' ';
		       	   	$post_term_names.= '<span>' . $term->name. '</span>,';
		       }
	
			endif;
		}
		
		
		?>
		
			<li <?php post_class();?> data-id="id-<?php echo ($i + 1);?>" data-tags="<?php echo  $post_term_list;?>">
			
			<?php require($portfolio->veuse_portfolio_locate_part('loop-project-content')); ?>
			
			</li>

		<?php 
		
		unset($post_term_list); // Reset the term list for each item

		endwhile; endif;?>

		</ul>