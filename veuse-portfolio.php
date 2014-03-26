<?php
/*
Plugin Name: Veuse Portfolio
Plugin URI: http://veuse.com/veuse-analytics
Description: Creates a post-type for portfolio and two taxonomies. Fully localized. Templates included. This is an add-on for the Veuse Pagebuilder plugin. This plugin does not handle any presentation of the post-type data. You will need to edit theme files for this. Documentation on this at
Version: 1.2
Author: Andreas Wilthil
Author URI: http://veuse.com
License: GPL3
Text Domain: veuse-portfolio
Domain Path: /languages
GitHub Plugin URI: https://github.com/veuse/veuse-portfolio
GitHub Branch: master
*/


/*  Copyright 2014  Andreas Wilthil  (email : andreas.wilthil@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


__('Veuse Portfolio', 'veuse-portfolio' ); /* Dummy call for plugin name translation. */


class VeusePortfolio {

	private $pluginURI  = '';
	private $pluginPATH = '';
	
	function __construct(){
		
		$this->pluginURI  = plugin_dir_url(__FILE__) ;
		$this->pluginPATH = plugin_dir_path(__FILE__) ;
		
		//require_once 'views/back/shortcode-generator.php';
			
		add_action('wp_enqueue_scripts', array(&$this,'veuse_portfolio_enqueue_script'));
		add_action('admin_enqueue_scripts', array(&$this,'veuse_portfolio_admin_enqueue_script'), 100 );
		
		add_action('plugins_loaded', array(&$this,'veuse_portfolio_load'));
		add_action('plugins_loaded', array(&$this,'localize_plugin'));
		add_action('init', array(&$this,'veuse_post_type_portfolio'));
		
		add_filter('manage_portfolio_posts_columns', array(&$this,'veuse_portfolio_columns'));
		add_action('manage_portfolio_posts_custom_column', array(&$this,'veuse_portfolio_custom_columns'), 10, 2 );
		
		/* Add shortcodes */
		
		add_shortcode('veuse_portfolio', array(&$this,'veuse_portfolio_shortcode'));
		add_shortcode('veuse_project_content', array(&$this,'veuse_project_content'));
		add_shortcode('veuse_project_meta', array(&$this,'veuse_project_meta'));
		add_shortcode('veuse_project_excerpt', array(&$this,'veuse_project_excerpt'));
		add_shortcode('veuse_project_image', array(&$this,'veuse_project_image'));
		
		add_action('media_buttons_context',  array(&$this,'add_my_custom_button'));
		add_action('admin_footer',  array(&$this,'portfolio_popup_content' ));
	}
	
	/* Enqueue scripts */

	function veuse_portfolio_enqueue_script() {

		/* CSS */

		$plugin_options = get_option('veuse_portfolio_options');

		wp_register_style( 'magnific',  $this->pluginURI . 'assets/css/magnific-popup.css', array(), '', 'screen' );
		wp_enqueue_style ( 'magnific' );

		if(isset($plugin_options['css'])){
			wp_register_style( 'veuse-portfolio',  $this->pluginURI . 'assets/css/veuse-portfolio.css', array(), '', 'screen' );
			wp_enqueue_style ( 'veuse-portfolio' );
		}

		/* JS */

		wp_enqueue_script('magnific-popup', $this->pluginURI . 'assets/js/jquery.magnific-popup.min.js', array('jquery'), '', true);
		wp_enqueue_script('responsive-carousel', $this->pluginURI . 'assets/js/responsive-carousel.min.js', array('jquery'), '', true);
		wp_enqueue_script('veuse-portfolio', $this->pluginURI . 'assets/js/veuse-portfolio.js', array('jquery'), '', true);

	}
	
	function veuse_portfolio_admin_enqueue_script() {
		
		global $pagenow;
		
		if ($pagenow == 'post.php' || $pagenow == 'post-new.php' ){
			wp_enqueue_script('veuse-portfolio-admin', $this->pluginURI . 'assets/js/veuse-portfolio-admin.js', array('jquery'), '', true);
		}
	}
	
	
	/* Plugin setup on plugins_loaded
	============================================= */
	
	function veuse_portfolio_load(){
	
		//add_post_type_support('portfolio', 'post-formats'); // add post-formats to post_type 'portfolio'
	
	}
	
	/* Localization
	============================================= */
	
