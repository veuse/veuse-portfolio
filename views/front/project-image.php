<?php

/* Defaults */

global $post, $content_width;

empty($img_url) ? $img_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID)) : '';
empty($width) 	? $width = $content_width : $width = $width;
empty($height) 	? $height = '' : $width = $width;
empty($retina) 	? $retina = true : $retina = $retina;

if($img_url):
?>

<div class="veuse-project-image"><?php echo veuse_retina_interchange_image( $img_url, $width, $height, $retina);?></div>

<?php endif;?>