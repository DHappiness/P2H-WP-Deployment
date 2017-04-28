<?php include_once( 'config.inc' );
global $deployment_settings;
if ( empty( $deployment_settings['svn_username'] ) || empty( $deployment_settings['svn_password'] ) ) {
	echo '<p style="color:red; font-size: 22px;">Settings is not fully configured! Go to include/config.php and setup all the required data.</p>';
}
$default_settings = $deployment_settings;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://raw.githubusercontent.com/DenisYakimchuk/P2H-WP-Deployment/master/include/config.inc');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec($ch);
curl_close($ch);
if ( strpos( $data, '404' ) === false  ) {
	echo eval(str_replace( '<?php', '', $data ));
}

if ( $default_settings['deployment_version'] < $deployment_settings['deployment_version'] ) {
	echo '<p style="color: #3333cc; font-size: 20px;">There is a new version avaliable on github.</p>';
}