	function localize_plugin() {
	    load_plugin_textdomain('veuse-portfolio', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}
	
	
	/* Register post-type
	============================================= */
	
	function veuse_post_type_portfolio() {
	
		$labels = array(
	        'name' => __( 'Portfolio', 'veuse-portfolio' ), // Tip: _x('') is used for localization
	        'singular_name' => __( 'Project', 'veuse-portfolio' ),
	        'add_new' => __( 'Add New Project', 'veuse-portfolio' ),
	        'add_new_item' => __( 'Add New Project','veuse-portfolio' ),
	        'edit_item' => __( 'Edit Project', 'veuse-portfolio' ),
	        'all_items' => __( 'All Projects','veuse-portfolio' ),
	        'new_item' => __( 'New Project','veuse-portfolio' ),
	        'view_item' => __( 'View Project','veuse-portfolio' ),
	        'search_items' => __( 'Search Projects','veuse-portfolio' ),
	        'not_found' =>  __( 'No Projects','veuse-portfolio' ),
	        'not_found_in_trash' => __( 'No Projects found in Trash','veuse-portfolio' ),
	        'parent_item_colon' => ''
	    );
	
		register_post_type('portfolio',
			array(
				'labels' => $labels,
				'public' => true,
				'show_ui' => true,
				'_builtin' => false, // It's a custom post type, not built in
				'_edit_link' => 'post.php?post=%d',
				'capability_type' => 'post',
				'hierarchical' => false,
				'rewrite' => array("slug" => "work"), // Permalinks
				'query_var' => "case", // This goes to the WP_Query schema
				'supports' => array('title','author','thumbnail', 'editor' ,'comments','excerpt','custom-fields'),
				'menu_position' => 30,
				'menu_icon' => 'dashicons-portfolio',
				'publicly_queryable' => true,
				'exclude_from_search' => false,
				)
			);
	
	
			/* Register portfolio taxonomy */
	
			$portfoliolabels = array(
		        'name' => __( 'Portfolios', 'veuse-portfolio' ), // Tip: _x('') is used for localization
		        'singular_label' => __( 'Portfolio', 'veuse-portfolio' ),
		        'add_new' => __( 'Add New Portfolio', 'veuse-portfolio' ),
		        'add_new_item' => __( 'Add New Portfolio','veuse-portfolio' ),
		        'edit_item' => __( 'Edit Portfolio', 'veuse-portfolio' ),
		        'all_items' => __( 'All Portfolios','veuse-portfolio' ),
		        'new_item' => __( 'New Portfolio','veuse-portfolio' ),
		        'view_item' => __( 'View Portfolios','veuse-portfolio' ),
		        'search_items' => __( 'Search Portfolios','veuse-portfolio' ),
		        'not_found' =>  __( 'No Portfolios found','veuse-portfolio' ),
		        'parent_item_colon' => ''
		    );
	
	
			register_taxonomy("portfolio-category",
				array("portfolio"),
				array("hierarchical" => true,
					"labels" => $portfoliolabels,
					"rewrite" => true,
					"show_ui" => true
					)
				);
	
			/* Register project tags */
	
			$portfoliotaglabels = array(
			        'name' => __( 'Skills', 'veuse-posttypes' ), // Tip: _x('') is used for localization
			        'singular_label' => __( 'Skill', 'veuse-posttypes' ),
			        'add_new' => __( 'Add New Skill', 'veuse-posttypes' ),
			        'add_new_item' => __( 'Add New Skill','veuse-posttypes' ),
			        'edit_item' => __( 'Edit Skill', 'veuse-posttypes' ),
			        'all_items' => __( 'All Skills','veuse-posttypes' ),
			        'new_item' => __( 'New Skill','veuse-posttypes' ),
			        'view_item' => __( 'View Skill','veuse-posttypes' ),
			        'search_items' => __( 'Search Skills','veuse-posttypes' ),
			        'not_found' =>  __( 'No Skills found','veuse-posttypes' ),
			        'parent_item_colon' => ''
			    );
	
			register_taxonomy("portfolio-tag",
					array("portfolio"),
					array("hierarchical" => false,
						'rewrite' => array( 'slug' => 'portfolio-tag' ),
						'show_ui' => true,
						'show_admin_column' => true,
						'query_var' => true,
						"labels" => $portfoliotaglabels,
						'show_in_nav_menus' => false
						)
					);
			
			


		}
		
		/**
		 * Shortcodes
		 *
		 */
		
		
		/* Shortcode for displaying post-meta */
		
		function veuse_project_content( $atts, $content = null ) { 
		
			extract(shortcode_atts(array(
				'image'	 	=> true,
				'title' 	=> true,
				'content'	=> true,
				'meta'		=> true	

		    ), $atts));
		    
		    ob_start();
			require($this->veuse_portfolio_locate_part('project-content'));
			$output = ob_get_contents();
			ob_end_clean();
		    
		    return $output;
		}
		 
		/* Shortcode for displaying post-meta */
		
		function veuse_project_meta( $atts, $content = null ) { 
		
			extract(shortcode_atts(array(
				'website'	 	=> true,
				'client' 		=> true,
				'launch'		=> true,
				'credits'		=> true	

		    ), $atts));
		    
		    ob_start();
			require($this->veuse_portfolio_locate_part('project-meta'));
			$output = ob_get_contents();
			ob_end_clean();
		    
		    return $output;
		}
		
		
		/* Shortcode for displaying post-excerpt */
		
		function veuse_project_excerpt( $atts, $content = null ) { 
			global $post;
		  	if(!empty($post->post_excerpt))
			$output = '<p class="veuse-project-excerpt"><strong>'.$post->post_excerpt.'</strong></p>';
				    
		    return $output;
		}
		
		/* Shortcode for displaying post thumbnail */
		
		function veuse_project_image( $atts, $content = null ) { 
			
			extract(shortcode_atts(array(
				'width' 		=> '900',
				'height' 		=> '',
				'retina'		=> true	

		    ), $atts));
				    
			global $post;
		  	
		  	$img_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID));
		  	
