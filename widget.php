<?php

class VeusePortfolioWidget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'veuse_portfolio_widget', // Base ID
			__('Portfolio (Veuse)','veuse-portfolio'), // Name
			array( 'description' => __( 'Add a portfolio in your page or post', 'text_domain' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$grid = $instance['grid'];
		$portfolio = $instance['portfolio'];
		$type = $instance['type'];
		$perpage = $instance['perpage'];
		$orderby = $instance['orderby'];	
		$order = $instance['order'];	

		$portfolio = rtrim($portfolio, ',');
		
		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;	
			 // Do Your Widgety Stuff Here…
			echo do_shortcode('[veuse_portfolio categories="'. $portfolio .'" columns="' . $grid . '" type="' . $type . '" orderby="'.$orderby.'" order="'.$order.'" perpage="'.$perpage.'"]');
		
		echo $after_widget;
	}


	public function update( $new_instance, $old_instance ) {
		
		$instance = array();
				
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['portfolio'] = strip_tags( $new_instance['portfolio'] );
		$instance['perpage'] = strip_tags( $new_instance['perpage'] );
		$instance['type'] = strip_tags( $new_instance['type'] );
		$instance['grid'] = strip_tags( $new_instance['grid'] );
		$instance['orderby'] = strip_tags( $new_instance['orderby'] );
		$instance['order'] = strip_tags( $new_instance['order'] );
		
		return $instance;
	}

	 
	public function form( $instance ) {
	
		global $widget, $wp_widget_factory, $wp_query;
		
		if ( isset( $instance[ 'title' ] ) ) $title = $instance[ 'title' ]; else $title = __( '', 'text_domain' );	
		if ( isset( $instance[ 'portfolio' ] ) ) $portfolio = $instance[ 'portfolio' ]; else $portfolio = '';
		if ( isset( $instance[ 'perpage' ] ) ) $perpage = $instance[ 'perpage' ]; else $perpage = __( '-1', 'text_domain' );
		if ( isset( $instance[ 'grid' ] ) ) $grid = $instance[ 'grid' ]; else $grid = __( '3', 'text_domain' );
		if ( isset( $instance[ 'type' ] ) ) $type = $instance[ 'type' ]; else $type = __( 'none', 'text_domain' );
		if ( isset( $instance[ 'orderby' ] ) ) $orderby = $instance[ 'orderby' ]; else $orderby = __( 'date', 'text_domain' );
		if ( isset( $instance[ 'order' ] ) ) $order = $instance[ 'order' ]; else $order = __( 'ASC', 'text_domain' );
	
		?>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<style>
			.portfolioselector-wrapper {
				
				padding:10px; background: #fff; border:1px solid #eee; overflow: scroll; max-height:180px;
				
			}
			
			.portfolioselector-wrapper a { 
				padding:3px 10px 3px 0px;  display: block; margin: 0; cursor: pointer; text-decoration: none;
				border-bottom:1px dotted #d4d4d4;
			}
			
			.portfolioselector-wrapper a:hover { color:#2a95c5;}
						
			.portfolioselector-wrapper a:after {
					content:'';
					color:#999;
					float:right;
					font-weight: bold;
				} 
			.portfolioselector-wrapper a.active { font-weight: bold; color:#de4b29;}
			.portfolioselector-wrapper a.active:after {
					content:'x';
					color:#de4b29;
				
				} 
		</style>
		
		<label for="<?php echo $this->get_field_id( 'portfolio' ); ?>"><?php _e( "Select portfolios:",'veuse-pagelist' ); ?></label> 
		<div class="portfolioselector-wrapper" style="margin-bottom:20px;">
		<?php
		
		$portfolio_array = explode(',', $portfolio);
		
		$terms = get_terms( 'portfolio-category', array('hide_empty' => 1 ));
        
                 
        
        if( $terms ){
                              
            foreach( $terms as $term ){
            	?>

            	<a href="#" data-portfolio-id="<?php echo $term->slug;?>"> <?php echo $term->name;?></a>
            	<?php
     
            }
            
        }

		?>
		</div>
		
		<input id="<?php echo $this->get_field_id( 'portfolio' ); ?>" name="<?php echo $this->get_field_name( 'portfolio' ); ?>" type="hidden" value="<?php echo esc_attr( $portfolio );?>" />
		

		
		<p>
			<label style="min-width:60px;"  for="<?php echo $this->get_field_id('grid');?>"><?php _e('Grid:','veuse-pagelist');?></label>
			<select name="<?php echo $this->get_field_name('grid');?>">
		  		<option value="1" <?php selected( $grid, '1' , true); ?>><?php _e('1 column','veuse-pagelist');?></option>
		  		<option value="2" <?php selected( $grid, '2' , true); ?>><?php _e('2 columns','veuse-pagelist');?></option>	
		  		<option value="3" <?php selected( $grid, '3' , true); ?>><?php _e('3 columns','veuse-pagelist');?></option>	
		  		<option value="4" <?php selected( $grid, '4' , true); ?>><?php _e('4 columns','veuse-pagelist');?></option>		  
		  	</select>
		</p>
		
		
		<p>
			<label style="min-width:60px;"  for="<?php echo $this->get_field_id('type');?>"><?php _e('Type:','veuse-pagelist');?></label>
			<select name="<?php echo $this->get_field_name('type');?>">
		  		<option value="pagination" <?php selected( $type, 'pagination' , true); ?>><?php _e('Pagination','veuse-pagelist');?></option>
		  		<option value="filtered" <?php selected( $type, 'filtered' , true); ?>><?php _e('Filtered','veuse-pagelist');?></option>	
		  		<option value="none" <?php selected( $type, 'none' , true); ?>><?php _e('None','veuse-pagelist');?></option>	  
		  	</select>
		</p>
		
	
		 
		
		<p>
		<label style="min-width:60px;" for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e( "Order by:",'veuse-pagelist' ); ?></label> 

			
			<select name="<?php echo $this->get_field_name('orderby');?>">
		  		<option value="title" <?php selected( $orderby, 'title' , true); ?>><?php _e('Post title','veuse-pagelist');?></option>
		  		<option value="date" <?php selected( $orderby, 'date' , true); ?>><?php _e('Post date','veuse-pagelist');?></option>	
		  	
		  	</select>
			
		</p>
		
		<p>
		<label style="min-width:60px;" for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( "Order:",'veuse-pagelist' ); ?></label> 

			
			<select name="<?php echo $this->get_field_name('order');?>">
		  		<option value="ASC" <?php selected( $order, 'ASC' , true); ?>><?php _e('Ascending','veuse-pagelist');?></option>
		  		<option value="DESC" <?php selected( $order, 'DESC' , true); ?>><?php _e('Descending','veuse-pagelist');?></option>	
		  	
		  	</select>
			
		</p>
		
		<p>
		<label style="min-width:60px;"  for="<?php echo $this->get_field_id( 'perpage' ); ?>"><?php _e( "Per page:",'veuse-pagelist' ); ?></label> 
			<input size="6" id="<?php echo $this->get_field_id( 'perpage' ); ?>" name="<?php echo $this->get_field_name( 'perpage' ); ?>" type="text" value="<?php echo esc_attr( $perpage ); ?>" />
			<small><?php _e( "To show all, enter -1",'veuse-pagelist' ); ?></small>
		</p>
		
		<p>
		<label style="min-width:60px;"  for="<?php echo $this->get_field_id( 'imagesize' ); ?>"><?php _e( "Image size:",'veuse-pagelist' ); ?></label> 
			<input size="6" id="<?php echo $this->get_field_id( 'imagesize' ); ?>" name="<?php echo $this->get_field_name( 'imagesize' ); ?>" type="text" value="<?php echo esc_attr( $imagesize ); ?>" />
			<small><?php _e( "width * height",'veuse-pagelist' ); ?></small>
		</p>
		
		<?php

	}

} 

add_action('widgets_init',create_function('','return register_widget("VeusePortfolioWidget");'));
 
?>