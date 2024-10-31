<?php 
	$updated = false;
	if(!current_user_can('manage_options')) {
		wp_die("Cheating, eh?");
	}
	if(isset($_POST['action'])) {
		if(!isset($_POST['_wpnonce'])) wp_die("NONCE Failure. Please log out and back in.");
		if(!check_admin_referer('pvapi_options')) wp_die("Replay forbidden (if only the NFL would impelment this!).");
		if($_POST['action'] == "update") {
			update_option("pvapi_key",$_POST['pvapi_key']);
			update_option("pvapi_tb",$_POST['pvapi_tb']);
			update_option("pvapi_camp",$_POST['pvapi_camp']);
			update_option("pvapi_length",$_POST['pvapi_length']);
			$updated = true;
		}
	}
?>
<div class="wrap">
	<h2>PinkVisual API Integration Options</h2>
	<?php if($updated): ?>
		<div id="message" class="updated">
			<p><strong>Settings Updated</strong></p>
		</div>
	<?php endif; ?>
	<p>
In order to get the most out of the PinkVisual API, please fill in the following options.
If you need to create an API account, visit the <a href="http://api.pinkvisual.com/signup/?code=">
PinkVisual API signup</a>. After signing in, generate a an API key and add it below.
	</p>
	<p>
Would you like to use your own personal linking code for TopBucks? Simply
visit <a href="http://referral.topbucks.com/?revid=66553">TopBucks</a> and signup for an account.
Once you have an account, enter you username (a 5 digit number) in the "Top Bucks Key" field.
	</p>
	<p>
Want to get started quickly? Just leave these values alone and start posting using the <code>[pvapi]</code>
shortcode in your posts. Or, click on the "Add PinkVisual Media" button above the post editor (right next to 
the "Add Image" button) to get a list of avialble media.
	</p>
	<form id="pvapi_options" name="pvapi_options" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post">
	<table class="form-table"><tbody>
		<tr valign="top">
			<th scope="row">PinkVisual API Key</th>
			<td>
				<input type="textbox" name="pvapi_key" id="pvapi_key" value="<?php echo get_option("pvapi_key",""); ?>" />
				<label for="pvapi_key">(Optional) Your personal PinkVisual API Key</label>
				<br />
				<span class="explanatory-text">If you would like to use your own PinkVisual API Key, please enter it here.</span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">TopBucks Key</th>
			<td>
				<input type="textbox" name="pvapi_tb" id="pvapi_tb" value="<?php echo get_option("pvapi_tb",""); ?>" />
				<label for="pvapi_tb">(Optional) Your personal TopBucks ID</label>
				<br />
				<span class="explanatory-text">Your TopBucks id. If you need one, sign up at <a href="http://referral.topbucks.com/?revid=66553">TopBucks</a>.</span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Campaign ID</th>
			<td>
				<input type="textbox" name="pvapi_camp" id="pvapi_camp" value="<?php echo get_option("pvapi_camp",""); ?>" />
				<label for="pvapi_camp">(Optional) A campaign ID appended to linking codes.</label>
				<br />
				<span class="explanatory-text">A campaign ID appended to linking codes to allow for more advanced statistics.</span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Default List Length</th>
			<td>
				<input type="textbox" name="pvapi_length" id="pvapi_length" value="<?php echo get_option("pvapi_length","10"); ?>" />
				<label for="pvapi_key">How many items appear in lists (by default).</label>
				<br />
				<span class="explanatory-text">How many items should appear in lists (by default). This can be overridden by the <code>num</code> command.</span>
			</td>
		</tr>
	</tbody></table>
    <p class="submit">
    	<input type="hidden" name="action" value="update" />
        <?php wp_nonce_field('pvapi_options'); ?>
    	<input type="submit" name="Submit" value="Configureate >" class="button" />
    </p>
	</form>
</div>