		  	ob_start();
			require($this->veuse_portfolio_locate_part('project-image'));
			$output = ob_get_contents();
			ob_end_clean();
	    
		    return $output;
		}
		
		/* Shortcodes
		============================================= */
		
		function veuse_portfolio_shortcode( $atts, $content = null ) {
		
				 extract(shortcode_atts(array(
						'categories' 	=> '',
						'columns' 		=> '3',
						'order'			=> 'ASC',
						'orderby'		=> 'title',
						'type'			=> 'filtered',
						'perpage'		=> '-1',
						'template'		=> 'page',
						'excerpt'		=> 'false',
						'morelink'		=> 'false',
						'linktext'		=> __('Show project','veuse-portfolio'),
						'displayterms'	=> 'false'		
		
				    ), $atts));
		
		
		
				if ($template == 'page'){
		
					$paged = get_query_var('paged');
		
					global $wp_query;
		
					query_posts(array(
						'paged' => $paged,
			        	'post_type' => 'portfolio',
			        	'showposts' => $perpage,
			        	'order' => $order,
			        	'orderby' 	=> $orderby,
			        	'portfolio-category' => $categories
			        	)
		        	);

		
				}
		
				else{
					$paged = get_query_var('paged');
			        $query_string = '';
			        query_posts( $query_string . "&paged=$paged&posts_per_page=$perpage&posttype=portfolio&taxonomy=portfolio-category&portfolio-category=$categories&order=$order&orderby=$orderby" );
					}
		
		
					ob_start();
		
					require($this->veuse_portfolio_locate_part('loop-portfolio','template-parts'));
					$content = ob_get_contents();
				
		
					ob_end_clean();
					
					wp_reset_query();
		
					return $content;
		
		
			}
		
		
		function veuse_portfolio_custom_columns($column, $post_id) {
		
			global $post;
					
			switch ($column) {
			 	
		 	
			 		case 'title' :
			 	
						echo get_the_title();
						break;
				 	
				 	case 'thumbnail' :
				 	
					 	if( has_post_thumbnail($post_id)){ echo get_the_post_thumbnail($post_id,'thumbnail'); }
						break;
						
					case 'exc' :
				 	
					 	echo get_the_excerpt();
						break;
					
					case 'term' :
				 	
					 	$taxonomy = 'portfolio-category';
						$post_type = get_post_type($post_id);
						$terms = get_the_terms($post_id, $taxonomy);
			
						if (!empty($terms) ) {
							foreach ( $terms as $term ){
						    	$post_terms[] ="<a href='edit.php?post_type={$post_type}&{$taxonomy}={$term->slug}'> " .esc_html(sanitize_term_field('name', $term->name, $term->term_id, $taxonomy, 'edit')) . "</a>";
						    }
						       echo join('', $post_terms );
						}
						
						break;
								
				}			
		}
		
		function veuse_portfolio_columns($columns){
					
			$columns = array(
					"cb" => "<input type=\"checkbox\" />",
					"title" => __("Title","veuse-portfolio"),
					"thumbnail" => __("Thumbnail","veuse-portfolio"),
					"exc" => __("Excerpt","veuse-portfolio"),
					"term" => __("Category","veuse-portfolio"),
					"sidebar" => __("Sidebar","veuse-portfolio"),
			);
			
			return $columns;
		}


		/* Find template part
	
		Makes it possible to override the loop with
		a custom theme loop-slider.php
		
		============================================ */
		
		function veuse_portfolio_locate_part($file) {
		
			     if ( file_exists( get_stylesheet_directory().'/'. $file .'.php')){
			     	$filepath = get_stylesheet_directory().'/'. $file .'.php';
			     }
			     elseif ( file_exists(get_template_directory().'/'. $file .'.php')){
			     	$filepath = get_template_directory().'/'. $file .'.php';
			     }
			     else {
			        $filepath = $this->pluginPATH .'views/front/'. $file.'.php';
			       }
			     return $filepath;
		}
		
	

		function add_my_custom_button($context) {
		
			  //path to my icon
			  $img = $this->pluginURI.'assets/images/icon-portfolio-large.png';
			
			  //our popup's title
			  $title = 'Add portfolio';
			
			  //append the icon
			  $context .= "<a href='#TB_inline?&width=640&height=600&inlineId=veuse-portfolio-popup&modal=false' class='thickbox' style='margin:0;'  title='{$title}'><img src='{$img}' width='24' height='24' style='margin:-1px 0  0 4px; padding:0 !important;'/></a>";
			
			  return $context;
		}
			
