<?php
/**
 * @package SNS_Wordpress
 * @version 0.1
 */
/*
Plugin Name: SNS Wordpress
Plugin URI: http://wordpress.org/plugins/sns-wordpress/
Description: A plugin for using AWS from Wordpress. Send SNS and stuff. 
Author: Paul Szerlip
Version: 0.1
Author URI: http://designforcode.com
*/
require_once AWS_CONFIGURE;

use Aws\Sns\SnsClient;



function debug_to_console( $data ) {

    if ( is_array( $data ) )
        $output = "<script>console.log( 'PHP: " . implode( ',', $data) . "' );</script>";
    else
        $output = "<script>console.log( 'PHP: " . $data . "' );</script>";

    echo $output;
}




// This just echoes the chosen line, we'll position it later
function hello_sns($subject, $message) {
	
	//predefined access variables
	$snsClient = SnsClient::factory(array(
		'key'    => AWS_ACCESS_KEY,
		'secret' => AWS_ACCESS_SECRET,
		'region' => AWS_ACCESS_REGION,
	));
	
	//debug_to_console($snsClient);
	
	$result = $snsClient->publish(array(
		'TopicArn' => SNS_PUSH_TOPIC_ARN,
		// Message is required
		'Message' => $message,
		'Subject' => $subject
	));
	
	return $result['MessageId'];
	//debug_to_console("OH GOD IS ANYONE THERE");
	//debug_to_console($mid);
	
	//echo "<p id='sns'>$mid</p>";
}

// Now we set that function up to execute when the admin_notices action is called
//add_action( 'admin_notices', 'hello_sns' );

// We need some CSS to position the paragraph
function dummy_css() {
	// This makes sure that the positioning is also good for right-to-left languages
	$x = is_rtl() ? 'left' : 'right';

	echo "
	<style type='text/css'>
	#sns {
		float: $x;
		padding-$x: 15px;
		padding-top: 5px;		
		margin: 0;
		font-size: 11px;
	}
	</style>
	";
}

add_action( 'admin_head', 'dummy_css' );


/** Step 2 (from text above). */
add_action( 'admin_menu', 'my_plugin_menu' );

/** Step 1. */
function my_plugin_menu() {

	$menu_title = "Pushes";
	$page_title = "SNS Configuration";
	$capability = 'manage_options';
	$menu_slug = 'AWS_PUSH_PLUGIN';
	$function = 'my_plugin_options';
	//$icon_url, $position 
	add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function);
	//add_options_page( 'My Plugin Options', 'My Plugin', 'manage_options', 'my-unique-identifier', 'my_plugin_options' );
}

/** Step 3. */
function my_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	//echo '<div class="wrap">';
	//echo '<p>Here is where the form would go if I actually had options.</p>';
	//echo '</div>'; 
	
	
	?>
	<div class="wrap">
  <div id="icon-options-general" class="icon32"><br /></div>
  <script>
  
		jQuery(function() {
			jQuery('#pushForm').submit(function() {
			
				console.log('Dumbass! Pressed button!');
				console.log("<?php echo $_SERVER['PHP_SELF']; ?>");
				jQuery.ajax({
					type: 'POST',
					url: "<?php echo $_SERVER['PHP_SELF']; ?>?page=AWS_PUSH_PLUGIN",
					data: { subject: jQuery("#subjectText").val() || "test", 
							message: jQuery("#messageText").val() || "empty" }
				});		
				
				return false;
			}); 
		})
  
  </script>
  <h2>Push Testing Page</h2>
	 <form id="pushForm">
	 <?php 
		if(isset($_POST['subject'])) 
		{
		debug_to_console($_POST);
		  debug_to_console("Call from php after post");
		  print_r($_POST);
		  $mID = hello_sns($_POST['subject'], $_POST['message']);
		}		
	 
	 ?>
	 
	  <input type="text" id="subjectText" placeholder="Subject" /> 
	  <input type="text" id="messageText" placeholder="Message" /> 
	  <input type="hidden" name="c" value="3" /> 
	  <input type="submit" /> 
	</form>
</div>
<?php
	
	
	
	
	
}

?>
