<?php include_once( 'config.php' );
global $deployment_settings;
if ( empty( $deployment_settings['svn_username'] ) || empty( $deployment_settings['svn_password'] ) ) {
	echo '<p style="color:red; font-size: 22px;">Settings is not fully configured! Go to include/config.php and set all required data.</p>';
}
$default_settings = $deployment_settings;

function get_content_from_github($url) {
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1);
	$content = curl_exec($ch);
	curl_close($ch);
	return $content;
}

$test = json_decode(get_content_from_github('https://github.com/DenisYakimchuk/P2H-WP-Deployment/blob/master/include/config.php'));

var_dump( $test );