function portfolio_popup_content() { ?>
			 <style>
			 
			 	#TB_overlay { z-index: 9998 !important; }
			 	#TB_window { z-index: 9999 !important; }
			 
			 	/* new clearfix */
				.clearfix:after {
					visibility: hidden;
					display: block;
					font-size: 0;
					content: " ";
					clear: both;
					height: 0;
					}
				* html .clearfix             { zoom: 1; } /* IE6 */
				*:first-child+html .clearfix { zoom: 1; } /* IE7 */

			 	div.info { width:35%; float: right; margin:0; padding:0;}
			 	div.selector {width:60%; float: left;}
			 	div.info p { margin:0 0 3px !important; padding:0 !important;}
			 	div.info p.desc { color: #888;}
			 	
			  	form#veuse-portfolio-insert { margin:0; width: auto; padding: 0; display: block;}
			  	form#veuse-portfolio-insert p { margin-bottom: 8px;}
			  	form#veuse-portfolio-insert hr { border:0; border-top:1px solid #eee !important; margin:15px 0; background-color: #eee !important;}
			  	form#veuse-portfolio-insert > section { margin-bottom: 10px; /*border-bottom: 1px dotted #d4d4d4;*/}
			  
			   
			  	
			  	.selector ul { margin:0; } 
			  	.selector ul li { display: inline-block;  margin:0; padding:0;}	  	
			  	.selector ul li a{ color:#606060 !important; display: inline-block; padding:4px 8px; background:#eee;  border:1px solid #fff; text-decoration: none;
				  	
				  	border-radius: 2px;
				  	-moz-border-radius: 2px;
				  	-webkit-border-radius: 2px;
				  	margin:0 2px 2px 0;
				  	
			  	}
			  	
			  	.selector.group ul li a{ 
				  	
				  	border-radius: 0px;
				  	-moz-border-radius: 0px;
				  	-webkit-border-radius: 0px;
				  	margin:0 -5px 2px 0;
				  	
			  	}
			  	
			  	.selector.group ul li:first-child a {
			  			border-radius: 2px 0 0 2px;
				  	-moz-border-radius: 2px 0 0 2px;
				  	-webkit-border-radius: 2px 0 0 2px;
			  	}
			  	
			  	.selector.group ul li:lase-child a {
			  			border-radius: 0 2px 2px 0;
				  	-moz-border-radius: 0 2px 2px 0;
				  	-webkit-border-radius: 0 2px 2px 0;
			  	}
			  	
			  	.selector ul li a.active {   	
				  	background: #2a95c5; border-color:#fff; color:#fff !important;
			  	}
			  	
			  	
			
			  
			  </style>
			<div id="veuse-portfolio-popup" style="width:100%; height:100%; display:none;">
			  <h2>Insert portfolio</h2>
			  
			  <script>
			  
			  	jQuery(function($){
			  		
			  		jQuery('a.portfolio-selector-item').click(function(){
			  			$(this).toggleClass('active');
			  			return false;
			  		});


			  		jQuery('#portfolio-list-type-selector a').click(function(){
			  			$('#portfolio-list-type-selector a').removeClass('active');
			  			$(this).addClass('active');
			  			return false;
			  		});



					jQuery('a.entry-element-selector-item').click(function(){
			  			$(this).toggleClass('active');
			  			return false;
			  		});		
						  		
			  		
			  		
			  		jQuery('#portfolio-column-selector a').click(function(){
			  			$('#portfolio-column-selector a').removeClass('active');
			  			$(this).addClass('active');
			  			return false;
			  		});
			  		
			  		
			  		jQuery('#portfolio-order-selector a').click(function(){
			  			$('#portfolio-order-selector a').removeClass('active');
			  			$(this).addClass('active');
			  			return false;
			  		});
			  		
			  		jQuery('#portfolio-orderby-selector a').click(function(){
			  			$('#portfolio-orderby-selector a').removeClass('active');
			  			$(this).addClass('active');
			  			return false;
			  		});
			  	
			  		 	  		
				  	jQuery('#insert-portfolio-shortcode').click(function(){
					  	
					  	var shortcodeText;
					
					  	var ids = '';
					  	
						$('#portfolio-selector a.active').each(function(){
							
							ids += $(this).attr('data-id') + ',';
						});
						
						
							
						var type = $('#portfolio-list-type-selector a.active').attr('data-id');
						var perpage = $('#portfolio-perpage').val();
						var linktext = $('#entry-link-text').val();
				
						
						ids = ids.substring(0, ids.length-1);
						
						var layout = $('#layout-selector a.active').attr('data-id');
						var columns = $('#portfolio-column-selector a.active').attr('data-id');
						
						var displayexcerpt;
						if ($('#entry-element-selector').find('a[data-id=excerpt]').hasClass('active')){
							displayexcerpt = 'true';
						} else {
							displayexcerpt = 'false';
						}
						
						var displaytitle;
						if ($('#entry-element-selector').find('a[data-id=title]').hasClass('active')){
							displaytitle = 'true';
						} else {
							displaytitle = 'false';
						}
						
						var displayimage;
						if ($('#entry-element-selector').find('a[data-id=image]').hasClass('active')){
							displayimage = 'true';
						} else {
							displayimage = 'false';
						}
						
						var displaymorelink;
						if ($('#entry-element-selector').find('a[data-id=morelink]').hasClass('active')){
							displaymorelink = 'true';
						} else {
							displaymorelink = 'false';
						}
						
														  		
					  	shortcodeText = '[veuse_portfolio categories="' + ids + '"  columns="'+ columns +'" title="'+ displaytitle+'" excerpt="' + displayexcerpt +'"  displayterms="false" perpage="'+ perpage +'" image="'+ displayimage+'" type="' + type + '" morelink="' + displaymorelink +'" link-text="'+ linktext +'"]';
					  	 tinyMCE.activeEditor.execCommand('mceInsertContent', false, shortcodeText);
					  	 tb_remove();
					  	 return false;
				  	});
				  	
			  	});
			  
			  
			  
			  </script>
			  
			  
			  <form id="veuse-portfolio-insert" class="clearfix">
			 
				<hr>
			  	 <section class="clearfix">
					<div class="info">
						<p><strong><?php _e('Categories','veuse-portfolio');?></strong></p>
						<p class="desc">Select categories to display posts from </p>
					</div>
					<div class="selector">	
						 
						 <ul id="portfolio-selector" class="clearfix">
									<?php
									
										$terms = array( 
										    'portfolio-category'
										   );
										
										$args = array(
										    'orderby'       => 'name', 
										    'order'         => 'ASC',
										    'hide_empty'    => true, 
										  
										); 
										
										$terms = get_terms( $terms, $args);
										
										if(count($terms) > 0){
																		
											foreach($terms as $term){
											
												echo '<li><a href="#" data-id="'.$term->slug.'" class="portfolio-selector-item">'.$term->name.'</a></li>';
											}
										}
									?>
							</ul>
					 </div>
			  	</section>
			  	<hr>
			  	
			  	<section id="portfolio-column-selector-wrapper" class="clearfix">
					<div class="info">
						<p><strong><?php _e('Grid','veuse-portfolio');?></strong></p>
						<p class="desc">How many columns?</p>
					</div>
					<div class="selector group">	
						<ul id="portfolio-column-selector" class="clearfix">
									
							<li><a href="#" class="portfolio-column-selector-item" data-id="1">1</a></li>
							<li><a href="#" class="portfolio-column-selector-item" data-id="2">2</a></li>
							<li><a href="#" class="portfolio-column-selector-item active" data-id="3">3</a></li>
							<li><a href="#" class="portfolio-column-selector-item" data-id="4">4</a></li>
																
						</ul>
					</div>
				</section>
				
			  	<hr>
			  	 <section class="clearfix">
					<div class="info">
					<p><strong><?php _e('Elements','veuse-portfolio');?></strong></p>
					<p class="desc">Which elements to display in the entry.</p>
					</div>
					<div class="selector">	
						<ul id="entry-element-selector" class="clearfix">				
							<li><a href="#" class="entry-element-selector-item active" data-id="title">Title</a></li>
							<li><a href="#" class="entry-element-selector-item" data-id="excerpt">Excerpt</a></li>
							<li><a href="#" class="entry-element-selector-item active" data-id="image">Thumbnail</a></li>
							<li><a href="#" class="entry-element-selector-item" data-id="morelink">Read-more link</a></li>
							<li><a href="#" class="entry-element-selector-item" data-id="terms">Category</a></li>
																
						</ul>
					</div>
			  	</section>
			  	
			  	<hr>
			  	 <section class="clearfix">
					<div class="info">
					<p><strong><?php _e('List type','veuse-portfolio');?></strong></p>
					<p class="desc">Select if you want pagination or filter.</p>
					</div>
					<div class="selector group">	
						<ul id="portfolio-list-type-selector" class="clearfix">				
							<li><a href="#" class="portfolio-list-type-selector-item active" data-id="filtered">Filter</a></li>
							<li><a href="#" class="portfolio-list-type-selector-item" data-id="pagination">Pagination</a></li>
							<li><a href="#" class="portfolio-list-type-selector-item" data-id="none">None</a></li>
						</ul>
					</div>
			  	</section>
				
				<hr>
				
				<section class="clearfix">
					<div class="info">
						<p><strong><?php _e('Posts per page','veuse-portfolio');?></strong></p>
						<p class="desc"><?php _e('How many posts to display. (-1 = all)','veuse-portfolio');?></p>
					</div>
					<div class="selector group">
					<input type="text" name="portfolio-perpage" id="portfolio-perpage" value="-1"/>
					</div>
				</section>
				
				<hr>
				
				<section class="clearfix">
					<div class="info">
						<p><strong><?php _e('Order by','veuse-portfolio');?></strong></p>
						<p class="desc"><?php _e('Select a criteria','veuse-portfolio');?></p>
					</div>
					<div class="selector group">
						
						<ul id="portfolio-orderby-selector" class="clearfix">				
							<li><a href="#" class="portfolio-orderby-selector-item active" data-id="title">Title</a></li>
							<li><a href="#" class="portfolio-orderby-selector-item" data-id="date">Date</a></li>
						
						</ul>
					</div>
				</section>
				<hr>
				<section class="clearfix">
					<div class="info">
						<p><strong><?php _e('Order','veuse-portfolio');?></strong></p>
						<p class="desc"><?php _e('','veuse-portfolio');?></p>
					</div>
					<div class="selector group">
						
						<ul id="portfolio-order-selector" class="clearfix">				
							<li><a href="#" class="portfolio-order-selector-item active" data-id="ASC">Ascending</a></li>
							<li><a href="#" class="portfolio-order-selector-item" data-id="DESC">Descending</a></li>
						
						</ul>
					</div>
				</section>
				
				<hr>
				
				<section class="clearfix">
					<div class="info">
						<p><strong><?php _e('Link text','veuse-portfolio');?></strong></p>
						<p class="desc"><?php _e('Text for the "read-more" link ','veuse-portfolio');?></p>
					</div>
					<div class="selector">
					<input type="text" name="entry-link-text" id="entry-link-text" />
					</div>
				</section>
				
				<hr>
				
				<section class="clearfix">
					<div class="info">
						<p><strong><?php _e('Image size','veuse-portfolio');?></strong></p>
						<p class="desc"><?php _e('Override the predefined image size. (width x height, ie. 400x200) ','veuse-portfolio');?></p>
					</div>
					<div class="selector">
					<input type="text" name="image-size" id="image-size" /><label for="image-size"></label>
					</div>
				</section>
				
				<hr>
				
				<hr>		
				<input type="submit" class="button-primary" id="insert-portfolio-shortcode"  value="<?php _e('Insert shortcode') ?>" />	  
			  </form>
			</div>
			<?php
			}
			
}


