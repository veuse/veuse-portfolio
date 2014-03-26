<?php

define('VEUSE_PORTFOLIO_IMAGES_URI', plugin_dir_url('veuse-portfolio').'veuse-portfolio/documentation/images');


// Set-up Action and Filter Hooks
add_action('admin_init', 'veuse_portfoliodocumentation_init' );
add_action('admin_menu', 'veuse_portfoliodocumentation_add_options_page');


// Init plugin options to white list our options
function veuse_portfoliodocumentation_init(){
	register_setting( 'veuse_portfoliodocumentation_plugin_options', 'veuse_portfoliodocumentation_options', 'veuse_portfoliodocumentation_validate_options' );
}


// Add menu page
function veuse_portfoliodocumentation_add_options_page() {
	add_submenu_page( 'edit.php?post_type=portfolio', __('Portfolio documentation page'), __('Documentation'), 'edit_themes', 'portfolio_documentation', 'veuse_portfoliodocumentation_render_form');

}



function get_all_portfolio_tabs(){
	
	 $tabs = array( 
    	
    	'intro' 		=> 'Intro', 
    	'categories' 	=> 'Portfolios', 
    	'posts'			=> 'Projects',
    	'display'		=> 'Adding a portfolio to a page'
    	
    	);
    return $tabs;
}

// Render the Plugin options form
function veuse_portfoliodocs_admin_tabs( $current = 'intro' ) {

    
    $tabs = get_all_portfolio_tabs();  
     
    echo '<h3 class="nav-tab-wrapper" style="padding-left:0; border-bottom:0;">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' style='padding-top:6px; margin:0px -1px -1px 0; border:1px solid #ccc;' href='?post_type=portfolio&page=portfolio_documentation&tab=$tab'>$name</a>";

    }
    echo '</h3>';
}


function veuse_portfoliodocumentation_render_form(){


    $plugin_name = 'Veuse Portfolio';

	
	
	
	?>
	<style>
		#veuse-portfoliodocumentation-wrapper a { text-decoration: none;}
		#veuse-portfoliodocumentation-wrapper img { max-width: 100%; height: auto; margin-bottom: 20px; border:1px solid #e1e1e1; padding:10px; box-sizing: border-box; background: #fff;}
		#veuse-portfoliodocumentation-wrapper hr { margin:20px 0;}
		#veuse-portfoliodocumentation-wrapper p {  }
		#veuse-portfoliodocumentation-wrapper ul { margin-bottom: 30px !important;}
		ul.inline-list { list-style: disc !important; list-style-position: inside;}
		ul.inline-list li { display: inline; margin-right: 10px; list-style: disc;}
		ul.inline-list li:after { content:'-'; margin-left: 10px; }
	</style>
	<div class="wrap">

		<div class="icon32" id="icon-options-general"><br></div>
		<h2><?php echo $plugin_name;?> <?php _e('documentation','veuse-portfoliodocumentation');?></h2>
		<p><?php
			
			echo sprintf( __( 'Here you find instructions on how to use the %s plugin. For more in-depth info, please check out http://veuse.com/support.', 'veuse-portfoliodocumentation' ), $plugin_name);?>
		</p>
		
		<?php
		
		$docpath = get_stylesheet_directory().'/includes/portfoliodocumentation';
		
		if ( isset ( $_GET['tab'] ) ) veuse_portfoliodocs_admin_tabs($_GET['tab']); else veuse_portfoliodocs_admin_tabs('intro'); ?>
		
		<div id="veuse-portfoliodocumentation-wrapper" style="padding:20px 0; max-width:800px;">	

		<?php
		
		if ( isset ( $_GET['tab'] ) ) $tab = $_GET['tab']; else $tab = 'intro';
		

			
			switch ($tab ) {	
				
				
				case $tab :
				
					echo '<div>';
					
					//$text = file_get_contents($docpath."/pages/$tab.php");		
					//echo nl2br($text);
					
					include("pages/$tab.php");
										
					echo '</div>';
					
					break;
				
			} // end switch			
			

	
		?>
		<div>
		<br>
		<hr>
		<br>
		<a href="http://veuse.com/support" class="button">Support forum</a>
		</div>
		</div>
		
	</div>
	<?php
}
?>