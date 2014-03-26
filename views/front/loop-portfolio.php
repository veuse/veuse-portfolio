<?php

/* The loop that displays portfolio-posts */


global $post, $categories;

if($columns == '1') { $imagesize = array( 'width' => 960, 'height' => 960 * 0.6);}
if($columns == '2') { $imagesize = array( 'width' => 480, 'height' => 480 * 0.6);}
if($columns == '3') { $imagesize = array( 'width' => 320, 'height' => 320 * 0.6);}
if($columns == '4') { $imagesize = array( 'width' => 225, 'height' => 225 * 0.6);}
if($columns == '5') { $imagesize = array( 'width' => 200, 'height' => 200 * 0.6);}

	$taxonomy = 'portfolio-category';

	/* Portfolio filter */

	if(isset($type) && $type == 'filtered' ){

		$allterms = get_terms( $taxonomy, array('hide_empty' => 1)); ?>
     			
     	<ul class="portfolio-filter sub-nav">
    		<li class="active"><a href="#" class="showall" ><?php _e('All','ceon');?></a></li>
    			<?php
     			foreach ( $allterms as $term ) {

		     		if(!empty($categories)){
	     				$needle = strpos($categories, $term ->slug);
	     					if($needle !== false){
	     					?><li><a href="#" class="<?php echo $term->slug;?>"><?php echo $term->name;?></a></li><?php
	        			}
	        		} else { ?>

	        		<li><a href="#" class="<?php echo $term->slug;?>"><?php echo $term->name;?></a></li>
	        		<?php
		        	}
        		}
?>
     			</ul>
     			<?php

		}


		?>
		<ul class="portfolio-list small-block-portfolio-grid-2 large-block-portfolio-grid-<?php echo $columns;?>">


		<?php

		/* The loop */

		$plugin_options = get_option('veuse_portfolio_options');

		$i = 0;

		if(have_posts()): while (have_posts()): the_post();

		$i++;

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
		$img_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
		?>
		
			<li <?php post_class();?> data-id="id-<?php echo ($i + 1);?>" data-tags="<?php echo  $post_term_list;?>">
			<div class="portfolio-entry">
				<?php if(has_post_thumbnail()):?>
				
				<div class="entry-thumbnail">
					<a href="<?php echo get_permalink();?>">
						<span class="overlay"></span>
					<?php //if($plugin_options['lightbox'] == true):?>
				<!--	<a class="zoom-link" href="<?php echo $img_url;?>" data-rel="lightbox" title="<?php the_title();?>"><i class="icon-resize-full"></i></a>-->
					<?php //endif;?>
					<?php echo veuse_retina_interchange_image( $img_url, $imagesize['width'], $imagesize['height'], true);?>
					</a>
				</div>
				<?php endif;?>

				<div class="entry-data">
					<a href="<?php echo get_permalink();?>"><span class="caption"><?php echo get_the_title();?></span></a>
					<?php if(!isset($excerpt) && $excerpt == true){?>
					<p><?php echo veuse_portfolio_excerpt_limit($excerpt_limit);?></p>
					<?php } ?>
					<?php //if($displayterms == 'true'){?><!--<p><?php //echo $post_term_names;?></p>--><?php //} ?>
					<!--<a class="more-link" href="<?php echo get_permalink();?>"><?php echo $linktext;?><i class="icon-chevron-down"></i></a>-->
					<!--<div class="entry-meta"><i class="icon-tag"></i> <?php //echo $post_term_names;?></div>-->
				</div>

			</div>


		</li>

		<?php

		unset($post_term_list); // Reset the term list for each item


		endwhile; endif;?>

		</ul>