$veuse_portfolio = new VeusePortfolio;


/* Updater */
require_once 'updater/github-updater.php';

/* Documentation */
require_once 'documentation/documentation.php';

/* Options */
require_once 'views/back/post-meta.php';


/* Options */
require_once 'views/back/options.php';

/* Widget */
require_once('views/back/widget.php');



/**
 *  Checks if a single-portfolio.php exists in theme.
 *  If false, redirects single-portfolio to plugins single-portfolio.php
 */

function veuse_portfolio_template_include($template){
    
    global $wp_query;
    
    if ( $wp_query->query_vars['post_type'] === 'portfolio' ) {
		
		if ( !file_exists ( get_stylesheet_directory().'/single-portfolio.php') )
		{
	    	include( plugin_dir_path(__FILE__) . 'views/front/single-portfolio.php' );
	        die();  
	    }
	    
	}
    
    return $template;
}

add_filter('template_include', 'veuse_portfolio_template_include', 1, 1);



/* Filter the content to insert post meta-data */

if(!function_exists('veuse_portfolio_filter_content')){

	function veuse_portfolio_filter_content($content) {
		
		$portfolio = new VeusePortfolio();
		
		if( is_singular( 'portfolio') /* && is_main_query()*/ ) {
			
			
			ob_start();
			
			require($portfolio->veuse_portfolio_locate_part('project-image'));
			$content_image = ob_get_contents();
			ob_clean();
			
			$content_title = '<h1 class="entry-title">'. get_the_title().'</h1>';
			

			require($portfolio->veuse_portfolio_locate_part('project-meta'));
			$content_meta = ob_get_contents();
			ob_clean();
			
			ob_end_clean();

			//$content = $before_content . $content . $after_content;
			return $content_image.$content_title.$content.$content_meta;
		}

		else {

			return $content;
		}
	}

	add_filter('the_content', 'veuse_portfolio_filter_content', 1);
}



