<?php

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
			?>