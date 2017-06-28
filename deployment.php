<?php ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit( 3600 );

// include configuration file
include( 'include/config.inc' );

if ( ! isset( $_POST['action'] ) ) {
  include_once( 'include/updater.php' );
  include_once( 'include/settings-form.php' );
} else {
  
  //include_once( 'include/settings-form.php' );
  
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
 
  // delete default themes
  $it = new RecursiveDirectoryIterator('wp-content/themes', RecursiveDirectoryIterator::SKIP_DOTS);
  $files = new RecursiveIteratorIterator($it,
               RecursiveIteratorIterator::CHILD_FIRST);
  foreach($files as $file) {
      if ($file->isDir()){
          rmdir($file->getRealPath());
      } else {
          unlink($file->getRealPath());
      }
  }
  
  
  
  // import ACF PRO plugin and base wordpress theme from SVN ( or from include folder )
  $acf_path = $current_path . '/wp-content/plugins/advanced-custom-fields-pro/';
  $acf_svn_url = 'http://svn.w3.ua/Implementation/Development/Wordpress/Plugins/acf-addons/advanced-custom-fields-pro/';
  
  $base_theme_path = $current_path . '/wp-content/themes/base/';
  $base_theme_svn_url = 'http://svn.w3.ua/Implementation/Development/Wordpress/BaseTheme/base/';
  
  $custom_base_theme_location = 'include/base';
  
  function recursive_svn_uploading( $url, $upload_path, $deployment_settings ) {
    
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
          recursive_svn_uploading( $url . $dir['@attributes']['href'], $upload_path . $dir['@attributes']['href'], $deployment_settings );
        }
      }
      
    endif;
  }
  if ( in_array( 'acf', $deployment_settings['install_plugins'] ) ) {
    recursive_svn_uploading( $acf_svn_url, $acf_path, $deployment_settings );
  }
  if ( is_dir( $custom_base_theme_location ) ) {
    rcopy( $custom_base_theme_location, $base_theme_path );
  } else {
    recursive_svn_uploading( $base_theme_svn_url, $base_theme_path, $deployment_settings );
  }
  
  rename( $base_theme_path . 'languages/base.pot', str_replace( 'base.pot', $_POST['dbname'] . '.pot', $base_theme_path . 'languages/base.pot' ) );
  $new_theme_path = str_replace( '/base/', '/' . $_POST['dbname'] . '/', $base_theme_path );
  rename( $base_theme_path, $new_theme_path );
  if ( ! file_exists( $new_theme_path . 'style.css' ) ) {
    $style_css_content_sign = 'LyoNClRoZW1lIE5hbWU6IDw8QmFzZT4+DQpBdXRob3I6IEFub255bW91cw0KQXV0aG9yIFVSSToNClZlcnNpb246IDENCkRlc2NyaXB0aW9uOiBCYXNlIHRoZW1lIGZvciBXb3JkcHJlc3MNCkxpY2Vuc2U6IEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIHYyIG9yIGxhdGVyDQpMaWNlbnNlIFVSSTogaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbA0KVGV4dCBEb21haW46IDw8YmFzZT4+DQpUYWdzOiBvbmUtY29sdW1uLCB0d28tY29sdW1ucw0KVGhlbWUgVVJJOg0KKi8=';
    $style_css_content = str_replace( array( '<<base>>', '<<Base>>' ), array( $_POST['dbname'], $_POST['sitename'] ), base64_decode($style_css_content_sign) );
    file_put_contents( $new_theme_path . 'style.css', $style_css_content );
  }
  
  
  
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
  $user_password = randomPassword();
  
  include_once( 'wp-includes/class-phpass.php' );
  $wp_hasher = new PasswordHash(8, true); 
  $hashed_pass = $wp_hasher->HashPassword( trim( $user_password ) );
  
  echo "WP User: $user_name<br />User Password: $user_password<br />User Email: $user_email<br />";

  // import default db dump
  if ( $db_dump = file_get_contents( $current_path . '/include/default-db.sql' ) ) {
    $site_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    $db_dump = str_replace( array( 'SITENAME', 'http://siteurl', '<<userpass>>', '<<template>>', '<<stylesheet>>' ), array( $_POST['sitename'], $site_url, $hashed_pass, $_POST['dbname'], $_POST['dbname'] ), $db_dump );
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    if ( $connection = mysqli_connect( 'localhost', ! empty( $_POST['dbuser'] ) ? $_POST['dbuser'] : $_POST['dbname'], $_POST['password'], $_POST['dbname'] ) ) {
      mysqli_query( $connection, "DROP TABLE IF EXISTS `wp_commentmeta`, `wp_comments`, `wp_links`, `wp_options`, `wp_postmeta`, `wp_posts`, `wp_termmeta`, `wp_terms`, `wp_term_relationships`, `wp_term_taxonomy`, `wp_usermeta`, `wp_users`;" );
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
  
  // load WP API
  include_once( 'wp-load.php' );
  
  // create file with cpt and taxonomy registration
  if ( ! empty( $_POST['cpt'] ) ) {
    $cpt_code_sign = 'CQkkbGFiZWxzID0gYXJyYXkoDQoJCSAgICAnbmFtZScgICAgICAgICAgICAgICA9PiBfeCggJ0Jvb2tzJywgJ3Bvc3QgdHlwZSBnZW5lcmFsIG5hbWUnLCAnYmFzZScgKSwNCgkJICAgICdzaW5ndWxhcl9uYW1lJyAgICAgID0+IF94KCAnQm9vaycsICdwb3N0IHR5cGUgc2luZ3VsYXIgbmFtZScsICdiYXNlJyApLA0KCQkgICAgJ21lbnVfbmFtZScgICAgICAgICAgPT4gX3goICdCb29rcycsICdhZG1pbiBtZW51JywgJ2Jhc2UnICksDQoJCSAgICAnbmFtZV9hZG1pbl9iYXInICAgICA9PiBfeCggJ0Jvb2snLCAnYWRkIG5ldyBvbiBhZG1pbiBiYXInLCAnYmFzZScgKSwNCgkJICAgICdhZGRfbmV3JyAgICAgICAgICAgID0+IF94KCAnQWRkIE5ldycsICdib29rJywgJ2Jhc2UnICksDQoJCSAgICAnYWRkX25ld19pdGVtJyAgICAgICA9PiBfXyggJ0FkZCBOZXcgQm9vaycsICdiYXNlJyApLA0KCQkgICAgJ25ld19pdGVtJyAgICAgICAgICAgPT4gX18oICdOZXcgQm9vaycsICdiYXNlJyApLA0KCQkgICAgJ2VkaXRfaXRlbScgICAgICAgICAgPT4gX18oICdFZGl0IEJvb2snLCAnYmFzZScgKSwNCgkJICAgICd2aWV3X2l0ZW0nICAgICAgICAgID0+IF9fKCAnVmlldyBCb29rJywgJ2Jhc2UnICksDQoJCSAgICAnYWxsX2l0ZW1zJyAgICAgICAgICA9PiBfXyggJ0FsbCBCb29rcycsICdiYXNlJyApLA0KCQkgICAgJ3NlYXJjaF9pdGVtcycgICAgICAgPT4gX18oICdTZWFyY2ggQm9va3MnLCAnYmFzZScgKSwNCgkJICAgICdwYXJlbnRfaXRlbV9jb2xvbicgID0+IF9fKCAnUGFyZW50IEJvb2tzOicsICdiYXNlJyApLA0KCQkgICAgJ25vdF9mb3VuZCcgICAgICAgICAgPT4gX18oICdObyBib29rcyBmb3VuZC4nLCAnYmFzZScgKSwNCgkJICAgICdub3RfZm91bmRfaW5fdHJhc2gnID0+IF9fKCAnTm8gYm9va3MgZm91bmQgaW4gVHJhc2guJywgJ2Jhc2UnICkNCgkJKTsNCgkJJGFyZ3MgPSBhcnJheSgNCgkJICAgICdsYWJlbHMnICAgICAgICAgICAgID0+ICRsYWJlbHMsDQoJCSAgICAncHVibGljJyAgICAgICAgICAgICA9PiB0cnVlLA0KCQkgICAgJ3B1YmxpY2x5X3F1ZXJ5YWJsZScgPT4gdHJ1ZSwNCgkJICAgICdzaG93X3VpJyAgICAgICAgICAgID0+IHRydWUsDQoJCSAgICAnc2hvd19pbl9tZW51JyAgICAgICA9PiB0cnVlLA0KCQkgICAgJ3F1ZXJ5X3ZhcicgICAgICAgICAgPT4gdHJ1ZSwNCgkJICAgICdyZXdyaXRlJyAgICAgICAgICAgID0+IGFycmF5KCAnc2x1ZycgPT4gJzw8Ym9vaz4+JyApLA0KCQkgICAgJ2NhcGFiaWxpdHlfdHlwZScgICAgPT4gJ3Bvc3QnLA0KCQkgICAgJ2hhc19hcmNoaXZlJyAgICAgICAgPT4gdHJ1ZSwNCgkJICAgICdoaWVyYXJjaGljYWwnICAgICAgID0+IGZhbHNlLA0KCQkgICAgJ21lbnVfcG9zaXRpb24nICAgICAgPT4gbnVsbCwNCgkJICAgICdzdXBwb3J0cycgICAgICAgICAgID0+IGFycmF5KCAndGl0bGUnLCAnZWRpdG9yJywgJ2F1dGhvcicsICd0aHVtYm5haWwnLCAnZXhjZXJwdCcsICdjb21tZW50cycgKQ0KCQkpOw0KCQlyZWdpc3Rlcl9wb3N0X3R5cGUoICc8PGJvb2s+PicsICRhcmdzICk7';
    $cpt_code = base64_decode( $cpt_code_sign );
    $taxonomy_code_sign = 'CQkkbGFiZWxzID0gYXJyYXkoDQoJCQknbmFtZScgICAgICAgICAgICAgID0+IF94KCAnR2VucmVzJywgJ3RheG9ub215IGdlbmVyYWwgbmFtZScgKSwNCgkJCSdzaW5ndWxhcl9uYW1lJyAgICAgPT4gX3goICdHZW5yZScsICd0YXhvbm9teSBzaW5ndWxhciBuYW1lJyApLA0KCQkJJ3NlYXJjaF9pdGVtcycgICAgICA9PiBfXyggJ1NlYXJjaCBHZW5yZXMnICksDQoJCQknYWxsX2l0ZW1zJyAgICAgICAgID0+IF9fKCAnQWxsIEdlbnJlcycgKSwNCgkJCSdwYXJlbnRfaXRlbScgICAgICAgPT4gX18oICdQYXJlbnQgR2VucmUnICksDQoJCQkncGFyZW50X2l0ZW1fY29sb24nID0+IF9fKCAnUGFyZW50IEdlbnJlOicgKSwNCgkJCSdlZGl0X2l0ZW0nICAgICAgICAgPT4gX18oICdFZGl0IEdlbnJlJyApLA0KCQkJJ3VwZGF0ZV9pdGVtJyAgICAgICA9PiBfXyggJ1VwZGF0ZSBHZW5yZScgKSwNCgkJCSdhZGRfbmV3X2l0ZW0nICAgICAgPT4gX18oICdBZGQgTmV3IEdlbnJlJyApLA0KCQkJJ25ld19pdGVtX25hbWUnICAgICA9PiBfXyggJ05ldyBHZW5yZSBOYW1lJyApLA0KCQkJJ21lbnVfbmFtZScgICAgICAgICA9PiBfXyggJ0dlbnJlcycgKSwNCgkJKTsNCgkJJGFyZ3MgPSBhcnJheSgNCgkJCSdoaWVyYXJjaGljYWwnICAgICAgPT4gdHJ1ZSwNCgkJCSdsYWJlbHMnICAgICAgICAgICAgPT4gJGxhYmVscywNCgkJCSdzaG93X3VpJyAgICAgICAgICAgPT4gdHJ1ZSwNCgkJCSdzaG93X2FkbWluX2NvbHVtbicgPT4gdHJ1ZSwNCgkJCSdxdWVyeV92YXInICAgICAgICAgPT4gdHJ1ZSwNCgkJCSdyZXdyaXRlJyAgICAgICAgICAgPT4gYXJyYXkoICdzbHVnJyA9PiAnZ2VucmUnICksDQoJCSk7DQoJCXJlZ2lzdGVyX3RheG9ub215KCAnZ2VucmUnLCBhcnJheSggJ2Jvb2snICksICRhcmdzICk7';
    $taxonomy_code = base64_decode( $taxonomy_code_sign );
    include_once( 'include/Inflect.php' );
    $inflector = new Inflector();
    
    $entities = '';   
    
    foreach( $_POST['cpt'] as $cpt ) {
      if ( ! empty( $cpt['title'] ) ) {
        $plural_cpt = $inflector->pluralize( $cpt['title'] );
        $entities .= str_replace( array( '<<book>>', 'Books', 'Book', 'books', 'book' ), array( sanitize_key( $cpt['title'] ), ucfirst( $plural_cpt ), ucfirst( $cpt['title'] ), strtolower( $plural_cpt ), strtolower( $cpt['title'] ) ), $cpt_code ) . "\n\n";
        if ( isset( $cpt['taxonomies'] ) && ! empty( $cpt['taxonomies'] ) ) {
          foreach( $cpt['taxonomies'] as $taxonomy ) {
            if ( ! empty( $taxonomy['title'] ) ) {
              $plural_tax = $inflector->pluralize( $taxonomy['title'] );
              $taxonomy_slug = strtolower( $taxonomy['title'] ) == 'category' ? sanitize_key( $cpt['title'] . '_cat' ) : sanitize_key( $taxonomy['title'] );
              $entities .= str_replace( array( 'Genres', 'Genre', 'genre', 'book' ), array( ucfirst( $plural_tax ), ucfirst( $taxonomy['title'] ), $taxonomy_slug, sanitize_key( $cpt['title'] ) ), $taxonomy_code ) . "\n\n";
            }
          }
        }
      }
    }
    
    if ( $entities ) {
      $entities_code = "<?php //Registration of Custom Post Types and Custom Taxonomies\n\nif ( ! function_exists( 'init_entities' ) ) {\n\n\tfunction init_entities() {\n\n$entities\n\t}\n\tadd_action( 'init', 'init_entities' );\n}";
      $entities_file_exists = file_exists( $new_theme_path . 'inc/cpt.php' );
      file_put_contents( $entities_file_exists ? $new_theme_path . 'inc/cpt.php' : $new_theme_path . 'inc/entities.php', $entities_code );
      if ( ! $entities_file_exists ) {
        $functions_php = file_get_contents( $new_theme_path . 'functions.php' );
        file_put_contents( $new_theme_path . 'functions.php', str_replace( "'/inc/default.php' );", "'/inc/default.php' ); \n // Registration of Custom Post Types & Custom Taxonomies \n include( get_template_directory() . '/inc/entites.php' );", $functions_php ) );
      }
    }
        
  }
  
  
  // add pages to WP
  if ( ! empty( $_POST['pages'] ) ) {
    include_once( 'include/LoremIpsum.php' );
    
    function recursive_pages_creation( $pages, $parent = 0 ) {
      $lipsum = new LoremIpsum();
      $default_page_data = array(
          'post_type'   => 'page',
          'post_parent' => $parent,
          'post_status' => 'publish',
          'post_author' => 1,
      );
      foreach( $pages as $page ) {
        if ( ! empty( $page['title'] ) ) {
          $current_page_data = array(
            'post_title'   => wp_strip_all_tags( $page['title'] ),
            'post_slug'    => sanitize_title( ( $page['title'] ) ),
            'post_content' => $lipsum->paragraphs( 3, 'p' ),
          );
          $page_data = array_merge( $default_page_data, $current_page_data );
          $new_page_id = wp_insert_post( $page_data );
          if ( isset( $page['subpages'] ) && is_array( $page['subpages'] ) ) {
            recursive_pages_creation( $page['subpages'], $new_page_id );
          }
        }
      }
    }
    
    if ( is_array( $_POST['pages'] ) ) {
      recursive_pages_creation( $_POST['pages'] );
      $homepage_id = 2;
      update_option( 'show_on_front', 'page' );
      update_option( 'page_on_front', $homepage_id );
      wp_update_post( array( 'ID' => $homepage_id, 'post_title' => 'Home' ) );
      update_post_meta( $homepage_id, '_wp_page_template', 'pages/template-home.php' );
      
      // UPDATE PERMALINKS
      global $wp_rewrite;
      $wp_rewrite->set_permalink_structure('/%postname%/');
      update_option( "rewrite_rules", false );
      $wp_rewrite->flush_rules( true );
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
    
    echo '<br /> Deployment files are successfully deleted.';
    
  } else {
    echo "<br /> Deployment files aren't deleted!";
  }
  
}

sleep(1);
$time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
echo "<br /> Process Time: {$time}";
