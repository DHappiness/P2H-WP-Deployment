<?php ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit( 3600 );

if ( ! isset( $_POST['action'] ) ) {
  include_once( 'include/updater.php' );
  include_once( 'include/settings-form.php' );
} else {
  
  include_once( 'include/settings-form.php' );
  
  // include configuration file
  include_once( 'include/config.php' );
  global $deployment_settings;
  
  // upload and unpack latest wordpress version
  include_once( 'include/BetterZipArchive.php' );
  
  $wordpress_url = 'https://wordpress.org/latest.zip';
  $zip_file = basename( $wordpress_url );
  $current_path = dirname(__FILE__);
  
  file_put_contents($zip_file, fopen($wordpress_url, 'r'));
  
  $zip = new BetterZipArchive;
  $res = $zip->open($zip_file);
  if ($res === TRUE) {
    $zip->extractSubdirTo( $current_path . '/', 'wordpress/' );
    $zip->close();
    unlink($zip_file);
  } else {
    echo 'ups!';
  }
  
  /**
  * Recursively copy files from one directory to another
  * 
  * @param String $src - Source of files being moved
  * @param String $dest - Destination of files being moved
  */
 function rcopy($src, $dest){
 
     // If source is not a directory stop processing
     if(!is_dir($src)) return false;
 
     // If the destination directory does not exist create it
     if(!is_dir($dest)) { 
         if(!mkdir($dest)) {
             // If the destination directory could not be created stop processing
             return false;
         }    
     }
 
     // Open the source directory to read in files
     $i = new DirectoryIterator($src);
     foreach($i as $f) {
         if($f->isFile()) {
             copy($f->getRealPath(), "$dest/" . $f->getFilename());
         } else if(!$f->isDot() && $f->isDir()) {
             rcopy($f->getRealPath(), "$dest/$f");
         }
     }
 }
  
  
  
  // import ACF PRO plugin and base wordpress theme from SVN ( or from include folder )
  $acf_path = $current_path . '/wp-content/plugins/advanced-custom-fields-pro/';
  $acf_svn_url = 'http://svn.w3.ua/Implementation/Development/Wordpress/Plugins/acf-addons/advanced-custom-fields-pro/';
  
  $base_theme_path = $current_path . '/wp-content/themes/base/';
  $base_theme_svn_url = 'http://svn.w3.ua/Implementation/Development/Wordpress/BaseTheme/base/';
  
  $custom_base_theme_location = 'include/base';
  
  function recursive_svn_uploading( $url, $upload_path ) {
    
    $context = stream_context_create(array (
        'http' => array (
            'header' => 'Authorization: Basic ' . base64_encode( $deployment_settings['svn_username'] . ":" . $deployment_settings['svn_password'] )
        )
    ));
    if ( $data = file_get_contents($url, false, $context) ) :
    
      $files = new SimpleXMLElement( $data );
      if ( $files->index->file ) {
        mkdir( $upload_path, 0755, true );
        foreach( $files->index->file as $file ) {
          $file = (array) $file;
          file_put_contents( $upload_path . $file['@attributes']['name'], fopen( $url . $file['@attributes']['name'], 'r', false, $context ) );
        }
      }
      if ( $files->index->dir ) {
        foreach( $files->index->dir as $dir ) {
          $dir = (array) $dir;
          recursive_svn_uploading( $url . $dir['@attributes']['href'], $upload_path . $dir['@attributes']['href'] );
        }
      }
      
    endif;
  }
  if ( in_array( 'acf', $deployment_settings['install_plugins'] ) ) {
    recursive_svn_uploading( $acf_svn_url, $acf_path );
  }
  if ( is_dir( $custom_base_theme_location ) ) {
    rcopy($custom_base_theme_location, $base_theme_path);
  } else {
    recursive_svn_uploading( $base_theme_svn_url, $base_theme_path );
  }
  
  rename( $base_theme_path . 'languages/base.pot', str_replace( 'base.pot', $_POST['dbname'] . '.pot', $base_theme_path . 'languages/base.pot' ) );
  rename( $base_theme_path, str_replace( '/base/', '/' . $_POST['dbname'] . '/', $base_theme_path ) );
  
  
  
  // wordpress installation
  $wp_salt = file_get_contents( 'https://api.wordpress.org/secret-key/1.1/salt/' );
  
  copy( 'wp-config-sample.php', 'wp-config.php' );
  
  if ( $default_wpconfig = file_get_contents( 'wp-config.php' ) ) {
    
    $edited_wpconfig = str_replace( array( 'database_name_here', 'username_here', 'password_here' ), array( $_POST['dbname'], $_POST['dbname'], $_POST['password'] ), $default_wpconfig );
    
    $wpconfig_lines = explode( "\n", $edited_wpconfig );
    $salt_lines = explode( "\n", $wp_salt );
    $updated_wpconfig_lines = array();
    $i = 1;
    foreach( $wpconfig_lines as $line ) {
      if ( $i >= 49 && $i <= 56 ) {
        foreach ( $salt_lines as $key => $salt ) {
          $line = $salt;
          unset( $salt_lines[$key] );
          break;
        }
      }
      $updated_wpconfig_lines[] = $line;
      $i++;
    }
    $edited_wpconfig = implode( "\n", $updated_wpconfig_lines );
    
    file_put_contents( 'wp-config.php', $edited_wpconfig );
  }
  
    //display installed user credentials
  function randomPassword() {
      $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
      $pass = array(); //remember to declare $pass as an array
      $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
      for ($i = 0; $i < 8; $i++) {
          $n = rand(0, $alphaLength);
          $pass[] = $alphabet[$n];
      }
      return implode($pass); //turn the array into a string
  }
  
  $user_name = 'admin';
  $user_email = 'admin@email.com';
  $user_pasword = randomPassword();
  
  include_once( 'wp-includes/class-phpass.php' );
  $wp_hasher = new PasswordHash(8, true); 
  $hashed_pass = $wp_hasher->HashPassword( trim( $user_pasword ) );
  
  echo "WP User: $user_name<br />User Password: $user_pasword<br />User Email: $user_email<br />";

  // import default db dump
  if ( $db_dump = file_get_contents( $current_path . '/include/default-db.sql' ) ) {
    $site_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    $db_dump = str_replace( array( 'SITENAME', 'http://siteurl', '<<userpass>>' ), array( $_POST['sitename'], $site_url, $hashed_pass ), $db_dump );
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    if ( $connection = mysqli_connect( 'localhost', ! empty( $_POST['dbuser'] ) ? $_POST['dbuser'] : $_POST['dbname'], $_POST['password'], $_POST['dbname'] ) ) {
      $lines = explode( "\n", $db_dump );
      $templine = '';
      // Loop through each line
      foreach ( $lines as $line ) {
        // Skip it if it's a comment
        if ( substr( $line, 0, 2 ) == '--' || $line == '' )
            continue;
        
        // Add this line to the current segment
        $templine .= $line;
        // If it has a semicolon at the end, it's the end of the query
        if ( substr( trim( $line ), -1, 1 ) == ';' ) {
            // Perform the query
            mysqli_query( $connection, $templine );
            // Reset temp variable to empty
            $templine = '';
        }
      }
      echo "Tables imported successfully";
    }
  }
  

  
  // delete deployment files
  if ( file_exists( sys_get_temp_dir() . '/staging-restrictions.php' ) ) {
    
    $it = new RecursiveDirectoryIterator('include', RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it,
                 RecursiveIteratorIterator::CHILD_FIRST);
    foreach($files as $file) {
        if ($file->isDir()){
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        }
    }
    rmdir('include');
    
    unlink('deployment.php');
  } else {
    echo "\nDeployment files aren't deleted!";
  }
  
}

sleep(1);
$time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
echo "<br /> Process Time: {$time}";