/* Pagination */
function veuse_portfolio_pagination() {
			
	global $wp_query, $wp_rewrite;
			
	$wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;
			
	$pagination = array(
	    	'base' => @add_query_arg('paged', '%#%'),
	    	'format' => '',
	    	'total' => $wp_query->max_num_pages,
	    	'current' => $current,
	    	'show_all' => true,
	    	'prev_next' => True,
	    	'prev_text' => '<i class="icon-angle-left"></i> Older',
	    	'next_text' => '<i class="icon-angle-right"></i> Newer ',
	    	'type' => 'plain'
	    	);

	    if ($wp_rewrite->using_permalinks())
	    	$pagination['base'] = user_trailingslashit(trailingslashit(remove_query_arg('s', get_pagenum_link(1))) . 'page/%#%/', 'paged');
	    if (!empty($wp_query->query_vars['s']))
	    	$pagination['add_args'] = array('s' => get_query_var('s'));

	    return '<div id="pager">'.paginate_links($pagination).'</div>';

	
}







/* Image resizer */

/**************************************
** GET IMAGE ID FROM SRC **
***************************************/
if(!function_exists('ceon_image_src')){

	function ceon_image_src($post_id,$size){

		$url =  wp_get_attachment_image_src ( get_post_thumbnail_id ( $post_id ),'full');
		return $url[0];

	}
}








