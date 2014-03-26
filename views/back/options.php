<?php

/* Plugin options */

// Set-up Action and Filter Hooks
register_activation_hook(__FILE__, 'veuse_portfolio_add_defaults');
register_uninstall_hook(__FILE__, 'veuse_portfolio_delete_plugin_options');
add_action('admin_init', 'veuse_portfolio_init' );
add_action('admin_menu', 'veuse_portfolio_add_options_page');
add_filter( 'plugin_action_links', 'veuse_portfolio_plugin_action_links', 10, 2 );


// Delete options table entries ONLY when plugin deactivated AND deleted
function veuse_portfolio_delete_plugin_options() {
	delete_option('veuse_portfolio_options');
}


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


// Init plugin options to white list our options
function veuse_portfolio_init(){
	register_setting( 'veuse_portfolio_plugin_options', 'veuse_portfolio_options', 'veuse_portfolio_validate_options' );
}


// Add menu page
function veuse_portfolio_add_options_page() {
	add_options_page('Veuse Portfolio Options Page', 'Veuse Portfolio', 'manage_options', __FILE__, 'veuse_portfolio_render_form');
}


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

?>