<?php
/**
 * Plugin hooks for customizing the plugin 
 * 
 *
 */
 
 
/* Before project  */
 
function veuse_insert_before_single_project(){
	 
	echo apply_filters('veuse_before_project','<div id="primary" class="site-content"><div id="content" role="main">');

}
 
add_action('veuse_before_single_project','veuse_insert_before_single_project');


/* After project  */

function veuse_insert_after_single_project(){
	 
	echo apply_filters('veuse_after_project','</div></div>');

}
 
add_action('veuse_after_single_project','veuse_insert_after_single_project');


/* Before project article */

function veuse_insert_before_single_project_article(){

		/* Get post classes */
		$the_post_classes = get_post_class();
		$the_post_class_string = '';
		foreach( $the_post_classes as $post_class ) {
		    $the_post_class_string .= $post_class . ' ';
		}
	 
		echo apply_filters('veuse_before_project_article','<article id="post-'. get_the_ID().'" class="'. $the_post_class_string.'"><div class="entry-content">');

}
 
add_action('veuse_before_single_project_article','veuse_insert_before_single_project_article');


/* After project article */

function veuse_insert_after_single_project_article(){
 
		echo apply_filters('veuse_after_project_article','</div></article>');

}
 
add_action('veuse_after_single_project_article','veuse_insert_after_single_project_article');