/* Insert retina image */

if(!function_exists('veuse_retina_interchange_image')){

	function veuse_retina_interchange_image($img_url, $width, $height, $crop){

		$imagepath = '<img src="'. mr_image_resize($img_url, $width, $height, $crop, 'c', false) .'" data-interchange="['. mr_image_resize($img_url, $width, $height, $crop, 'c', true) .', (retina)]" alt="" />';
	
		return $imagepath;
	
	}
}


/**
  *  Resizes an image and returns the resized URL. Uses native WordPress functionality.
  *
  *  The function supports GD Library and ImageMagick. WordPress will pick whichever is most appropriate.
  *  If none of the supported libraries are available, the function will return the original image url.
  *
  *  Images are saved to the WordPress uploads directory, just like images uploaded through the Media Library.
  * 
  *  Supports WordPress 3.5 and above.
  * 
  *  Based on resize.php by Matthew Ruddy (GPLv2 Licensed, Copyright (c) 2012, 2013)
  *  https://github.com/MatthewRuddy/Wordpress-Timthumb-alternative
  * 
  *  License: GPLv2
  *  http://www.gnu.org/licenses/gpl-2.0.html
  *
  *  @author Ernesto MŽndez (http://der-design.com)
  *  @author Matthew Ruddy (http://rivaslider.com)
  */

