<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Wolf_Theme_Admin_Metabox' ) ) {
	/**
	 * Metabox class
	 *
	 * Create metabox easily from an array (includes/metabox.php)
	 * Usually used for page styling options
	 *
	 * @since 1.4.2
	 * @package WolfFramework
	 * @author WolfThemes
	 */
	class Wolf_Theme_Admin_Metabox {

		var $meta = array();

		/**
		 * Wolf_Theme_Admin_Metabox constructor
		 */
		public function __construct( $meta = array() ) {

			$this->meta = $meta + $this->meta;
			add_action( 'add_meta_boxes', array( $this, 'add_meta' ) );
			add_action( 'save_post', array( $this, 'save' ) );
		}

		/**
		 * Add meta box 
		 */
		public function add_meta() {

			foreach ( $this->meta as $k => $v) {
				if ( is_array( $v['page'] ) ) {
					foreach ( $v['page'] as $p) {
						add_meta_box(
						sanitize_title( $k ).'_wolf_meta_box',
						$v['title'],
						array( $this, 'render' ),
						$p,
						'normal',
						'high' );
					}
				} else {
					add_meta_box(
					sanitize_title( $k ).'_wolf_meta_box',
					$v['title'],
					array( $this, 'render' ),
					$v['page'],
					'normal',
					'high' );
				}	
			}
		}

		/**
		 * Display Inputs
		 */
		public function render() {

			global $post;
			$post_id = $post->ID;

			$meta_fields = array();
			
			$current_post_type = get_post_type( $post_id );

			foreach ( $this->meta as $k => $v ) {
				if ( is_array( $v['page'] ) ) {
					if (  in_array( $current_post_type, $v['page'] ) ) {
						$meta_fields = $v['metafields'];
					}
				} else {
					if ( $v['page'] == $current_post_type ) {
						$meta_fields = $v['metafields'];
					}
				}
			}

			// Use nonce for verification
			echo '<input type="hidden" name="custom_meta_box_nonce" value="' . wp_create_nonce( basename( __FILE__ ) ) . '" />';
			
			// Begin the field table and loop
			echo '<table class="form-table wolf-metabox-table">';

			foreach ( $this->meta as $k => $v ) {

				if ( isset( $v['help'] ) ) {
					echo '<div class="wolf-metabox-help">'.$v['help'].'</div>';
				}
			}

			foreach ( $meta_fields as $field ) {

				$field_id = sanitize_title( $field['id'] );
				$type     = ( isset( $field['type'] ) ) ? $field['type'] : 'text';
				$label    = ( isset( $field['label'] ) ) ? $field['label'] : 'Label';
				$desc     = ( isset( $field['desc'] ) ) ? $field['desc'] : '';
				$def      = ( isset( $field['def'] ) ) ? $field['def'] : '';

				// get value of this field if it exists for this post
				$meta = get_post_meta( $post_id, $field_id, true );

				if ( ! $meta )
					$meta = $def;
				// begin a table row with
				echo '<tr>
				
				<th style="width:15%"><label for="' . $field_id . '">' . $label . '</label></th>
				
				<td>';

					// editor
					if ( 'editor' == $type ) {
						wp_editor( $meta, $field_id, $settings = array() );				
					// text
					} elseif ( 'text' == $type || 'int' == $type ) {
					
						echo '<input type="text" name="' . $field_id . '" id="' . $field_id . '" value="' . $meta . '" size="30" />
								<br><span class="description">' . $desc . '</span>';
					
					// textarea
					} elseif ( 'textarea' == $type ) {
						echo '<textarea name="' . $field_id . '" id="' . $field_id . '" cols="60" rows="4">' . $meta . '</textarea>
								<br><span class="description">' . $desc . '</span>';
					
					// checkbox
					} elseif ( 'checkbox' == $type ) {
						echo '<input type="checkbox" name="' . $field_id . '" id="' . $field_id . '" ', ( $meta ) ? ' checked="checked"' : '','/>
								<span class="description">' . $desc . '</span>';
					
					// select
					} elseif ( 'select' == $type ) {
						
						echo '<select name="' . $field_id . '" id="' . $field_id . '">';
						if ( array_keys( $field['options'] ) != array_keys( array_keys( $field['options'] ) ) ) {
							foreach ( $field['options'] as $k => $option) {
								echo '<option', $k == $meta ? ' selected="selected"' : '', ' value="'.$k.'">' . $option . '</option>';
							}
						} else{
							foreach ( $field['options'] as $option) {
								echo '<option', $option == $meta ? ' selected="selected"' : '', ' value="' . $option . '">' . $option . '</option>';
							}
						}
						
						echo '</select><br><span class="description">' . $desc . '</span>';
										
					// colorpicker
					} elseif ( 'colorpicker' == $type ) {
						
						echo '<input type="text" class="wolf-options-colorpicker wolf-colorpicker-input" name="' . $field_id . '" id="' . $field_id . '" value="' . $meta . '" />
								<br><span class="description">' . $desc . '</span>';

					// file
					} elseif ( 'file' == $type ) { 
						$meta_img = get_post_meta( $post_id, $field_id, true );
					?>
					<div>
						<input type="text"  name="<?php echo $field_id; ?>" id="<?php echo $field_id; ?>" value="<?php echo esc_url( $meta_img); ?>">
						<br><a href="#" class="button wolf-options-reset-file"><?php _e( 'Clear', 'wolf' ); ?></a>
						<a href="#" class="button wolf-options-set-file"><?php _e( 'Choose a file', 'wolf' ); ?></a>
					</div>
					
					<div style="clear:both"></div>
					<?php
					/*  Background
					-------------------------------------------*/
					} elseif ( 'background' == $type ) {
						
						$parallax           = isset( $field['parallax'] ) ? $field['parallax'] : false;
						$bg_meta_color      = get_post_meta( $post_id, $field_id . '_color', true );
						$bg_meta_img        = get_post_meta( $post_id, $field_id . '_img', true );
						$bg_meta_repeat     = get_post_meta( $post_id, $field_id . '_repeat', true );
						$bg_meta_position   = get_post_meta( $post_id, $field_id . '_position', true );
						$bg_meta_attachment = get_post_meta( $post_id, $field_id . '_attachment', true );
						$bg_meta_size       = get_post_meta( $post_id, $field_id . '_size', true );
						$bg_meta_parallax   = get_post_meta( $post_id, $field_id . '_parallax', true );
						/* Bg Image */
						?>
						<p><strong><?php _e( 'Background color', 'wolf' ); ?></strong></p>
						<input name="<?php echo  $field_id . '_color'; ?>" name="<?php echo  $field_id . '_color'; ?>" class="wolf-options-colorpicker" type="text" value="<?php echo $bg_meta_color; ?>">
						<br><br>
						
						<p><strong><?php _e( 'Background image', 'wolf' ); ?></strong></p>
						<div>
							<input type="hidden" name="<?php echo $field_id; ?>_img" id="<?php echo $field_id; ?>_img" value="<?php echo esc_url( $bg_meta_img); ?>">
							<img style="max-width:250px;<?php if ( '' == $bg_meta_img ) echo ' display:none;'; ?>" class="wolf-options-img-preview" src="<?php echo esc_url( $bg_meta_img ); ?>" alt="<?php echo $field_id; ?>">
							<br><a href="#" class="button wolf-options-reset-bg"><?php _e( 'Clear', 'wolf' ); ?></a>
							<a href="#" class="button wolf-options-set-bg"><?php _e( 'Choose an image', 'wolf' ); ?></a>
						</div>
						<br><br>
						<?php
						/* Bg Repeat */
						$options = array(  'no-repeat', 'repeat','repeat-x', 'repeat-y' );

						?>
						<br>
						<p><strong><?php _e( 'Background repeat', 'wolf' ); ?></strong></p>
						<select name="<?php echo $field_id . '_repeat'; ?>" id="<?php echo $field_id . '_repeat'; ?>">
							<?php foreach ( $options as $o): ?>
								<option value="<?php echo $o; ?>" <?php if ( $o == $bg_meta_repeat ) echo 'selected="selected"'; ?>><?php echo $o; ?></option>
							<?php endforeach; ?>
						</select>
						<?php

						/* Bg position */
						$options = array( 
							'center center',
							'center top', 
							'left top' ,
							'right top' , 
							'center bottom', 
							'left bottom' , 
							'right bottom' ,
							'left center' ,
							'right center'
						);

						?>
						<br><br>
						<p><strong><?php _e( 'Background position', 'wolf' ); ?></strong></p>
						<select name="<?php echo $field_id . '_position'; ?>" id="<?php echo $field_id . '_position'; ?>">
							<?php foreach ( $options as $o): ?>
								<option value="<?php echo $o; ?>" <?php if ( $o == $bg_meta_position ) echo 'selected="selected"'; ?>><?php echo $o; ?></option>
							<?php endforeach; ?>
						</select>
						<?php

						/* Attachment
						--------------------*/
						$options = array( 'scroll', 'fixed' ); 

						?>
						<br><br>
						<p><strong><?php _e( 'Background attachment', 'wolf' ); ?></strong></p>
						<select name="<?php echo $field_id . '_attachment'; ?>" id="<?php echo $field_id . '_attachment'; ?>">
							<?php foreach ( $options as $o): ?>
								<option value="<?php echo $o; ?>" <?php if ( $o == $bg_meta_attachment ) echo 'selected="selected"'; ?>><?php echo $o; ?></option>
							<?php endforeach; ?>
						</select>
						<?php

						/* size
						--------------------*/
						$options = array( 
							'cover' => __( 'cover (resize)', 'wolf' ),
							'normal' => __( 'normal', 'wolf' ),
							'resize' => __( 'responsive (hard resize)', 'wolf' ),
						);

						?>
						<br><br>
						<p><strong><?php _e( 'Background size', 'wolf' ); ?></strong></p>
						<select name="<?php echo $field_id . '_size'; ?>" id="<?php echo $field_id . '_size'; ?>">
							<?php foreach ( $options as $k =>$v ) : ?>
								<option value="<?php echo $k; ?>" <?php if ( $k == $bg_meta_size ) echo 'selected="selected"'; ?>><?php echo $v; ?></option>
							<?php endforeach; ?>
						</select>
						<?php
						if ( $parallax ) {
							?>
							<br><br>
							<p><strong><?php _e( 'Parallax', 'wolf' ); ?></strong></p>
							<input <?php if ( $bg_meta_parallax ) echo 'checked="checked"'; ?> type="checkbox" name="<?php echo $field_id . '_parallax'; ?>" id="<?php echo $field_id . '_parallax'; ?>">
							<?php
						}
					
				} //end conditions
			echo '</td></tr>';
			} // end foreach
			echo '</table>'; // end table
		}

		/**
		 * Save the post meta data
		 */
		public function save( $post_id ) {
			global $post;

			$meta_fields = '';
			
			// verify nonce
			if ( ( isset( $_POST['wolf_meta_box_nonce'] ) ) && ( ! wp_verify_nonce( $_POST['wolf_meta_box_nonce'], basename( __FILE__ ) ) ) )
				return $post_id;
			
			// check autosave
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
				return $post_id;
			
			// check permissions
			if ( isset( $_POST['post_type'] ) && is_object( $post ) ) {
				
				$current_post_type = get_post_type( $post->ID );
				
				if ( 'page' == $_POST['post_type'] ) {
					if ( ! current_user_can( 'edit_page', $post_id ) ) {
						return $post_id;
						
					} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
						return $post_id;
					}
						
				}
			
				foreach ( $this->meta as $k => $v ) {
	 
					if ( is_array( $v['page'] ) )
						$condition = isset( $_POST['post_type'] ) && in_array( $_POST['post_type'], $v['page'] );
					else
						$condition = isset( $_POST['post_type'] ) && $_POST['post_type'] == $v['page'];

					if ( $condition ) {
						
						$meta_fields = $v['metafields'];
						
						// loop through fields and save the data
						foreach ( $meta_fields as $field ) {

							$field_id = $field['id'];

							if ( 'background' == $field['type'] ) {

								$meta = get_post_meta( $post_id, $field_id, true );
								
								$bg_settings = array( 'color', 'position', 'repeat', 'attachment', 'size', 'img', 'parallax' );

								foreach ( $bg_settings as $s ) {

									$o = $field_id . '_' . $s;
									
									if ( isset( $_POST[ $o ] ) ) {
										
										update_post_meta( $post_id, $o, $_POST[$o] );
									
									} else {

										delete_post_meta( $post_id, $o );
									}
								}
							} // end background

							else{
								$old = get_post_meta( $post_id, $field_id, true );
								$new = '';
								
								if ( isset( $_POST[ $field_id ] ) ) {

									if ( 'int' == $field['type'] ) {
										$new = absint( $_POST[ $field_id ] );
									} else {
										$new = $_POST[ $field_id ];
									}
								}
									
								
								if ( $new && $new != $old ) {

									update_post_meta( $post_id, $field_id, $new );
								
								} elseif ( '' == $new && $old ) {
									
									delete_post_meta( $post_id, $field_id, $old );
								}
							}
						} // end foreach
					}
				}					
			}
		}	
	} // end class

} // end class exists check