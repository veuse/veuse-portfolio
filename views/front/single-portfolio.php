<?php
/**
 * The template for displaying single post of post-type portfolio
 * 
 * @ Post type: portfolio
 *
 *
 * The hooks are filterable, so you can change markup without touching this file.
 * Current markup is for twenty-twelve theme. 
 * 
 * Example filter for the veuse_before_single_project hook:

	function your_function_name(){
		return '<div id="someId" class="your classes here">';
	}
		
	add_filter('veuse_before_project','your_function_name');

*/

get_header(); 

do_action('veuse_before_single_project'); // Function located in veuse-portfolio/includes/hooks.php - @ Filter name: veuse_before_project

do_action('veuse_before_single_project_article'); // Function located in veuse-portfolio/includes/hooks.php - @ Filter name: veuse_before_project_article


/* Start loop */

while ( have_posts() ):   the_post(); 
											
	 the_content(); // Post content - Filtered in veuse-portfolio.php

endwhile; // end of the loop. 

do_action('veuse_after_single_project_article'); // Function located in veuse-portfolio/includes/hooks.php - @ Filter name: veuse_after_project_article

do_action('veuse_after_single_project'); // Function located in veuse-portfolio/includes/hooks.php - @ Filter name: veuse_after_project

get_sidebar(); 
get_footer(); 

?>