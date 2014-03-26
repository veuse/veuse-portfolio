<?php

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
?>