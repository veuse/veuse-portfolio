<?php

global $post;

/* Defaults */

$website = true;
$client = true;
$launch = true;
$credits = true;

	    
$categories 	= get_the_term_list($post->ID, 'portfolio-category','',', ','');
$skills 		= get_the_term_list($post->ID, 'portfolio-tag','',', ','');
$website_data	= get_post_meta($post->ID,'veuse_portfolio_website',true);
$credits_data 	= get_post_meta($post->ID,'veuse_portfolio_credits',true);
$client_data 	= get_post_meta($post->ID,'veuse_portfolio_client',true);
$launch_data 	= get_post_meta($post->ID,'veuse_portfolio_launch',true);

?>

<ul class="veuse-project-meta">
<?php if( $website == true){ ?>
<li><span data-id="meta-website"><?php _e('Website:','veuse-portfolio');?></span> <a href="<?php echo $website_data;?>"><?php echo $website_data;?></a></li>
<?php } 
if( $client == true){?>
<li><span data-id="meta-client"><?php _e('Client:','veuse-portfolio');?></span> <?php echo $client_data;?></li>
<?php } 
if( $launch == true){ ?>
<li><span data-id="meta-lauch"><?php _e('Lauch:','veuse-portfolio');?></span> <?php echo $launch_data;?></li>
<?php } 
if( $credits == true){?>
<li><span data-id="meta-credits"><?php _e('Credits:','veuse-portfolio');?></span> <?php echo $credits_data;?></li>
<?php } 
if( !empty($categories) ){?>
<li><span data-id="meta-categories"><?php _e('Categories:','veuse-portfolio');?></span> <?php echo $categories;?></li>
<?php } 
if( !empty($skills) ){?>
<li><span data-id="meta-skills"><?php _e('Skills:','veuse-portfolio');?></span> <?php echo $skills;?></li>
<?php } ?>
</ul>