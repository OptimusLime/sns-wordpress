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
require AWS_CONFIGURE;

use Aws\Sns\SnsClient;



function debug_to_console( $data ) {

    if ( is_array( $data ) )
        $output = "<script>console.log( 'PHP: " . implode( ',', $data) . "' );</script>";
    else
        $output = "<script>console.log( 'PHP: " . $data . "' );</script>";

    echo $output;
}




// This just echoes the chosen line, we'll position it later
function hello_sns() {
	
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
		'Message' => 'Testing admin send',
		'Subject' => 'PHP'
	));
	$mid = $result['MessageId'];
	debug_to_console("OH GOD IS ANYONE THERE");
	debug_to_console($mid);
	
	echo "<p id='sns'>$mid</p>";
}

// Now we set that function up to execute when the admin_notices action is called
add_action( 'admin_notices', 'hello_sns' );

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

?>
