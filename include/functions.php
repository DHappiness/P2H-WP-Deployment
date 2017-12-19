<?php /**
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
 

// upload files and folders from SVN
function recursive_svn_uploading( $url, $upload_path ) {
			global $deployment_settings;
			
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

// replace string in file
function replace_string_in_file( $file_path, $search, $replace ) {
		if ( file_exists( $file_path ) ) {
			$file_content = file_get_contents( $file_path );
			file_put_contents( $file_path, str_replace( $search, $replace, $file_content ) );
		}
}

// replace string in all presented files
function replace_string_in_selected_files( $files = array(), $search, $replace ) {
	if ( ! empty( $files ) ) {
		foreach ( $files as $file ) {
				replace_string_in_file( $file, $search, $replace );
		}
	}
}
 
 
// generate random user password
function randomPassword() {
	 $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	 $pass = array(); //remember to declare $pass as an array
	 $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	 for ($i = 0; $i < 12; $i++) {
		 $n = rand(0, $alphaLength);
		 $pass[] = $alphabet[$n];
	 }
	 return implode($pass); //turn the array into a string
 }

 
// creating of Wordpress Pages
function recursive_pages_creation( $pages, $parent = 0, $acf_identifier_path = '' ) {
	 $lipsum = new LoremIpsum();
	 $default_page_data = array(
		 'post_type'   => 'page',
		 'post_parent' => $parent,
		 'post_status' => 'publish',
		 'post_author' => 1,
	 );
		
	 foreach( $pages as $key => $page ) {
				if ( ! empty( $page['title'] ) ) {
						$current_page_data = array(
								'post_title'   => wp_strip_all_tags( $page['title'] ),
								'post_slug'    => sanitize_title( ( $page['title'] ) ),
								'post_content' => $lipsum->paragraphs( 3, 'p' ),
						);
						$page_data = array_merge( $default_page_data, $current_page_data );
						$new_page_id = wp_insert_post( $page_data );
						$entity_type = 'page|' . $new_page_id;
						
						if ( $page['template'] && isset( $_POST['dbname'] ) ) {
								$template_code_sign = 'PD9waHANCi8qDQpUZW1wbGF0ZSBOYW1lOiA8PFRFTVBMQVRFTkFNRT4+DQoqLw0KZ2V0X2hlYWRlcigpOyA/Pg0KPGRpdiBpZD0iY29udGVudCI+DQoJPD9waHAgd2hpbGUgKCBoYXZlX3Bvc3RzKCApICkgOiB0aGVfcG9zdCgpOyA/Pg0KCQk8ZGl2IGNsYXNzPSJwb3N0IiBpZD0icG9zdC08P3BocCB0aGVfSUQoKTsgPz4iPg0KCQkJPD9waHAgdGhlX3RpdGxlKCAnPGRpdiBjbGFzcz0idGl0bGUiPjxoMT4nLCAnPC9oMT48L2Rpdj4nICk7ID8+DQoJCQk8ZGl2IGNsYXNzPSJjb250ZW50Ij4NCgkJCQk8P3BocCB0aGVfY29udGVudCgpOyA/PgkJCQ0KCQkJCTw/cGhwIGVkaXRfcG9zdF9saW5rKCBfXyggJ0VkaXQnLCBUSEVNRURPTUFJTiApICk7ID8+DQoJCQkJDQoJCQk8L2Rpdj4NCgkJPC9kaXY+DQoJPD9waHAgZW5kd2hpbGU7ID8+DQo8L2Rpdj4NCjw/cGhwIGdldF9zaWRlYmFyKCk7ID8+DQo8P3BocCBnZXRfZm9vdGVyKCk7ID8+';
								$title_words = explode( ' ', $page['title'] );
								$template_content = str_replace( '<<TEMPLATENAME>>', $title_words[0], base64_decode($template_code_sign) );
								$theme_path = dirname( dirname(__FILE__) ) . '/wp-content/themes/' . $_POST['dbname'] . '/';
								$template_name = 'pages/template-' . sanitize_title( $title_words[0] ) . '.php';
								if ( file_exists( $theme_path . $template_name ) ) {
										$template_name = 'pages/template';
										for( $i = 0; $i < count( $title_words ); $i++ ) {
												if ( empty( $title_words[$i] ) ) {continue;}
											 $template_name .= '-' . sanitize_title( $title_words[$i] );
												if ( ! file_exists( $theme_path . $template_name ) ) {break;}
										}
										$template_name .= '.php';
								}
								file_put_contents( $theme_path . $template_name, $template_content );
								update_post_meta( $new_page_id, '_wp_page_template', $template_name );
								$entity_type = 'page_template|' . $template_name;
						}
						
						$acf_file_name = ( ( $acf_identifier_path ) ? $acf_identifier_path : 'pages_' ) . $key;
						if ( isset( $_POST['plugins'] ) && in_array( 'acf', $_POST['plugins'] ) ) {
							$fields = generate_acf_fields_code( $acf_file_name );
							if ( ! empty( $fields ) ) {
								create_acf_fields_group( $page['title'], $entity_type, $fields );
							}
						}
						
						if ( isset( $page['subpages'] ) && is_array( $page['subpages'] ) ) {
								recursive_pages_creation( $page['subpages'], $new_page_id, $acf_file_name . '_subpages_' );
						}
				}
	 }
		
}


if ( ! function_exists( 'generate_acf_fields_code' ) ) {
	function generate_acf_fields_code( $acf_file_name ) {
		$acf_path = __DIR__ . '/acf/';
		$acf_file = $acf_path . $acf_file_name . '.json';
		if ( file_exists( $acf_file ) ) {
			if ( $fields_data = json_decode(file_get_contents($acf_file)) ) {
				foreach( $fields_data as $field_data ) {
					$fields[] = array (
						'key' => 'field_' . uniqid(),
						'label' => $field_data->title,
						'name' => '_' . str_replace( '-', '_', sanitize_title( $field_data->title ) ),
						'type' => $field_data->type,
					);
				}
				return $fields;
			}
		}
		return array();
	}
}


if ( ! function_exists( 'run_activate_plugin' ) ) {
	function run_activate_plugin( $plugin ) {
					$current = get_option( 'active_plugins' );
					$plugin = plugin_basename( trim( $plugin ) );
					if ( !in_array( $plugin, $current ) ) {
									$current[] = $plugin;
									sort( $current );
									do_action( 'activate_plugin', trim( $plugin ) );
									update_option( 'active_plugins', $current );
									do_action( 'activate_' . trim( $plugin ) );
									do_action( 'activated_plugin', trim( $plugin) );
									return true;
					}
					return false;
	}
}


if ( ! function_exists( 'create_acf_fields_group' ) ) {
	function create_acf_fields_group( $entity_name, $entity_type, $fields ) {
		global $deployment_result;
		include_once( dirname( __DIR__ ) . '/wp-content/plugins/advanced-custom-fields-pro/acf.php' );
		if( function_exists('acf_add_local_field_group') ) {
			$entity = explode( '|', $entity_type );
			switch( $entity[0] ) {
				case 'page' :
					$title = '"' . $entity_name . '" Page fields';
					break;
				case 'page_template' :
					$title = '"' . $entity_name . '" Template fields';
					break;
				default :
					$title = '"' . $entity_name . '" fields';
			}
			if ( $test = acf_write_json_field_group(array(
				'key' => 'group_' . uniqid(),
				'title' => $title,
				'fields' => $fields,
				'style' => 'seamless',
				'location' => array (
					array (
						array (
							'param' => $entity[0],
							'operator' => '==',
							'value' => $entity[1],
						),
					),
				),
			))) {
				$type = ucfirst( str_replace( '_', ' ', $entity[0] ) );
				$deployment_result .= '<br/>ACF Fields Group for ' . $type . ' "' . $entity_name . '" has been created.';
			} else {
				$deployment_result .= '<br/>Fields creation faild.';
			}
		} else {
			$deployment_result .= '<br/>Can\'t create ACF fields. ACF plugin is not active.';
		}
	}
}


// delete files from specified folder
function recursive_files_remover( $folder, $delete_only_inside = false, $exceptions = array() ) {
			$it = new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS);
			$files = new RecursiveIteratorIterator($it,
																RecursiveIteratorIterator::CHILD_FIRST);
			foreach($files as $file) {
							if ( ! empty( $exceptions ) && basename( dirname( $file->getRealPath() ) ) == basename( $folder ) && in_array( basename( $file->getRealPath() ), $exceptions ) ) {
								continue;
							}
							if ($file->isDir()){
											rmdir($file->getRealPath());
							} else {
											unlink($file->getRealPath());
							}
			}
			if ( ! $delete_only_inside ) {
				rmdir($folder);
			}
}
