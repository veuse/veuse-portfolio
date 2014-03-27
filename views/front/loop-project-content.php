<?php 

 /** 
  *  This file displays the project content inside the lopp 
  *
  */
  
?>
  
  	<div class="portfolio-entry">

	<?php /* Insert post thumbnail */
				
	  	if(has_post_thumbnail()):
					
	  	$img_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) ); ?>
					
	  	<div class="veuse-project-thumbnail">
						
			<?php if($plugin_options['lightbox'] == true):?>
			<a class="zoom-link" href="<?php echo $img_url;?>" data-rel="lightbox" title="<?php the_title();?>">
			<?php else:?>
			<a href="<?php echo get_permalink();?>">
			<?php endif;?>	
			
			<?php echo veuse_retina_interchange_image( $img_url, $imagesize['width'], $imagesize['height'], true);?>
			
			</a>
		</div>
		<?php endif;?>

		<?php /* Entry content */?>
		<div class="veuse-project-data">
			<a href="<?php echo get_permalink();?>"><span class="caption"><?php echo get_the_title();?></span></a>
			<?php if(!isset($excerpt) && $excerpt == true){?>
			<p><?php echo veuse_portfolio_excerpt_limit($excerpt_limit);?></p>
			<?php } ?>
		</div>
	</div>