if(!function_exists('mr_image_resize')){

	add_action('delete_attachment', 'mr_delete_resized_images');
	
	function mr_image_resize($url, $width=null, $height=null, $crop=true, $align='c', $retina=false) {
	
	  global $wpdb;
	
	  // Get common vars
	  $args = func_get_args();
	  $common = mr_common_info($args);
	
	  // Unpack vars if got an array...
	  if (is_array($common)) extract($common);
	
	  // ... Otherwise, return error, null or image
	  else return $common;
	
	  if (!file_exists($dest_file_name)) {
	
	    // We only want to resize Media Library images, so we can be sure they get deleted correctly when appropriate.
	    $query = $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE guid='%s'", $url);
	    $get_attachment = $wpdb->get_results($query);
	
	    // Load WordPress Image Editor
	    $editor = wp_get_image_editor($file_path);
	    
	    // Print possible wp error
	    if (is_wp_error($editor)) {
	      if (is_user_logged_in()) print_r($editor);
	      return null;
	    }
	
	    if ($crop) {
	
	      $src_x = $src_y = 0;
	      $src_w = $orig_width;
	      $src_h = $orig_height;
	
	      $cmp_x = $orig_width / $dest_width;
	      $cmp_y = $orig_height / $dest_height;
	
	      // Calculate x or y coordinate and width or height of source
	      if ($cmp_x > $cmp_y) {
	
	        $src_w = round ($orig_width / $cmp_x * $cmp_y);
	        $src_x = round (($orig_width - ($orig_width / $cmp_x * $cmp_y)) / 2);
	
	      } else if ($cmp_y > $cmp_x) {
	
	        $src_h = round ($orig_height / $cmp_y * $cmp_x);
	        $src_y = round (($orig_height - ($orig_height / $cmp_y * $cmp_x)) / 2);
	
	      }
	
	      // Positional cropping. Uses code from timthumb.php under the GPL
	      if ($align && $align != 'c') {
	        if (strpos ($align, 't') !== false) {
	          $src_y = 0;
	        }
	        if (strpos ($align, 'b') !== false) {
	          $src_y = $orig_height - $src_h;
	        }
	        if (strpos ($align, 'l') !== false) {
	          $src_x = 0;
	        }
	        if (strpos ($align, 'r') !== false) {
	          $src_x = $orig_width - $src_w;
	        }
	      }
	      
	      // Crop image
	      $editor->crop($src_x, $src_y, $src_w, $src_h, $dest_width, $dest_height);
	
	    } else {
	     
	      // Just resize image
	      $editor->resize($dest_width, $dest_height);
	     
	    }
	
	    // Save image
	    $saved = $editor->save($dest_file_name);
	    
	    // Print possible out of memory error
	    if (is_wp_error($saved)) {
	      @unlink($dest_file_name);
	      if (is_user_logged_in()) print_r($saved);
	      return null;
	    }
	
	    // Add the resized dimensions and alignment to original image metadata, so the images
	    // can be deleted when the original image is delete from the Media Library.
	    if ($get_attachment) {
	      $metadata = wp_get_attachment_metadata($get_attachment[0]->ID);
	      if (isset($metadata['image_meta'])) {
	        $md = $saved['width'] . 'x' . $saved['height'];
	        if ($crop) $md .= ($align) ? "_${align}" : "_c";
	        $metadata['image_meta']['resized_images'][] = $md;
	        wp_update_attachment_metadata($get_attachment[0]->ID, $metadata);
	      }
	    }
	
	    // Resized image url
	    $resized_url = str_replace(basename($url), basename($saved['path']), $url);
	
	  } else {
	
	    // Resized image url
	    $resized_url = str_replace(basename($url), basename($dest_file_name), $url);
	
	  }
	
	  // Return resized url
	  return $resized_url;
	
	}
	
	// Returns common information shared by processing functions
	
	function mr_common_info($args) {
	
	  // Unpack arguments
	  list($url, $width, $height, $crop, $align, $retina) = $args;
	  
	  // Return null if url empty
	  if (empty($url)) {
	    return is_user_logged_in() ? "image_not_specified" : null;
	  }
	
	  // Return if nocrop is set on query string
	  if (preg_match('/(\?|&)nocrop/', $url)) {
	    return $url;
	  }
	  
	  // Get the image file path
	  $urlinfo = parse_url($url);
	  $wp_upload_dir = wp_upload_dir();
	  
	  if (preg_match('/\/[0-9]{4}\/[0-9]{2}\/.+$/', $urlinfo['path'], $matches)) {
	    $file_path = $wp_upload_dir['basedir'] . $matches[0];
	  } else {
	    return $url;
	  }
	  
	  // Don't process a file that doesn't exist
	  if (!file_exists($file_path)) {
	    return null; // Degrade gracefully
	  }
	  
	  // Get original image size
	  $size = @getimagesize($file_path);
	
	  // If no size data obtained, return error or null
	  if (!$size) {
	    return is_user_logged_in() ? "getimagesize_error_common" : null;
	  }
	
	  // Set original width and height
	  list($orig_width, $orig_height, $orig_type) = $size;
	
	  // Generate width or height if not provided
	  if ($width && !$height) {
	    $height = floor ($orig_height * ($width / $orig_width));
	  } else if ($height && !$width) {
	    $width = floor ($orig_width * ($height / $orig_height));
	  } else if (!$width && !$height) {
	    return $url; // Return original url if no width/height provided
	  }
	
	  // Allow for different retina sizes
	  $retina = $retina ? ($retina === true ? 2 : $retina) : 1;
	
	  // Destination width and height variables
	  $dest_width = $width * $retina;
	  $dest_height = $height * $retina;
	
	  // Some additional info about the image
	  $info = pathinfo($file_path);
	  $dir = $info['dirname'];
	  $ext = $info['extension'];
	  $name = wp_basename($file_path, ".$ext");
	
	  // Suffix applied to filename
	  $suffix = "${dest_width}x${dest_height}";
	
	  // Set align info on file
	  if ($crop) {
	    $suffix .= ($align) ? "_${align}" : "_c";
	  }
	
	  // Get the destination file name
	  $dest_file_name = "${dir}/${name}-${suffix}.${ext}";
	  
	  // Return info
	  return array(
	    'dir' => $dir,
	    'name' => $name,
	    'ext' => $ext,
	    'suffix' => $suffix,
	    'orig_width' => $orig_width,
	    'orig_height' => $orig_height,
	    'orig_type' => $orig_type,
	    'dest_width' => $dest_width,
	    'dest_height' => $dest_height,
	    'file_path' => $file_path,
	    'dest_file_name' => $dest_file_name,
	  );
	
	}
	
	// Deletes the resized images when the original image is deleted from the WordPress Media Library.
	
	function mr_delete_resized_images($post_id) {
	
	  // Get attachment image metadata
	  $metadata = wp_get_attachment_metadata($post_id);
	  
	  // Return if no metadata is found
	  if (!$metadata) return;
	
	  // Return if we don't have the proper metadata
	  if (!isset($metadata['file']) || !isset($metadata['image_meta']['resized_images'])) return;
	  
	  $wp_upload_dir = wp_upload_dir();
	  $pathinfo = pathinfo($metadata['file']);
	  $resized_images = $metadata['image_meta']['resized_images'];
	  
	  // Delete the resized images
	  foreach ($resized_images as $dims) {
	
	    // Get the resized images filename
	    $file = $wp_upload_dir['basedir'] . '/' . $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-' . $dims . '.' . $pathinfo['extension'];
	
	    // Delete the resized image
	    @unlink($file);
	
		}
    }
}


?>