<?php
/*
Plugin Name: Veuse Portfolio
Plugin URI: http://veuse.com/veuse-analytics
Description: Creates a post-type for portfolio and two taxonomies. Fully localized. Templates included. This is an add-on for the Veuse Pagebuilder plugin. This plugin does not handle any presentation of the post-type data. You will need to edit theme files for this. Documentation on this at
Version: 1.3
Author: Andreas Wilthil
Author URI: http://veuse.com
License: GPL3
Text Domain: veuse-portfolio
Domain Path: /languages
GitHub Plugin URI: https://github.com/veuse/veuse-portfolio
GitHub Branch: master
*/
*/

__('Veuse Portfolio', 'veuse-portfolio' ); /* Dummy call for plugin name translation. */


class VeusePortfolio {

	private $pluginURI  = '';
	private $pluginPATH = '';
	
	function __construct(){
		
		$this->pluginURI  = plugin_dir_url(__FILE__) ;
		$this->pluginPATH = plugin_dir_path(__FILE__) ;
		
		add_action('wp_enqueue_scripts', array(&$this,'veuse_portfolio_enqueue_script'));
		add_action('admin_enqueue_scripts', array(&$this,'veuse_portfolio_admin_enqueue_script') );
		add_action('plugins_loaded', array(&$this,'veuse_portfolio_load'));
		add_action('plugins_loaded', array(&$this,'localize_plugin'));
		add_action('init', array(&$this,'veuse_post_type_portfolio'));
		
		add_filter('manage_portfolio_posts_columns', array(&$this,'veuse_portfolio_columns'));
		add_action('manage_portfolio_posts_custom_column', array(&$this,'veuse_portfolio_custom_columns'), 10, 2 );
		
		add_shortcode('veuse_portfolio', array(&$this,'veuse_portfolio_shortcode'));
		
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
	        'name' => __( 'Projects', 'veuse-portfolio' ), // Tip: _x('') is used for localization
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
				'supports' => array('title','author','thumbnail', 'editor' ,'comments','excerpt','custom-fields','post-formats'),
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
	
	
			/* Register client taxonomy */
			/*
			$clientlabels = array(
			        'name' => __( 'Clients', 'veuse-posttypes' ), // Tip: _x('') is used for localization
			        'singular_label' => __( 'Client', 'veuse-posttypes' ),
			        'add_new' => __( 'Add New Client', 'veuse-posttypes' ),
			        'add_new_item' => __( 'Add New Client','veuse-posttypes' ),
			        'edit_item' => __( 'Edit Client', 'veuse-posttypes' ),
			        'all_items' => __( 'All Clients','veuse-posttypes' ),
			        'new_item' => __( 'New Client','veuse-posttypes' ),
			        'view_item' => __( 'View Client','veuse-posttypes' ),
			        'search_items' => __( 'Search Clients','veuse-posttypes' ),
			        'not_found' =>  __( 'No Clients found','veuse-posttypes' ),
			        'parent_item_colon' => ''
			    );
	
			register_taxonomy("portfolio-client",
					array("portfolio"),
					array("hierarchical" => false,
						'rewrite' => array( 'slug' => 'clients' ),
						'show_ui' => true,
						'show_admin_column' => true,
						'query_var' => true,
						"labels" => $clientlabels,
	
						)
					);
			*/
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
		
		/* Shortcode
		============================================= */
		
		function veuse_portfolio_shortcode( $atts, $content = null ) {
		
				 extract(shortcode_atts(array(
						'categories' 	=> '',
						'columns' 		=> '3',
						'order'			=> 'ASC',
						'orderby'		=> 'title',
						'type'			=> 'filtered',
						'perpage'		=> '9',
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
			        $filepath = $this->pluginPATH . $file.'.php';
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
			
		function portfolio_popup_content() {
			?>
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
						
						var link = document.getElementById('insert-link');	
						
						if(link.checked){
							insertlink = 'true';
						}
						else {
							insertlink = 'false';
							}
								
								
						/*
							'categories' 	=> '',
							'columns' 		=> '3',
							'order'			=> 'ASC',
							'orderby'		=> 'title',
							'type'			=> 'pagination',
							'perpage'		=> '9',
							'template'		=> 'page',
							'excerpt'		=> false
						*/		
								  		
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

include_once('updater.php');

if ( is_admin()) { // note the use of is_admin() to double check that this is happening in the admin
    $config = array(
        'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
        'proper_folder_name' => 'veuse-portfolio', // this is the name of the folder your plugin lives in
        'api_url' => 'https://api.github.com/repos/veuse/veuse-portfolio', // the github API url of your github repo
        'raw_url' => 'https://raw.github.com/veuse/veuse-portfolio', // the github raw url of your github repo
        'github_url' => 'https://github.com/veuse/veuse-portfolio', // the github url of your github repo
        'zip_url' => 'https://github.com/veuse/veuse-portfolio/archive/master.zip', // the zip url of the github repo
        'sslverify' => true, // wether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
        'requires' => '3.7', // which version of WordPress does your plugin require?
        'tested' => '3.8', // which version of WordPress is your plugin tested up to?
        'readme' => 'README.md', // which file to use as the readme for the version number
        'access_token' => '' // Access private repositories by authorizing under Appearance > Github Updates when this example plugin is installed
    );
    new WP_GitHub_Updater($config);
}


require_once('widget.php');

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


/* Plugin options */

// ------------------------------------------------------------------------
// PLUGIN PREFIX:
// ------------------------------------------------------------------------
// A PREFIX IS USED TO AVOID CONFLICTS WITH EXISTING PLUGIN FUNCTION NAMES.
// WHEN CREATING A NEW PLUGIN, CHANGE THE PREFIX AND USE YOUR TEXT EDITORS
// SEARCH/REPLACE FUNCTION TO RENAME THEM ALL QUICKLY.
// ------------------------------------------------------------------------

// 'veuse_portfolio_' prefix is derived from [p]plugin [o]ptions [s]tarter [k]it

// ------------------------------------------------------------------------
// REGISTER HOOKS & CALLBACK FUNCTIONS:
// ------------------------------------------------------------------------
// HOOKS TO SETUP DEFAULT PLUGIN OPTIONS, HANDLE CLEAN-UP OF OPTIONS WHEN
// PLUGIN IS DEACTIVATED AND DELETED, INITIALISE PLUGIN, ADD OPTIONS PAGE.
// ------------------------------------------------------------------------

// Set-up Action and Filter Hooks
register_activation_hook(__FILE__, 'veuse_portfolio_add_defaults');
register_uninstall_hook(__FILE__, 'veuse_portfolio_delete_plugin_options');
add_action('admin_init', 'veuse_portfolio_init' );
add_action('admin_menu', 'veuse_portfolio_add_options_page');
add_filter( 'plugin_action_links', 'veuse_portfolio_plugin_action_links', 10, 2 );

// --------------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_uninstall_hook(__FILE__, 'veuse_portfolio_delete_plugin_options')
// --------------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE USER DEACTIVATES AND DELETES THE PLUGIN. IT SIMPLY DELETES
// THE PLUGIN OPTIONS DB ENTRY (WHICH IS AN ARRAY STORING ALL THE PLUGIN OPTIONS).
// --------------------------------------------------------------------------------------

// Delete options table entries ONLY when plugin deactivated AND deleted
function veuse_portfolio_delete_plugin_options() {
	delete_option('veuse_portfolio_options');
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_activation_hook(__FILE__, 'veuse_portfolio_add_defaults')
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE PLUGIN IS ACTIVATED. IF THERE ARE NO THEME OPTIONS
// CURRENTLY SET, OR THE USER HAS SELECTED THE CHECKBOX TO RESET OPTIONS TO THEIR
// DEFAULTS THEN THE OPTIONS ARE SET/RESET.
//
// OTHERWISE, THE PLUGIN OPTIONS REMAIN UNCHANGED.
// ------------------------------------------------------------------------------

// Define default option settings
function veuse_portfolio_add_defaults() {
	$tmp = get_option('veuse_portfolio_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp))) {
		delete_option('veuse_portfolio_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
		$arr = array(	"css" => "1",
						"lightbox" => "1",

		);
		update_option('veuse_portfolio_options', $arr);
	}
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_init', 'veuse_portfolio_init' )
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_init' HOOK FIRES, AND REGISTERS YOUR PLUGIN
// SETTING WITH THE WORDPRESS SETTINGS API. YOU WON'T BE ABLE TO USE THE SETTINGS
// API UNTIL YOU DO.
// ------------------------------------------------------------------------------

// Init plugin options to white list our options
function veuse_portfolio_init(){
	register_setting( 'veuse_portfolio_plugin_options', 'veuse_portfolio_options', 'veuse_portfolio_validate_options' );
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_menu', 'veuse_portfolio_add_options_page');
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_menu' HOOK FIRES, AND ADDS A NEW OPTIONS
// PAGE FOR YOUR PLUGIN TO THE SETTINGS MENU.
// ------------------------------------------------------------------------------

// Add menu page
function veuse_portfolio_add_options_page() {
	add_options_page('Veuse Portfolio Options Page', 'Veuse Portfolio', 'manage_options', __FILE__, 'veuse_portfolio_render_form');
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION SPECIFIED IN: add_options_page()
// ------------------------------------------------------------------------------
// THIS FUNCTION IS SPECIFIED IN add_options_page() AS THE CALLBACK FUNCTION THAT
// ACTUALLY RENDER THE PLUGIN OPTIONS FORM AS A SUB-MENU UNDER THE EXISTING
// SETTINGS ADMIN MENU.
// ------------------------------------------------------------------------------

// Render the Plugin options form
function veuse_portfolio_render_form() {
	?>
	<div class="wrap">

		<!-- Display Plugin Icon, Header, and Description -->
		<div class="icon32" id="icon-options-general"><br></div>
		<h2><?php _e('Veuse Portfolio Options','veuse-portfolio');?></h2>
		<p><?php _e('Settings for the portfolio-plugin.','veuse-portfolio');?></p>

		<h3><?php _e('Shortcode syntax','veuse-portfolio');?></h3>
		<code>[portfolio categories="" perpage="" order="" orderby="" columns="" pagination=""]</code>
		[portfolio categories="'.$terms.'" columns="'.$module_meta['columns'].'" type="'.$module_meta['type'].'" order="'.$module_meta['order'].'" orderby="' . $module_meta['orderby']. '" perpage="'.$module_meta['perpage'].'"]
		<ul>
			<li>Categories: category names separated by comma</li>
			<li>Columns: 1, 2, 3 or 4</li>
			<li>Type: pagination or filtered</li>
			<li>Order: ASC or DESC</li>
			<li>Orderby: title or date</li>
			<li>Perpage: number of posts to display per page</li>
		</ul>


		<h3><?php _e('Portfolio settings','veuse-portfolio');?></h3>
		<!-- Beginning of the Plugin Options Form -->
		<form method="post" action="options.php">
			<?php settings_fields('veuse_portfolio_plugin_options'); ?>
			<?php $options = get_option('veuse_portfolio_options'); ?>

			<!-- Table Structure Containing Form Controls -->
			<!-- Each Plugin Option Defined on a New Table Row -->
			<table class="form-table">


				<tr>
					<th scope="row"><strong><?php _e('Enable plugin stylesheet','veuse-portfolio');?></strong></th>
					<td>
						<input name="veuse_portfolio_options[css]" type="checkbox" <?php echo (isset($options['css']) ? 'checked="checked"' : ''); ?>/>
						<label for="veuse_portfolio_options[css]"><?php _e('Uncheck this if you want to use theme stylesheet to style the portfolio.','veuse-portfolio');?></label>
					</td>
				</tr>

				<tr>
					<th scope="row"><strong><?php _e('Enable lightbox','ceon');?></strong></th>
					<td>
						<input name="veuse_portfolio_options[lightbox]" type="checkbox" <?php echo (isset($options['lightbox']) ? 'checked="checked"' : ''); ?>/>
						<label for="veuse_portfolio_options[lightbox]"><?php _e('Check this if you want to add the lightbox feature when clicking on a portfolio thumbnail','veuse-portfolio');?></label>
					</td>
				</tr>

				<tr>
					<th scope="row"><strong><?php _e('Posts per page','ceon');?></strong></th>
					<td>
						<input name="veuse_portfolio_options[perpage]" type="text" value="<?php echo (!empty($options['perpage']) ? $options['perpage'] : '');?>"/>
						<label for="veuse_portfolio_options[perpage]"><?php _e('How many entries you want to display per page.','veuse-portfolio');?></label>
					</td>
				</tr>

				<tr>
					<th scope="row"><strong><?php _e('Layout','veuse-employees');?></strong></th>
					<td>
						<select name="veuse_portfolio_options[layout]">
							<option value="2" <?php if(isset($options['layout']) && $options['layout'] == 2 ) echo 'selected="selected"'; ?>><?php _e('2 columns','veuse-portfolio');?></option>
							<option value="3" <?php if(isset($options['layout']) && $options['layout'] == 3 ) echo 'selected="selected"'; ?>><?php _e('3 columns','veuse-portfolio');?></option>
							<option value="4" <?php if(isset($options['layout']) && $options['layout'] == 4 ) echo 'selected="selected"'; ?>><?php _e('4 columns','veuse-portfolio');?></option>
						</select>
						<label><?php _e('Select a layout to use in the portfolio-template and in taxonomy-template.','veuse-portfolio');?></label>
					</td>
				</tr>

			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>


	</div>
	<?php
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function veuse_portfolio_validate_options($input) {
	 // strip html from textboxes
	//$input['css'] =  	$options['css']; // Sanitize textarea input (strip html tags, and escape characters)
	//$input['lightbox'] =$options['lightbox']; // Sanitize textarea input (strip html tags, and escape characters)

	//$input['txt_one'] =  wp_filter_nohtml_kses($input['txt_one']); // Sanitize textbox input (strip html tags, and escape characters)
	return $input;
}

// Display a Settings link on the main Plugins page
function veuse_portfolio_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$veuse_portfolio_links = '<a href="'.get_admin_url().'options-general.php?page=plugin-options-starter-kit/plugin-options-starter-kit.php">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $veuse_portfolio_links );
	}

	return $links;
}


/* Post meta
=========================================================== */

add_action( 'add_meta_boxes', 'veuse_portfolio_meta_box_add' );

function veuse_portfolio_meta_box_add()
{
	add_meta_box( 'veuse_portfolio_meta', 'Project meta', 'veuse_portfolio_meta_box_cb', 'portfolio', 'normal', 'high' );
}

function veuse_portfolio_meta_box_cb( $post )
{
	$prefix = 'veuse_portfolio';

	$values = get_post_custom( $post->ID );
	$url = isset( $values[$prefix.'_website'] ) ? esc_attr( $values[$prefix.'_website'][0] ) : '';
	$credits = isset( $values[$prefix.'_credits'] ) ? esc_attr( $values[$prefix.'_credits'][0] ) : '';
	$client = isset( $values[$prefix.'_client'] ) ? esc_attr( $values[$prefix.'_client'][0] ) : '';
	$launch = isset( $values[$prefix.'_launch'] ) ? esc_attr( $values[$prefix.'_launch'][0] ) : '';
	wp_nonce_field( 'veuse_portfolio_nonce', 'meta_box_nonce' );?>
	<p>
		<label style="min-width:90px; display:inline-block;" for="<?php echo $prefix;?>_text"><?php _e('Project website','veuse-portfolio');?></label>
		<input type="text" name="<?php echo $prefix;?>_website" id="<?php echo $prefix;?>_website" value="<?php echo $url; ?>" />
		<span class="description"><?php _e('Enter url to the project website','veuse-portfolio');?></span>
	</p>


	<p>
		<label style="min-width:90px; display:inline-block;" for="<?php echo $prefix;?>_client"><?php _e('Client','veuse-portfolio');?></label>
		<input type="text" name="<?php echo $prefix;?>_client" id="<?php echo $prefix;?>_client" value="<?php echo $client; ?>" />
		<span class="description"><?php _e('Enter name of client','veuse-portfolio');?></span>
	</p>
	
	<p>
		<label style="min-width:90px; display:inline-block;" for="<?php echo $prefix;?>_launch"><?php _e('Launch','veuse-portfolio');?></label>
		<input type="text" name="<?php echo $prefix;?>_launch" id="<?php echo $prefix;?>_launch" value="<?php echo $launch; ?>" />
		<span class="description"><?php _e('Time of launch','veuse-portfolio');?></span>
	</p>

	
	<p>
		<label style="min-width:90px;  display:inline-block;" for="<?php echo $prefix;?>_text"><?php _e('Credits','veuse-portfolio');?></label>
		<input type="text" name="<?php echo $prefix;?>_credits" id="<?php echo $prefix;?>_credits" value="<?php echo $credits; ?>" />
		
	</p>
	<!--
	<p>
		<label for="my_meta_box_select">Color</label>
		<select name="<?php echo $prefix;?>_select" id="my_meta_box_select">
			<option value="red" <?php selected( $selected, 'red' ); ?>>Red</option>
			<option value="blue" <?php selected( $selected, 'blue' ); ?>>Blue</option>
		</select>
	</p>
	<p>
		<input type="checkbox" name="<?php echo $prefix;?>_check" id="<?php echo $prefix;?>_check" <?php checked( $check, 'on' ); ?> />
		<label for="<?php echo $prefix;?>_check">Don't Check This.</label>
	</p>-->
	<?php }


add_action( 'save_post', 'veuse_portfolio_meta_box_save' );


function veuse_portfolio_meta_box_save( $post_id ){

	$prefix = 'veuse_portfolio';

	// Bail if we're doing an auto save
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	// if our nonce isn't there, or we can't verify it, bail
	if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'veuse_portfolio_nonce' ) ) return;

	// if our current user can't edit this post, bail
	if( !current_user_can( 'edit_posts' ) ) return;

	// now we can actually save the data
	$allowed = array(
		'a' => array( // on allow a tags
			'href' => array() // and those anchors can only have href attribute
		)
	);

	// Probably a good idea to make sure your data is set
	if( isset( $_POST[$prefix.'_website'] ) )
		update_post_meta( $post_id, $prefix.'_website', wp_kses( $_POST[$prefix.'_website'], $allowed ) );
	else
		delete_post_meta($post_id, $prefix.'_website');
		
	// Probably a good idea to make sure your data is set
	if( isset( $_POST[$prefix.'_credits'] ) )
		update_post_meta( $post_id, $prefix.'_credits', wp_kses( $_POST[$prefix.'_credits'], $allowed ) );
	else
		delete_post_meta($post_id, $prefix.'_credits');
	
	// Probably a good idea to make sure your data is set
	if( isset( $_POST[$prefix.'_client'] ) )
		update_post_meta( $post_id, $prefix.'_client', wp_kses( $_POST[$prefix.'_client'], $allowed ) );
	else
		delete_post_meta($post_id, $prefix.'_client');
		
	// Probably a good idea to make sure your data is set
	if( isset( $_POST[$prefix.'_launch'] ) )
		update_post_meta( $post_id, $prefix.'_launch', wp_kses( $_POST[$prefix.'_launch'], $allowed ) );
	else
		delete_post_meta($post_id, $prefix.'_launch');

	/*
	if( isset( $_POST[$prefix.'_select'] ) )
		update_post_meta( $post_id, $prefix.'_select', esc_attr( $_POST[$prefix.'_select'] ) );
	*/
	// This is purely my personal preference for saving checkboxes
	//$chk = ( isset( $_POST[$prefix.'_website'] ) && $_POST[$prefix.'_website'] ) ? 'on' : 'off';


	//update_post_meta( $post_id, $prefix.'_website', $chk );
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







/* Filter the content to insert post meta-data */

if(!function_exists('veuse_portfolio_filter_content')){

	function veuse_portfolio_filter_content($content) {

		global $post;

		if( is_singular( 'portfolio') /* && is_main_query()*/ ) {

			/* Get meta into variables */
			$veuse_portfolio_options = get_option('veuse_portfolio_options');

			$image = get_the_post_thumbnail($post->ID, 'large');

			$categories = get_the_term_list($post->ID, 'portfolio-category','',', ','');
			$clients = get_the_term_list($post->ID, 'portfolio-client','',', ','');
			$link = get_post_meta($post->ID,'veuse_portfolio_website',true);

			$post = get_post($post->ID);

			$before_content  = '';
			$content = '';
			$after_content  = '';

			//$before_content .= '<p class="lead">'. $post->post_excerpt .'</p>';
			$content .= wpautop(do_shortcode($post->post_content)) ;


			$after_content .= '<ul class="portfolio-meta">';
				if($categories)	$after_content .= '<li><i class="icon-briefcase"></i> ' . $categories . '</li>';
	 			//if($clients)	$after_content .= '<li><i class="icon-bookmark"></i> '. $clients . '</li>';
	 			if($link)		$after_content .= '<li><i class="icon-external-link"></i> <a href="'.$link.'" rel="external">'. $link .'</a></li>';
			$after_content .= '</ul>';

			$content = $before_content . $content . $after_content;
			return $content;
		}

		else {

			return $content;
		}
	}

	add_filter('the_content', 'veuse_portfolio_filter_content', 1);
}

/* Insert retina image */

if(!function_exists('veuse_retina_interchange_image')){

	function veuse_retina_interchange_image($img_url, $width, $height, $crop){

		$imagepath = '<img src="'. mr_image_resize($img_url, $width, $height, $crop, 'c', false) .'" data-interchange="['. mr_image_resize($img_url, $width, $height, $crop, 'c', true) .', (retina)]" alt=""/>';
	
		return $imagepath;
	
	}
}


?>
