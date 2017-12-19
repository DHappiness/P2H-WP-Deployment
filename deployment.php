<?php include_once( 'include/ajax-requests.php' );

global $deployment_result;
$deployment_result = '';
// include configuration file
include( 'include/config.inc' );

if ( ! isset( $_POST['deploy'] ) ) {
  // check for the latest version
  include_once( 'include/updater.php' );
} else {
  
  // helper functions
  include_once( 'include/functions.php' );
  
  // extended php ZipArchive Class
  include_once( 'include/BetterZipArchive.php' );
  
  // upload and unpack latest wordpress version
  $wordpress_url = 'https://wordpress.org/latest.zip';
  $zip_file = basename( $wordpress_url );
  $current_path = dirname(__FILE__);
  
  if ( $wordpress_zip = @fopen($wordpress_url, 'r') ) {  
    file_put_contents($zip_file, $wordpress_zip );
  } else {
    $deployment_result .= 'ERROR: Can\'t get a wordpress archive.';
    include_once( 'include/settings-form.php' );
    exit();
  }
  
  $zip = new BetterZipArchive;
  $res = $zip->open($zip_file);
  if ($res === TRUE) {
    $zip->extractSubdirTo( $current_path . '/', 'wordpress/' );
    $zip->close();
    unlink($zip_file);
  } else {
    $deployment_result .= 'ERROR: Can\'t unzipp WordPress archive!';
    include_once( 'include/settings-form.php' );
    exit();
  }
  
  // delete default themes
  if ( isset( $_POST['delete_themes'] ) ) {
    recursive_files_remover( $current_path . '/wp-content/themes', true, array( 'index.php' ) );
  }
  
  
  
  // import ACF PRO plugin and base wordpress theme from SVN ( or from include folder )
  $acf_path = $current_path . '/wp-content/plugins/advanced-custom-fields-pro/';
  $acf_svn_url = 'http://svn.w3.ua/Implementation/Development/Wordpress/Plugins/acf-addons/advanced-custom-fields-pro/';
  
  $base_theme_path = $current_path . '/wp-content/themes/base/';
  $base_theme_svn_url = 'http://svn.w3.ua/Implementation/Development/Wordpress/BaseTheme/base/';
  
  $custom_base_theme_location = 'include/base';
  
  if ( file_exists( $custom_base_theme_location ) ) {
    $deployment_result .= 'Was used custom base theme.<br />';
    rcopy( $custom_base_theme_location, $base_theme_path );
  } else {
    $deployment_result .= 'Was used base theme from SVN.<br />';
    recursive_svn_uploading( $base_theme_svn_url, $base_theme_path );
  }
  
  // raname theme and theme files
  rename( $base_theme_path . 'languages/base.pot', str_replace( 'base.pot', $_POST['dbname'] . '.pot', $base_theme_path . 'languages/base.pot' ) );
  $new_theme_path = str_replace( '/base/', '/' . $_POST['dbname'] . '/', $base_theme_path );
  rename( $base_theme_path, $new_theme_path );
  
  // change language domain
  $theme_files = glob( $new_theme_path.'*.php' );
  $scaned_theme_folder = scandir($new_theme_path);
  replace_string_in_selected_files( $theme_files, "'base'", "'" . $_POST['dbname'] . "'" );
  foreach( $scaned_theme_folder as $file_or_dir ) {
    if ( is_dir( $new_theme_path . $file_or_dir ) ) {
      $files_in_folder = glob( $new_theme_path . $file_or_dir . '/*.php' );
      replace_string_in_selected_files( $files_in_folder, "'base'", "'" . $_POST['dbname'] . "'" );
    }
  }
  
  // uploading acf and further needed manipulation
  if ( isset( $_POST['plugins'] ) && in_array( 'acf', $_POST['plugins'] ) ) {
    recursive_svn_uploading( $acf_svn_url, $acf_path );
    $base_theme_functions_default = file_get_contents( $new_theme_path . 'inc/default.php' );
    $base_theme_functions_default = str_replace( '//acf theme', '/*acf theme', $base_theme_functions_default );
    file_put_contents( $new_theme_path . 'inc/default.php', $base_theme_functions_default );
  }
  
  // creating style.css in the new theme and put basical comments in it
  if ( ! file_exists( $new_theme_path . 'style.css' ) ) {
    $style_css_content_sign = 'LyoNClRoZW1lIE5hbWU6IDw8QmFzZT4+DQpBdXRob3I6IEFub255bW91cw0KQXV0aG9yIFVSSToNClZlcnNpb246IDENCkRlc2NyaXB0aW9uOiBCYXNlIHRoZW1lIGZvciBXb3JkcHJlc3MNCkxpY2Vuc2U6IEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIHYyIG9yIGxhdGVyDQpMaWNlbnNlIFVSSTogaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbA0KVGV4dCBEb21haW46IDw8YmFzZT4+DQpUYWdzOiBvbmUtY29sdW1uLCB0d28tY29sdW1ucw0KVGhlbWUgVVJJOg0KKi8=';
    $style_css_content = str_replace( array( '<<base>>', '<<Base>>' ), array( $_POST['dbname'], $_POST['sitename'] ), base64_decode($style_css_content_sign) );
    file_put_contents( $new_theme_path . 'style.css', $style_css_content );
  }
  
  
  
  // wordpress configuration
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
  
  
  
  //user credentials  
  $user_name = 'admin';
  $user_email = 'admin@email.com';
  $user_password = randomPassword();
  
  include_once( 'wp-includes/class-phpass.php' );
  $wp_hasher = new PasswordHash(8, true); 
  $hashed_pass = $wp_hasher->HashPassword( trim( $user_password ) );
  
  $deployment_result .= "<strong>WP User:</strong> $user_name<br /><strong>User Password:</strong> $user_password<br /><strong>User Email:</strong> $user_email<br />";

  // import default db dump
  if ( $db_dump = file_get_contents( $current_path . '/include/default-db.sql' ) ) {
    $site_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    $site_description = ( isset( $_POST['site_description'] ) && ! empty( $_POST['site_description'] ) ) ? $_POST['site_description'] : 'Just another WordPress site';
    $db_dump = str_replace( array( "'SITENAME'", "'Just another WordPress site'", 'http://siteurl', '<<userpass>>', '<<template>>', '<<stylesheet>>' ), array( "'" . $_POST['sitename'] . "'", "'" . $site_description . "'", $site_url, $hashed_pass, $_POST['dbname'], $_POST['dbname'] ), $db_dump );
    
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    if ( $connection = mysqli_connect( ( ( $_POST['host'] ) ? $_POST['host'] : 'localhost' ), ! empty( $_POST['dbuser'] ) ? $_POST['dbuser'] : $_POST['dbname'], $_POST['password'], $_POST['dbname'] ) ) {
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
      $deployment_result .= "Database imported successfully.<br/>";
    }
  }
  
  
  $useremail = $user_email;
  // load WP API
  define( 'WP_USE_THEMES', false );
  define( 'COOKIE_DOMAIN', false );
  define( 'DISABLE_WP_CRON', true );
  if( ! session_id() ) {session_start();}
  include_once( 'wp-load.php' );
  
  
  // install selected plugins
  if ( ! empty( $_POST['plugins'] ) ) {
    require_once( $current_path . "/wp-admin/includes/plugin.php" );
    if ( ! file_exists( 'plugins' ) ) {
      mkdir( 'plugins' );
    }
    foreach( $_POST['plugins'] as $plugin ) {
      if ( $plugin == 'acf' ) {
        if ( run_activate_plugin( 'advanced-custom-fields-pro/acf.php' ) ) {
          if ( ! file_exists( $new_theme_path . 'acf-json' ) ) {
            mkdir( $new_theme_path . 'acf-json' );
          }
          $deployment_result .= '<br/>Plugin <strong>"Advanced Custom Fields PRO"</strong> was successfully installed and activated.';
        }
        continue;
      }
      
      $plugin_url = $plugins[$plugin]['url'];
      $zip_file = basename( $plugin_url );
      $path_to_plugins = '/wp-content/plugins/';
      
      if ( ! @file_put_contents( 'plugins/' . $zip_file, fopen( $plugin_url, 'r' ) ) ) {
        $deployment_result .= '<br/>Uploadin of Plugin <strong>"' . $plugins[$plugin]['label'] . '"</strong> faild.';
      }
      
      $zip = new BetterZipArchive;
      $res = $zip->open( 'plugins/' . $zip_file );
      if ($res === TRUE) {
        $zip->extractTo( $current_path . $path_to_plugins );
        $zip->close();
      }
      
      wp_cache_init();
      
      if ( run_activate_plugin( $plugin ) ) {
        $deployment_result .= '<br/>Plugin <strong>"' . $plugins[$plugin]['label'] . '"</strong> was successfully installed and activated.';
      }
      
    }
    
    wp_cache_flush();
    
    // delete uploaded plugins
    recursive_files_remover( 'plugins' );
    
    $deployment_result .= '<br />';
    
  }
  
  
  
  // create file with cpt and taxonomy registration
  if ( ! empty( $_POST['cpt'] ) ) {
    $cpt_code_sign = 'CQkkbGFiZWxzID0gYXJyYXkoDQoJCSAgICAnbmFtZScgICAgICAgICAgICAgICA9PiBfeCggJ0Jvb2tzJywgJ3Bvc3QgdHlwZSBnZW5lcmFsIG5hbWUnLCAnYmFzZScgKSwNCgkJICAgICdzaW5ndWxhcl9uYW1lJyAgICAgID0+IF94KCAnQm9vaycsICdwb3N0IHR5cGUgc2luZ3VsYXIgbmFtZScsICdiYXNlJyApLA0KCQkgICAgJ21lbnVfbmFtZScgICAgICAgICAgPT4gX3goICdCb29rcycsICdhZG1pbiBtZW51JywgJ2Jhc2UnICksDQoJCSAgICAnbmFtZV9hZG1pbl9iYXInICAgICA9PiBfeCggJ0Jvb2snLCAnYWRkIG5ldyBvbiBhZG1pbiBiYXInLCAnYmFzZScgKSwNCgkJICAgICdhZGRfbmV3JyAgICAgICAgICAgID0+IF94KCAnQWRkIE5ldycsICdib29rJywgJ2Jhc2UnICksDQoJCSAgICAnYWRkX25ld19pdGVtJyAgICAgICA9PiBfXyggJ0FkZCBOZXcgQm9vaycsICdiYXNlJyApLA0KCQkgICAgJ25ld19pdGVtJyAgICAgICAgICAgPT4gX18oICdOZXcgQm9vaycsICdiYXNlJyApLA0KCQkgICAgJ2VkaXRfaXRlbScgICAgICAgICAgPT4gX18oICdFZGl0IEJvb2snLCAnYmFzZScgKSwNCgkJICAgICd2aWV3X2l0ZW0nICAgICAgICAgID0+IF9fKCAnVmlldyBCb29rJywgJ2Jhc2UnICksDQoJCSAgICAnYWxsX2l0ZW1zJyAgICAgICAgICA9PiBfXyggJ0FsbCBCb29rcycsICdiYXNlJyApLA0KCQkgICAgJ3NlYXJjaF9pdGVtcycgICAgICAgPT4gX18oICdTZWFyY2ggQm9va3MnLCAnYmFzZScgKSwNCgkJICAgICdwYXJlbnRfaXRlbV9jb2xvbicgID0+IF9fKCAnUGFyZW50IEJvb2tzOicsICdiYXNlJyApLA0KCQkgICAgJ25vdF9mb3VuZCcgICAgICAgICAgPT4gX18oICdObyBib29rcyBmb3VuZC4nLCAnYmFzZScgKSwNCgkJICAgICdub3RfZm91bmRfaW5fdHJhc2gnID0+IF9fKCAnTm8gYm9va3MgZm91bmQgaW4gVHJhc2guJywgJ2Jhc2UnICkNCgkJKTsNCgkJJGFyZ3MgPSBhcnJheSgNCgkJICAgICdsYWJlbHMnICAgICAgICAgICAgID0+ICRsYWJlbHMsDQoJCSAgICAncHVibGljJyAgICAgICAgICAgICA9PiB0cnVlLA0KCQkgICAgJ3B1YmxpY2x5X3F1ZXJ5YWJsZScgPT4gdHJ1ZSwNCgkJICAgICdzaG93X3VpJyAgICAgICAgICAgID0+IHRydWUsDQoJCSAgICAnc2hvd19pbl9tZW51JyAgICAgICA9PiB0cnVlLA0KCQkgICAgJ3F1ZXJ5X3ZhcicgICAgICAgICAgPT4gdHJ1ZSwNCgkJICAgICdyZXdyaXRlJyAgICAgICAgICAgID0+IGFycmF5KCAnc2x1ZycgPT4gJzw8Ym9vaz4+JyApLA0KCQkgICAgJ2NhcGFiaWxpdHlfdHlwZScgICAgPT4gJ3Bvc3QnLA0KCQkgICAgJ2hhc19hcmNoaXZlJyAgICAgICAgPT4gdHJ1ZSwNCgkJICAgICdoaWVyYXJjaGljYWwnICAgICAgID0+IGZhbHNlLA0KCQkgICAgJ21lbnVfcG9zaXRpb24nICAgICAgPT4gbnVsbCwNCgkJICAgICdzdXBwb3J0cycgICAgICAgICAgID0+IGFycmF5KCAndGl0bGUnLCAnZWRpdG9yJywgJ2F1dGhvcicsICd0aHVtYm5haWwnLCAnZXhjZXJwdCcsICdjb21tZW50cycgKQ0KCQkpOw0KCQlyZWdpc3Rlcl9wb3N0X3R5cGUoICc8PGJvb2s+PicsICRhcmdzICk7';
    $cpt_code = base64_decode( $cpt_code_sign );
    $taxonomy_code_sign = 'CQkkbGFiZWxzID0gYXJyYXkoDQoJCQknbmFtZScgICAgICAgICAgICAgID0+IF94KCAnR2VucmVzJywgJ3RheG9ub215IGdlbmVyYWwgbmFtZScgKSwNCgkJCSdzaW5ndWxhcl9uYW1lJyAgICAgPT4gX3goICdHZW5yZScsICd0YXhvbm9teSBzaW5ndWxhciBuYW1lJyApLA0KCQkJJ3NlYXJjaF9pdGVtcycgICAgICA9PiBfXyggJ1NlYXJjaCBHZW5yZXMnICksDQoJCQknYWxsX2l0ZW1zJyAgICAgICAgID0+IF9fKCAnQWxsIEdlbnJlcycgKSwNCgkJCSdwYXJlbnRfaXRlbScgICAgICAgPT4gX18oICdQYXJlbnQgR2VucmUnICksDQoJCQkncGFyZW50X2l0ZW1fY29sb24nID0+IF9fKCAnUGFyZW50IEdlbnJlOicgKSwNCgkJCSdlZGl0X2l0ZW0nICAgICAgICAgPT4gX18oICdFZGl0IEdlbnJlJyApLA0KCQkJJ3VwZGF0ZV9pdGVtJyAgICAgICA9PiBfXyggJ1VwZGF0ZSBHZW5yZScgKSwNCgkJCSdhZGRfbmV3X2l0ZW0nICAgICAgPT4gX18oICdBZGQgTmV3IEdlbnJlJyApLA0KCQkJJ25ld19pdGVtX25hbWUnICAgICA9PiBfXyggJ05ldyBHZW5yZSBOYW1lJyApLA0KCQkJJ21lbnVfbmFtZScgICAgICAgICA9PiBfXyggJ0dlbnJlcycgKSwNCgkJKTsNCgkJJGFyZ3MgPSBhcnJheSgNCgkJCSdoaWVyYXJjaGljYWwnICAgICAgPT4gdHJ1ZSwNCgkJCSdsYWJlbHMnICAgICAgICAgICAgPT4gJGxhYmVscywNCgkJCSdzaG93X3VpJyAgICAgICAgICAgPT4gdHJ1ZSwNCgkJCSdzaG93X2FkbWluX2NvbHVtbicgPT4gdHJ1ZSwNCgkJCSdxdWVyeV92YXInICAgICAgICAgPT4gdHJ1ZSwNCgkJCSdyZXdyaXRlJyAgICAgICAgICAgPT4gYXJyYXkoICdzbHVnJyA9PiAnZ2VucmUnICksDQoJCSk7DQoJCXJlZ2lzdGVyX3RheG9ub215KCAnZ2VucmUnLCBhcnJheSggJ2Jvb2snICksICRhcmdzICk7';
    $taxonomy_code = base64_decode( $taxonomy_code_sign );
    include_once( 'include/Inflect.php' );
    $inflector = new Inflector();
    
    $entities = '';
    
    foreach( $_POST['cpt'] as $cpt_key => $cpt ) {
      if ( ! empty( $cpt['title'] ) ) {
        $plural_cpt = $inflector->pluralize( $cpt['title'] );
        $entities .= str_replace( array( '<<book>>', 'Books', 'Book', 'books', 'book' ), array( sanitize_key( $cpt['title'] ), ucfirst( $plural_cpt ), ucfirst( $cpt['title'] ), strtolower( $plural_cpt ), strtolower( $cpt['title'] ) ), $cpt_code ) . "\n\n";
        if ( isset( $_POST['plugins'] ) && in_array( 'acf', $_POST['plugins'] ) ) {
          $fields = generate_acf_fields_code( 'cpt_' . $cpt_key );
          if ( ! empty( $fields ) ) {
            create_acf_fields_group( $cpt['title'], 'post_type|' . sanitize_key( $cpt['title'] ), $fields );
          }
        }
        if ( isset( $cpt['taxonomies'] ) && ! empty( $cpt['taxonomies'] ) ) {
          foreach( $cpt['taxonomies'] as $tax_key => $taxonomy ) {
            if ( ! empty( $taxonomy['title'] ) ) {
              $plural_tax = $inflector->pluralize( $taxonomy['title'] );
              $taxonomy_slug = strtolower( $taxonomy['title'] ) == 'category' ? sanitize_key( $cpt['title'] . '_cat' ) : sanitize_key( $taxonomy['title'] );
              $entities .= str_replace( array( 'Genres', 'Genre', 'genre', 'book' ), array( ucfirst( $plural_tax ), ucfirst( $taxonomy['title'] ), $taxonomy_slug, sanitize_key( $cpt['title'] ) ), $taxonomy_code ) . "\n\n";
              
              if ( isset( $_POST['plugins'] ) && in_array( 'acf', $_POST['plugins'] ) ) {
                $fields = generate_acf_fields_code( 'cpt_' . $cpt_key . '_taxonomies_' . $tax_key );
                if ( ! empty( $fields ) ) {
                  create_acf_fields_group( $taxonomy['title'], 'taxonomy|' . sanitize_key( $taxonomy['title'] ), $fields );
                }
              }
              
            }
          }
        }
      }
    }
    
    if ( $entities ) {
      $entities_code = "<?php // Registration of Custom Post Types and Custom Taxonomies\n\nif ( ! function_exists( 'init_entities' ) ) {\n\n\tfunction init_entities() {\n\n$entities\n\t}\n\tadd_action( 'init', 'init_entities' );\n}";
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
    
    if ( is_array( $_POST['pages'] ) ) {
      
      recursive_pages_creation( $_POST['pages'] );
      
      // setup homepage and set it as front page
      if ( isset( $_POST['homepage'] ) ) {
        update_option( 'show_on_front', 'page' );
        $homepage_id = 2;
        if ( $_POST['homepage'] == '0' ) {
          update_option( 'page_on_front', $homepage_id );
          wp_update_post( array( 'ID' => $homepage_id, 'post_title' => 'Home' ) );
          update_post_meta( $homepage_id, '_wp_page_template', 'pages/template-home.php' );
          if ( isset( $_POST['plugins'] ) && in_array( 'acf', $_POST['plugins'] ) ) {
            $fields = generate_acf_fields_code( 'home' );
            if ( ! empty( $fields ) ) {
              create_acf_fields_group( 'Homepage Template', 'page_template|pages/template-home.php', $fields );
            }
          }
        } elseif ( $_POST['homepage'] == '1' ) {
          $blog_page_data = array(
            'post_type'    => 'page',
            'post_title'   => 'Blog',
            'post_slug'    => 'blog',
            'post_status'  => 'publish',
            'post_author'  => 1,
          );
          $blog_page_id = wp_insert_post( $blog_page_data );
          update_option( 'page_for_posts', $blog_page_id );
          if ( isset( $_POST['plugins'] ) && in_array( 'acf', $_POST['plugins'] ) ) {
            $fields = generate_acf_fields_code( 'home' );
            if ( ! empty( $fields ) ) {
              create_acf_fields_group( 'Blog page', 'page_type|posts_page', $fields );
            }
          }
        } elseif ( $_POST['homepage'] == '2' ) {
          file_put_contents( $new_theme_path . 'front-page.php', base64_decode('PD9waHAgZ2V0X2hlYWRlcigpOyA/PgoJPGRpdiBpZD0iY29udGVudCI+CgkJPD9waHAgJGxhdGVzdF9ibG9nX3Bvc3RzID0gbmV3IFdQX1F1ZXJ5KCBhcnJheSggJ3Bvc3RzX3Blcl9wYWdlJyA9PiAzICkgKTsgPz4KCQk8P3BocCBpZiAoICRsYXRlc3RfYmxvZ19wb3N0cy0+aGF2ZV9wb3N0cygpICkgOiA/PgoJCQk8P3BocCB3aGlsZSAoICRsYXRlc3RfYmxvZ19wb3N0cy0+aGF2ZV9wb3N0cygpICkgOiAkbGF0ZXN0X2Jsb2dfcG9zdHMtPnRoZV9wb3N0KCk7ID8+CgkJCQk8P3BocCBnZXRfdGVtcGxhdGVfcGFydCggJ2Jsb2Nrcy9jb250ZW50JywgZ2V0X3Bvc3RfdHlwZSgpICk7ID8+CgkJCTw/cGhwIGVuZHdoaWxlOyA/PgoJCQk8P3BocCBnZXRfdGVtcGxhdGVfcGFydCggJ2Jsb2Nrcy9wYWdlcicgKTsgPz4KCQk8P3BocCBlbHNlIDogPz4KCQkJPD9waHAgZ2V0X3RlbXBsYXRlX3BhcnQoICdibG9ja3Mvbm90X2ZvdW5kJyApOyA/PgoJCTw/cGhwIGVuZGlmOyA/PgoJCTw/cGhwIHdwX3Jlc2V0X3Bvc3RkYXRhKCk7ID8+Cgk8L2Rpdj4KCTw/cGhwIGdldF9zaWRlYmFyKCk7ID8+Cjw/cGhwIGdldF9mb290ZXIoKTsgPz4=') );
          if ( isset( $_POST['plugins'] ) && in_array( 'acf', $_POST['plugins'] ) ) {
            update_option( 'page_on_front', $homepage_id );
            wp_update_post( array( 'ID' => $homepage_id, 'post_title' => 'Front Page' ) );
            $fields = generate_acf_fields_code( 'home' );
            if ( ! empty( $fields ) ) {
              create_acf_fields_group( 'Front Page', 'page_type|front_page', $fields );
            }
          }
        }
      }
      
      if ( isset( $_POST['plugins'] ) && in_array( 'acf', $_POST['plugins'] ) ) {
        $fields = generate_acf_fields_code( 'options' );
        if ( ! empty( $fields ) ) {
          create_acf_fields_group( 'Options Page', 'options_page|acf-options-theme-options', $fields );
        }
        file_put_contents( $new_theme_path . 'inc/default.php', str_replace( '/*acf theme', '//acf theme', $base_theme_functions_default ) );
      }
      
      $deployment_result .= '<br />All pages with appropriate templates has been created.<br />';
      
      // UPDATE PERMALINKS
      global $wp_rewrite;
      $wp_rewrite->set_permalink_structure('/%postname%/');
      update_option( "rewrite_rules", false );
      $wp_rewrite->flush_rules( true );
      $deployment_result .= 'Permalinks structure changed to <strong>/%postname%/</strong><br />';
    }
  }  
  
  
  // authentificate admin
  if( ! empty( $useremail ) ) {
    if ( $user = get_user_by( 'email', $useremail ) ) {
      $user_id = $user->ID;
      $user_login = $user->user_login;
      wp_set_current_user( $user_id, $user_login );
      wp_clear_auth_cookie();
      wp_set_auth_cookie( $user_id, true );
      do_action( 'wp_login', $user_login );
      $deployment_result .= '<br />You\'re now logged in as <strong>' . $user_name . '</strong>';
    }
  } else {
    $deployment_result .= '<br/>User Email is empty';
  }
  
  $deployment_result .= '<br /> <a href="'. get_admin_url() .'" target="_blank">Go to WordPress admin panel.</a>';
  
}

// main form
include_once( 'include/settings-form.php' );

// delete deployment files
if ( isset( $_POST['deploy'] ) && $deployment_settings['delete_after_execution'] ) {
  recursive_files_remover( 'include' );
  unlink('deployment.php');
}
