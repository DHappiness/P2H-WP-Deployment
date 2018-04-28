<?php if ( isset( $_GET['acf-group'] ) || isset( $_POST['fields'] ) ) :
	$acf_path = __DIR__ . '/acf';
	$entity = isset( $_GET['acf-group'] ) ? explode( '[', $_GET['acf-group'] ) : explode( '[', $_POST['entity'] );
	$type = $entity[0];
	$id = '';
	for ( $i = 1; $i <= ( count($entity) - 1 ); $i++ ) {
		$item = str_replace( ']', '', $entity[$i] );
		$id .= '_' . $item;
	}
	$acf_file_path = $acf_path . '/' . $type . $id . '.json';
endif; ?>
<?php if ( isset( $_GET['acf-group'] ) ) :
	$current_data = array( 'empty' );
	if ( file_exists( $acf_file_path ) ) :
		$current_data = json_decode( file_get_contents( $acf_file_path ) );
	endif;
	$acf_types = array(
		'text' => 'Text',
		'textarea' => 'Textarea',
		'number' => 'Number',
		'email' => 'Email',
		'url' => 'URL',
		'password' => 'Password',
		'wysiwyg' => 'Wysiwyg Editor',
		'oembed' => 'oEmbed',
		'image' => 'Image',
		'file' => 'File',
		'gallery' => 'Gallery',
		'link' => 'Link',
		'post_object' => 'Post Object',
		'page_link' => 'Page Link',
		'relationship' => 'Relationship',
		'taxonomy' => 'Taxonomy',
		'user' => 'User',
		'select' => 'Select',
		'checkbox' => 'Checkbox',
		'radio' => 'Radio Button',
		'true_false' => 'True / False',
		'google_map' => 'Google Map',
		'date_picker' => 'Date Picker',
		'date_time_picker' => 'Date Time Picker',
		'time_picker' => 'Time Picker',
		'color_picker' => 'Color Picker',
		'tab' => 'TAB',
		'group' => 'Group',
		'repeater' => 'Repeater',
		'flexible_content' => 'Flexible Content',
	); ?>
	<form action="#" method="post" class="ajax-form-validation">
		<div class="clone-elements" data-depth="1">
			<a href="#" class="add-item">Add Field</a>
			<br /><br />
			<?php $items_num = count( $current_data ) === 0 ? 1 : count( $current_data );
			for( $item = 0; $item < $items_num; $item++ ) : ?>
				<div class="item">
					<div class="field-holder">
						<input type="text" name="fields[<?php echo $item; ?>][title]" placeholder="Field Name" <?php echo isset( $current_data[$item]->title ) ? 'value="' . $current_data[$item]->title . '"' : ''; ?>>
						<input type="text" name="fields[<?php echo $item; ?>][slug]" placeholder="Field Slug" <?php echo isset( $current_data[$item]->title ) ? 'value="' . $current_data[$item]->title . '"' : ''; ?>>
						<?php $selected_type = isset( $current_data[$item]->type ) ? $current_data[$item]->type : ''; ?>
						<select name="fields[<?php echo $item; ?>][type]" title="Field Type">
							<?php foreach( $acf_types as $type_slug => $type_label ) : ?>
								<option value="<?php echo $type_slug; ?>"<?php if ( $type_slug == $selected_type ) {echo 'selected';} ?>><?php echo $type_label; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<a class="icon icon-minus remove-item" href="#" title="Remove Field"> </a>
				</div>
			<?php endfor; ?>
		</div>
		<input type="hidden" name="entity" value="<?php echo $_GET['acf-group']; ?>">
		<input type="submit" value="Save">
		<span class="loader"></span>
	</form>
	<?php exit;
elseif ( isset( $_POST['fields'] ) && ! empty( $_POST['fields'] ) ) :
	if ( ! file_exists( $acf_path ) ) {
		mkdir( $acf_path );
	}
	$fields = array();
	foreach( $_POST['fields'] as $field ) {
		if ( ! empty( $field['title'] ) ) {
			$fields[] = $field;
		}
	}
	if ( ! empty( $fields ) ) {
		$fields = json_encode( $fields );
		file_put_contents( $acf_file_path, $fields );
	}
	exit;
elseif ( isset( $_GET['dbcheck'] ) ) :
	$dbuser = ! empty( $_GET['dbuser'] ) ? $_GET['dbuser'] : $_GET['dbname'];
	if ( mysqli_connect( $_GET['host'], $dbuser, $_GET['password'] ) ) {
		echo 1;
	} else {
		echo 0;
	}
	exit;
endif;