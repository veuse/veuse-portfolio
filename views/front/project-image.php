<?php

/* Defaults */

global $post, $content_width;

empty($img_url) ? $img_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID)) : '';
empty($width) 	? $width = $content_width : $width = $width;
empty($height) 	? $height = '' : $width = $width;
empty($retina) 	? $retina = true : $retina = $retina;

if($img_url):

$classes = 'attachment-post-thumbnail wp-post-image';

?>

<div class="veuse-project-image <?php echo $classes;?>"><?php echo veuse_retina_interchange_image( $img_url, $width, $height, $retina);?></div>

<?php endif;?>