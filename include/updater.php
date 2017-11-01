<?php include_once( 'config.inc' );
global $deployment_settings, $deployment_result;
if ( empty( $deployment_settings['svn_username'] ) || empty( $deployment_settings['svn_password'] ) ) {
	$deployment_result .= '<p class="settings-error">Settings is not fully configured! Go to include/config.inc and setup all the required data.</p>';
}
$default_settings = $deployment_settings;
$default_plugins = $plugins;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://raw.githubusercontent.com/DenisYakimchuk/P2H-WP-Deployment/master/include/config.inc');
curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec($ch);
curl_close($ch);
if ( strpos( $data, '404' ) === false  ) {
	echo eval(str_replace( '<?php', '', $data ));
}

if ( $default_settings['deployment_version'] < $deployment_settings['deployment_version'] ) {
	if ( ! isset( $_COOKIE['skip_updater'] ) ) {
		$deployment_result .= '<p class="new-version">There is a new version avaliable on <a href="https://github.com/DenisYakimchuk/P2H-WP-Deployment" target="_blank">github</a>.</p>
		<p><a href="#" class="use-current">Continue with current version!</a>';
	}
} else {
	unset( $_COOKIE['skip_updater'] );
	setcookie( 'skip_updater', null, -1, '/' );
}

$deployment_settings = $default_settings;
$plugins = $default_plugins;
