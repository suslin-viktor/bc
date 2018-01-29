<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Wolf_Theme_Admin_Options' ) ) {
	/**
	 * Theme options class
	 *
	 * Create theme options easily from an array (includes/options.php)
	 *
	 * @since 1.4.2
	 * @package WolfFramework
	 * @author WolfThemes
	 */
	class Wolf_Theme_Admin_Options {

		/**
		 * @var array
		 */
		public $options = array();

		/**
		 * Wolf_Theme_Admin_Options Constructor
		 *
		 * @todo set a main key option
		 */
		public function __construct( $options = array() ) {

			$this->options = $options + $this->options;
			$this->save();
			$this->render();
		}

		/**
		 * Get theme option from "wolf_theme_options_template" array
		 *
		 * @param string $o
		 * @param string $default
		 * @return string
		 */
		public function get_option( $o, $default = null ) {
			global $options;

			$wolf_framework_options = get_option( 'wolf_theme_options_' . wolf_get_theme_slug() );

			if ( isset( $wolf_framework_options[ $o ] ) ) {
				
				$option = $wolf_framework_options[ $o ];

				if ( function_exists( 'icl_t' ) ) {

					$option = icl_t( wolf_get_theme_slug(), $o, $option ); // WPML
				}

				return $option;

			} elseif ( $default ) {

				return $default;
			}
		}

		/**
		 * Save the theme options in array 
		 */
		public function save() {
			
			global $options;
			
			if ( ( isset( $_GET['page'] ) ) && ( $_GET['page'] == 'wolf-theme-options' ) ) {
				
				$errors = array();
				
				if ( isset( $_POST['action'] ) && $_POST['action'] == 'save' 
					&& wp_verify_nonce( $_POST['wolf_save_theme_options_nonce'],'wolf_save_theme_options' ) ) {

					$new_options = array();
					$data = $_POST['wolf_theme_options'];
					
					foreach ( $this->options as $value ) {

						$type = isset( $value['type'] ) ? $value['type'] : null;
						$value_key = isset( $value['id'] ) ? $value['id'] : null;
						
						if ( 'int' == $type ) {

							$new_options[ $value_key ] = intval( $data[ $value_key ] );
								
						}

						elseif ( 'url' == $type || 'image' == $type || 'file' == $type ) {

							if ( ! empty( $data[ $value_key ] ) )
								$new_options[ $value_key ] = esc_url( $data[ $value_key ] );
						}

						elseif ( 'email' == $type ) {

							if ( ! empty( $data[ $value_key ] ) && ! is_email( $data[ $value_key ] ) ) {

								$errors[] = '<strong>' . $data[ $value_key ] . '</strong> '.__( 'is not a valid email', 'wolf' ).'.';

							} elseif ( ! empty( $data[ $value_key ] ) ) {

								$new_options[ $value_key ] = sanitize_email( $data[ $value_key ] );

							}
						}

						elseif ( 'editor' == $type ) {

							if ( ! empty( $_POST[ 'wolf_theme_options_editor_' . $value['id'] ] ) ) {

								$new_options[ $value_key ] = $_POST[ 'wolf_theme_options_editor_' . $value_key ];

								if ( function_exists( 'icl_register_string' ) ) {
									icl_register_string( wolf_get_theme_slug(), $value_key, $new_options[ $value_key ] );
								}	
							}
						}

						elseif ( 'text' == $type || 'text_html' == $type || 'textarea' == $type || 'javascript' == $type ) {
							
							if ( ! empty( $data[ $value_key ] ) ) {

								$new_options[ $value_key ] = $data[ $value_key ];

								if ( 'text' == $type || 'text_html' == $type || 'textarea' == $type ) {
									if ( function_exists( 'icl_register_string' ) ) {
										icl_register_string( wolf_get_theme_slug(), $value_key, $new_options[ $value_key ] );
									}
								}
							}
						}

						elseif ( 'font-title' == $type ) {

							$new_options[ $value_key ] = $data[ $value_key ] ;

							$new_options[$value_key.'_transform'] = $data[$value_key.'_transform'] ;
						}
					
						elseif ( 'background' == $type ) {


							$bg_settings = array( 'color', 'img', 'position', 'repeat', 'attachment', 'size', 'parallax', 'font_color' );

							foreach ( $bg_settings as $s ) {

								$o = $value_key.'_'.$s;
								
								//debug( $data[ $value_key.'_'.$s ] );
								
								if ( isset( $o ) && ! empty( $data[ $o ] ) ) {

									$new_options[$o] = sanitize_text_field( $data[ $o ] );

								}
							}

						} else {
							if ( isset( $value_key ) && ! empty( $data[ $value_key ] ) ) {
								$new_options[ $value_key ] = sanitize_text_field( strip_tags( $data[ $value_key ] ) ) ;
							}
						}
					}

					update_option( 'wolf_theme_options_' . wolf_get_theme_slug(), $new_options );				

			 
				} else if ( ( isset( $_POST['action'] ) ) && ( $_POST['action'] == 'wolf-reset-all-options' ) ) {
				 	
				 	$old_options = get_option( 'wolf_theme_options' );
				 	
					delete_option( 'wolf_theme_options_' . wolf_get_theme_slug() );

					if ( function_exists( 'wolf_theme_default_options_init' ) )
						wolf_theme_default_options_init();
				}

				if ( ( isset( $_POST['action'] ) ) && ( $_POST['action'] == 'save' ) )
					wolf_admin_notice( __( 'Your settings have been saved.', 'wolf' ), 'updated' );
				
				if ( ( isset( $_POST['action'] ) ) && ( $_POST['action'] == 'wolf-reset-all-options' ) )
					wolf_admin_notice( __( 'Your settings have been reset.', 'wolf' ), 'updated' );
					
				/* Display raw error message */
				if ( $errors != array() ) {
					$error_message = '<br><div class="error">';
					foreach ( $errors as $error) {
						$error_message .= '<p>'.$error.'</p>';
					}
					$error_message .= '</div>';
					echo $error_message;
				}
			}

		} // end save function

		/**
		 * Render Theme Options inputs
		 */
		public function render() {

			$theme_version = 'v.' . WOLF_THEME_VERSION;

			/* If a child theme is used and update notces are enabled, we show the parent theme version */
			if ( is_child_theme() && WOLF_UPDATE_NOTICE )
				$theme_version = sprintf( __( 'v.%1$s (Parent Theme v.%2$s)', 'wolf' ), wp_get_theme()->Version, WOLF_THEME_VERSION ) ;

		$i = 0; $j = 0; $k = 0; $y = 0;
		?>
		<div id="wolf-framework-messages">
			<?php 
			// Check for theme update and set an admin notification if needed
			wolf_theme_update_notification_message();
			?>
		</div>
		
	<div class="wrap">
		
		<form id="wolf-theme-options-form" method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=wolf-theme-options' ) ); ?>">
		<?php wp_nonce_field( 'wolf_save_theme_options', 'wolf_save_theme_options_nonce' ); ?>

			<h2 class="nav-tab-wrapper">
				
				<div class="tabs " id="tabs1">
					<?php foreach ( $this->options as $value) { ?>
						<?php if ( $value['type'] == 'open' ) { ?>
						<?php $y++; ?>
						<a href="#panel<?php echo $y; ?>" class="nav-tab"><?php echo $value['name']; ?></a>
					<?php } }?>
				</div>
			</h2>

		<div class="content">

	<?php foreach ( $this->options as $value) {
		if ( ! isset( $value['def'] ) ) $value['def'] = '';
		if ( ! isset( $value['desc'] ) ) $value['desc'] = '';
		
		if ( $value['type'] == 'open' ) {
		$k++;
		?>
		<div id="panel<?php echo $k; ?>" class="wolf-options-panel">
			
			<p><?php echo $value['desc']; ?></p>	
			
		<?php 
		} elseif ( $value['type'] == 'close' ) {
		?>	
			<div class="wolf-options-actions">	
				<span class="submit">
					<img class="wolf-options-loader" style="display:none; vertical-align:middle; margin-right:5px" src="<?php echo esc_url( admin_url( 'images/loading.gif' ) ); ?>" alt="loader">
					<input name="wolf-theme-options-save" type="submit" class="wolf-theme-options-save button-primary menu-save" value="<?php _e( 'Save changes', 'wolf' ); ?>">
					<div style="float:none; clear:both"></div>
				</span>
				<div class="clear"></div>
			</div>

		</div><!-- panel -->

		<?php 

		} elseif ( $value['type'] == 'subtitle' ) {
		?>

			<div class="wolf_title wolf_subtitle">
				<h3>
				<?php echo $value['name']; ?>
				<br><small><?php echo $value['desc']; ?></small>
				</h3>
				<div class="clear"></div>
			</div>

		<?php 

		} elseif ( $value['type'] == 'section_open' ) {
		?>

		<div class="section-title">
			<?php if ( isset( $value['name'] ) ) : ?>
				<h3><?php echo $value['name']; ?></h3>
			<?php endif ?>
			
			<p class="description"><?php echo $value['desc']; ?></p>
		</div>
		
		<table class="form-table">
			<tbody>
		<?php

		} elseif ( $value['type'] == 'section_close' ) {
		?>
			</tbody>
		</table>
		<?php
		} else {

				$this->do_input( $value);

				}
		// foreach $options
		}
		?>
		 
		<input type="hidden" name="action" value="save">
		</form>

		</div> <!-- .content -->

		<?php
		$reset_options_confirm = __( 'Are you sure to want to reset all options ?', 'wolf' );
		?>
		<div id="wolf-options-footer">
			<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=wolf-theme-options' ) ); ?>">
				<p id="reset">
					<input name="wolf-reset-all-options" type="submit" value="<?php _e( 'Reset all options', 'wolf' ); ?>" onclick="if (window.confirm( '<?php echo $reset_options_confirm; ?>' ) ) 
					{location.href='default.htm';return true;} else {return false;}">
					<input type="hidden" name="action" value="wolf-reset-all-options">
				</p>
			</form>
			
			<p id="theme-version"><?php echo wp_get_theme()->Name; ?> <small><?php echo $theme_version; ?></small></p>
		</div>
	</div><!-- .wrap -->
		
		<?php
			if ( WOLF_DEBUG ) {
				echo "<br><br>options";
				debug( get_option( 'wolf_theme_options_' . wolf_get_theme_slug() ) );

				echo "<br><br>posted";
				debug( $_POST );

			}
			//end wolf_options_admin
		}

		/**
		 * Generate theme option inputs
		 * @return string
		 */
		public function do_input( $item ) {
			wp_enqueue_media();

			$prefix = 'wolf_theme_options';
			$type = 'text';
			$desc = '';
			$def = '';
			$pre = '';
			$app = '';
			$help = '';
			$parallax = false;
			$font_color = true;
			
			if ( isset( $item['type'] ) ) $type = $item['type'];
			if ( isset( $item['def'] ) ) $def = $item['def'];
			if ( isset( $item['desc'] ) ) $desc= $item['desc'];
			if ( isset( $item['pre'] ) ) $pre= $item['pre'];
			if ( isset( $item['app'] ) ) $app= $item['app'];
			if ( isset( $item['parallax'] ) && $item['parallax'] == true && $type == 'background'  ) $parallax = true;
			if ( isset( $item['font_color'] ) && $item['font_color'] == false && $type == 'background'  ) $font_color = false;
			
			if ( isset( $item['help'] ) ) {

				$help = '<span class="hastip" title="' . __( 'Click to view the screenshot helper', 'wolf' ) . '"><a class="wolf-help-img" href="' . esc_url( WOLF_THEME_URL . '/images/help/' . $item['help']  .'.jpg' )  . '"><img src="' . esc_url( WOLF_FRAMEWORK_URL . '/assets/img/help.png' ) . '" alt="help"></a></span>';
				
				if ( $type != 'checkbox' )
				$desc .= $help;
			} 


		if ( $type == 'text' || $type == 'text_html' || $type == 'int' || $type == 'email' || $type == 'url' ) : ?>

			<tr valign="top">
		    		<th scope="row" class="titledesc">
					<label for="<?php echo $prefix; ?>[<?php echo $item['id']; ?>]"><?php echo $item['name']; ?>
						<br>
						<small class="description"><?php echo $desc; ?></small>
					</label>
				</th>
				
				<td class="forminp">
					<div class="<?php if ( $pre != '' ) : echo "input-prepend"; elseif ( $app!='' ) : echo "input-append"; endif; ?>">
					<?php if ( $pre != '' ) : ?>
						<span class="add-on"><?php echo $pre; ?></span>
					<?php endif; ?>
						<input class="option-input" name="<?php echo $prefix; ?>[<?php echo $item['id']; ?>]" id="<?php echo $item['id']; ?>" type="text" value="<?php echo ( $this->get_option( $item['id'] ) ) ? htmlentities( stripslashes($this->get_option( $item['id'] ) ) ) : $def; ?>">
					<?php if ( $app != '' ) : ?>
						<span class="add-on"><?php echo $app; ?></span>
					<?php endif; ?>
					</div>
				</td>
			</tr>


		<?php 
		// to do
		elseif ( $type == 'css' ) : ?>

		<div id="custom_css_container">
			<div name="custom_css" id="<?php echo $prefix.'['.$item['id'].']' ?>" style="border: 1px solid #DFDFDF; -moz-border-radius: 3px; -webkit-border-radius: 3px; border-radius: 3px; width: 100%; height: 400px; position: relative;"></div>
		</div>
	 
	            <textarea id="custom_css_textarea" name="<?php echo $prefix.'['.$item['id'].']' ?>" style="display: none;"><?php echo ( $this->get_option( $item['id'] ) ) ? stripslashes( $this->get_option( $item['id'] ) ) : $def; ?></textarea>

		<?php elseif ( $type == 'editor' ) : ?>
		<?php 
			$content =  ( $this->get_option( $item['id'] ) ) ? stripslashes( $this->get_option( $item['id'] ) ) : $def;
			$editor_id = $prefix . '_editor_' . $item['id'];
		?>
		    	
			<tr valign="top">
		    		<th scope="row" class="titledesc">
					<label for="<?php echo $editor_id; ?>]"><?php echo $item['name']; ?>
					<br><small class="description"><?php echo $desc; ?></small></label>
				</th>
				
				<td class="forminp">
					<div class="wolf-editor-container">
						<?php wp_editor( $content, $editor_id, $settings = array() ); ?>
					</div>
				</td>
			</tr>
		 
	    	<?php elseif ( $type == 'textarea' || $type == 'javascript' ) : ?>

		    	<tr valign="top">
		    		<th scope="row" class="titledesc">
					<label for="<?php echo $prefix; ?>[<?php echo $item['id']; ?>]"><?php echo $item['name']; ?><br>
					<small class="description"><?php echo $desc; ?></small></label>
				</th>
				
				<td class="forminp">
					<div class="option-textarea">
						<textarea name="<?php echo $prefix; ?>[<?php echo $item['id']; ?>]"><?php echo ( $this->get_option( $item['id'] ) ) ? stripslashes( $this->get_option( $item['id'] ) ) : $def; ?></textarea>
					</div>
				</td>
			</tr>	

	    	<?php elseif ( $type == 'select' ) : ?>

			<tr valign="top">
		    		<th scope="row" class="titledesc">
					<label for="<?php echo $prefix; ?>[<?php echo $item['id']; ?>]"><?php echo $item['name']; ?><br>
						<small class="description"><?php echo $desc; ?></small>
					</label>
				</th>
				
				<td class="forminp">
					
					<select name="<?php echo $prefix; ?>[<?php echo $item['id']; ?>]" id="<?php echo $item['id']; ?>">
					<?php if (array_keys( $item['options'] ) != array_keys(array_keys( $item['options'] ) )) : ?>
						<?php foreach ( $item['options'] as $v => $o) { ?>
							<option value="<?php echo $v; ?>" <?php if ( stripslashes($this->get_option( $item['id'] ) ) == $v  ) { echo 'selected="selected"'; } ?>><?php echo $o; ?></option>
						<?php } ?>
					<?php else: ?>
						<?php foreach ( $item['options'] as $v) { ?>
							<option value="<?php echo $v; ?>" <?php if ( stripslashes($this->get_option( $item['id'] ) ) == $v  ) { echo 'selected="selected"'; } ?>><?php echo $v; ?></option>
						<?php } ?>
					<?php endif; ?>
					</select>

				</td>
			</tr>

		<?php elseif ( $type == 'checkbox' ) : ?>

			<?php
			$checked = "";
			if ( $this->get_option( $item['id'] ) == 'true' ) $checked = "checked=\"checked\""; 
			if ( $this->get_option( $item['id'] ) == 'false' ) $checked = ""; 
			?>

			<tr valign="top">
		    		<th scope="row" class="titledesc">
					<label for="<?php echo $prefix; ?>[<?php echo $item['id']; ?>]"><?php echo $item['name']; ?></label>
					<?php echo $help; ?>
				</th>
				
				<td class="forminp">
					<input type="checkbox" name="<?php echo $prefix; ?>[<?php echo $item['id']; ?>]" id="<?php echo $item['id']; ?>" value="true" <?php echo $checked; ?>>
					<small class="description"><?php echo $desc; ?></small>
				</td>
			</tr>

		<?php elseif ( $type == 'radio' ) : ?>

			<div class="wolf_input wolf_checkbox">
				<label for="<?php echo $prefix; ?>[<?php echo $item['id']; ?>]"><?php echo $item['name']; ?></label>
				<?php if ( $def==true) { $checked = "checked=\"checked\""; } else { $checked = "";} ?>
				<input type="radio" name="<?php echo $prefix; ?>[<?php echo $item['id']; ?>]" id="<?php echo $item['id']; ?>" value="true" <?php echo $checked; ?>>
				<small><?php echo $desc; ?></small>
			 </div>

		<?php elseif ( $type == 'image' ) : ?>

			<tr valign="top">
		    		<th scope="row" class="titledesc">
					<label for="<?php echo $prefix; ?>[<?php echo $item['id']; ?>]"><?php echo $item['name']; ?>
						<br>
						<small class="description"><?php echo $desc; ?></small>
					</label>
				</th>
				
				<td class="forminp">
					
					<input type="hidden" name="<?php echo $prefix; ?>[<?php echo $item['id']; ?>]" id="<?php echo $item['id']; ?>" value="<?php echo esc_url($this->get_option( $item['id'] ) ); ?>">
					<img <?php if ( !$this->get_option( $item['id'] ) ) echo 'style="display:none;"'; ?> class="wolf-options-img-preview" src="<?php echo esc_url($this->get_option( $item['id'] ) ); ?>" alt="<?php echo $item['id']; ?>">
					<br>
					<a href="#" class="button wolf-options-reset-img"><?php _e( 'Clear', 'wolf' ); ?></a>
					<a href="#" class="button wolf-options-set-img"><?php _e( 'Choose an image', 'wolf' ); ?></a>
			
				</td>
			</tr>
		
		<?php elseif ( $type == 'file' ) : ?>

			<tr valign="top">
		    		<th scope="row" class="titledesc">
					<label for="<?php echo $prefix; ?>[<?php echo $item['id']; ?>]"><?php echo $item['name']; ?><br>
					<small class="description"><?php echo $desc; ?></small></label>
				</th>
				
				<td class="forminp">
					<input type="text" class="option-input" name="<?php echo $prefix; ?>[<?php echo $item['id']; ?>]" id="<?php echo $item['id']; ?>" value="<?php echo esc_url( $this->get_option( $item['id'] ) ); ?>">
					<br><br>
					<a href="#" class="button wolf-options-reset-file"><?php _e( 'Clear', 'wolf' ); ?></a>
					<a href="#" class="button wolf-options-set-file"><?php _e( 'Choose a file', 'wolf' ); ?></a>
				</td>
			</tr>

		<?php elseif ( $type == 'background' ) : ?>

			<div class="section-title">
				<h3><?php echo $item['name']; ?></h3>
				<p class="description"><?php echo $item['desc']; ?></p>
			</div>
			
			<table class="form-table">
				<tbody>

		<?php
		/* Font Color
		---------------*/
		?>
		<?php 
		if ( $font_color ) :
			$options = array( 
				'dark' => __( 'Dark', 'wolf' ), 
				'light' => __( 'Light', 'wolf' )
			);
			 ?>
			 	<tr valign="top">
			    		<th scope="row" class="titledesc">
						<label for="<?php echo $prefix; ?>[<?php echo $item['id']; ?>_font_color]"><?php _e( 'Font Color', 'wolf' ); ?></label>
					</th>
					
					<td class="forminp">
						<select name="<?php echo $prefix; ?>[<?php echo $item['id']; ?>_font_color]" id="<?php echo $item['id']; ?>_font_color">
						<?php foreach ( $options as $o => $v) : ?>
							<option value="<?php echo $o; ?>" <?php if ( stripslashes($this->get_option( $item['id'].'_font_color' ) ) == $o  ) echo 'selected="selected"'; ?>><?php echo $v; ?></option>
						<?php endforeach; ?>
						</select>
					</td>
				</tr>
			
			<?php
		endif;
			/* Color
			---------------*/
			?>
			<tr valign="top">
		    		<th scope="row" class="titledesc">
					<label for="<?php echo $prefix; ?>[<?php echo $item['id']; ?>_color]"><?php _e( 'Background Color', 'wolf' ); ?><br></label>
				</th>
				
				<td class="forminp">
					<input class="wolf-options-colorpicker" name="<?php echo $prefix; ?>[<?php echo $item['id']; ?>_color]" id="<?php echo $item['id']; ?>_color" style="width:75px" type="text" value="<?php if ( $this->get_option( $item['id'].'_color' ) ) echo htmlentities( stripslashes($this->get_option( $item['id'].'_color' ) ) ); ?>">
				</td>
			</tr>

		<?php
		/* Image
		---------------*/
		?>
			<tr valign="top">
		    		<th scope="row" class="titledesc">
					<label for="<?php echo $prefix; ?>[<?php echo $item['id']; ?>_img]"><?php _e( 'Background Image', 'wolf' ); ?>
				</label>
				</th>
				
				<td class="forminp">
					<input type="hidden" name="<?php echo $prefix; ?>[<?php echo $item['id']; ?>_img]" id="<?php echo $item['id']; ?>_img" value="<?php echo esc_url($this->get_option( $item['id'] . '_img' ) ); ?>">
					<img <?php if ( !$this->get_option( $item['id'] .'_img' ) ) echo 'style="display:none;"'; ?> class="wolf-options-img-preview" src="<?php echo esc_url($this->get_option( $item['id'] . '_img' ) ); ?>" alt="<?php echo $item['id']; ?>">
					<br><a href="#" class="button wolf-options-reset-bg"><?php _e( 'Clear', 'wolf' ); ?></a>
					<a href="#" class="button wolf-options-set-bg"><?php _e( 'Choose an image', 'wolf' ); ?></a>
				</td>
			</tr>

		<?php
		/* Repeat
		---------------*/
		?>
		<?php 
		$options = array( 'no-repeat', 'repeat', 'repeat-x', 'repeat-y' );
		 ?>
		 	<tr valign="top">
		    		<th scope="row" class="titledesc">
					<label for="<?php echo $prefix; ?>[<?php echo $item['id']; ?>_repeat]"><?php _e( 'Repeat', 'wolf' ); ?></label>
				</th>
				
				<td class="forminp">
					<select name="<?php echo $prefix; ?>[<?php echo $item['id']; ?>_repeat]" id="<?php echo $item['id']; ?>_repeat">
					<?php foreach ( $options as $o) : ?>
						<option value="<?php echo $o; ?>" <?php if ( stripslashes($this->get_option( $item['id'].'_repeat' ) ) == $o  ) echo 'selected="selected"'; ?>><?php echo $o; ?></option>
					<?php endforeach; ?>
					</select>
				</td>
			</tr>
		
		<?php
		/* Position
		---------------*/
		
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
	 		<tr valign="top">
		    		<th scope="row" class="titledesc">
					<label for="<?php echo $prefix; ?>[<?php echo $item['id']; ?>_position]"><?php _e( 'Position', 'wolf' ); ?></label>
				</th>
				
				<td class="forminp">
					<select name="<?php echo $prefix; ?>[<?php echo $item['id']; ?>_position]" id="<?php echo $item['id']; ?>_position">
					<?php foreach ( $options as $o) : ?>
						<option value="<?php echo $o; ?>" <?php if ( stripslashes($this->get_option( $item['id'].'_position' ) ) == $o  ) echo 'selected="selected"'; ?>><?php echo $o; ?></option>
					<?php endforeach; ?>
					</select>
				</td>
			</tr>
			
		<?php
		/* Attachment
		--------------------*/
		$options = array( 'scroll', 'fixed' ); 

		?>
			<tr valign="top">
		    		<th scope="row" class="titledesc">
					<label for="<?php echo $prefix; ?>[<?php echo $item['id']; ?>_attachment]"><?php _e( 'Attachment', 'wolf' ); ?></label>
				</th>
				
				<td class="forminp">
					<select name="<?php echo $prefix; ?>[<?php echo $item['id']; ?>_attachment]" id="<?php echo $item['id']; ?>_attachment">
					<?php foreach ( $options as $o) : ?>
						<option value="<?php echo $o; ?>" <?php if ( stripslashes($this->get_option( $item['id'].'_attachment' ) ) == $o  ) echo 'selected="selected"'; ?>><?php echo $o; ?></option>
					<?php endforeach; ?>
					</select>
				</td>
			</tr>
		
		<?php
		/* Size
		---------------*/
		$options = array( 
			'cover' => __( 'cover (resize)', 'wolf' ),
			'normal' => __( 'normal', 'wolf' ),
			'resize' => __( 'responsive (hard resize)', 'wolf' ),
		); 

		?>
			<tr valign="top">
		    		<th scope="row" class="titledesc">
					<label for="<?php echo $prefix; ?>[<?php echo $item['id']; ?>_size]"><?php _e( 'Size', 'wolf' ); ?></label>
				</th>
				
				<td class="forminp">
					<select name="<?php echo $prefix; ?>[<?php echo $item['id']; ?>_size]" id="<?php echo $item['id']; ?>_size">
					<?php foreach ( $options as $o => $v) : ?>
						<option value="<?php echo $o; ?>" <?php if ( stripslashes($this->get_option( $item['id'].'_size' ) ) == $o  ) echo 'selected="selected"'; ?>><?php echo $v; ?></option>
					<?php endforeach; ?>
					</select>
				</td>
			</tr>

				<?php if ( $parallax ): ?>
					<?php
					$checked = "";
					if ( $this->get_option( $item['id'] . '_parallax' ) == 'true' ) $checked = "checked=\"checked\""; 
					if ( $this->get_option( $item['id'] . '_parallax' ) == 'false' ) $checked = ""; 
					?>
					<tr valign="top">
				    		<th scope="row" class="titledesc">
							<label for="<?php echo $prefix; ?>[<?php echo $item['id']; ?>_parallax]"><?php _e( 'Parallax', 'wolf' ); ?></label>
							</label>
						</th>
						
						<td class="forminp">
							<input type="checkbox" name="<?php echo $prefix; ?>[<?php echo $item['id']; ?>_parallax]" id="<?php echo $item['id']; ?>_parallax" value="true" <?php echo $checked; ?>>
						</td>
					</tr>

				<?php endif ?>
			
			</tbody>
		</table>
		
		<?php

		/* Font
		---------------*/
		?>

		<?php elseif ( $type == 'font-title' ) : ?>

			<div class="wolf_input wolf_select">
				<div class="wolf_input_label">
					<label for="<?php echo $prefix; ?>[<?php echo $item['id']; ?>]"><?php echo $item['name']; ?> transform
						<br><small></small>
					</label>
				</div>
				<select name="<?php echo $prefix; ?>[<?php echo $item['id']; ?>_transform]" id="<?php echo $item['id']; ?>_transform" class="wolf_font_select_transform">
					<option value="normal" <?php if ( stripslashes($this->get_option( $item['id'].'_transform' ) ) == 'normal'  ) { echo 'selected="selected"'; } ?>>Normal</option>
					<option value="uppercase" <?php if ( stripslashes($this->get_option( $item['id'].'_transform' ) ) == 'uppercase'  ) { echo 'selected="selected"'; } ?>>Uppercase</option>
				</select>
			</div>

			<div class="wolf_input wolf_select">
				<label for="<?php echo $prefix; ?>[<?php echo $item['id']; ?>]"><?php echo $item['name']; ?>
					<br><small><?php echo $desc; ?></small>
				</label>
				
			<select name="<?php echo $prefix; ?>[<?php echo $item['id']; ?>]" id="<?php echo $item['id']; ?>" class="wolf_font_select">
				<?php foreach ( $item['options'] as $v => $o) { ?>
				<option value="<?php echo $v; ?>" <?php if ( stripslashes($this->get_option( $item['id'] ) ) == $v  ) { echo 'selected="selected"'; } ?>><?php echo $o; ?></option>
				<?php } ?>
			</select>

				<div class="wolf_font_preview" style="display:inline-block; margin:25px 0 0 10%; font-size:250%; font-family:<?php echo ( $this->get_option( $item['id'] ) ) ? stripslashes( $this->get_option( $item['id'] ) ) : $def; ?>; ">
					L<span style="text-transform: <?php echo ( $this->get_option( $item['id'].'_transform' ) ) ? stripslashes( $this->get_option( $item['id'].'_transform' ) ) : 'normal';  ?>">orem ipsum</span>
				</div>
				
			</div>

		<?php elseif ( $type == 'font-body' ) :

			$size = '';

			if ( $this->get_option( $item['id'] ) == 'Times New Roman' || $this->get_option( $item['id'] ) == 'Geneva' ) {
				
				$size  = 'font-size:14px';  

			} 

			?>
			<div class="wolf_input wolf_select">
				<label for="<?php echo $prefix; ?>[<?php echo $item['id']; ?>]"><?php echo $item['name']; ?>
					<br><small><?php echo $desc; ?></small>
				</label>
				
			<select name="<?php echo $prefix; ?>[<?php echo $item['id']; ?>]" id="<?php echo $item['id']; ?>"  class="wolf_font_select">
				<?php foreach ( $item['options'] as $v => $o) { ?>
				<option value="<?php echo $v; ?>" <?php if ( stripslashes($this->get_option( 'body_font' ) ) == $v  ) { echo 'selected="selected"'; } ?>><?php echo $o; ?></option>
				<?php } ?>
			</select>

				<span class="wolf_font_preview" style="display:inline-blockk;margin:15px 0 0 10%; font-family:<?php echo ( $this->get_option( $item['id'] ) ) ? stripslashes( $this->get_option( $item['id'] ) ) : $def; ?>; <?php echo $size; ?>">I am a Preview</span>
			</div>

		<?php elseif ( $type == 'colorpicker' ) : ?>

			<tr valign="top">
		    		<th scope="row" class="titledesc">
					<label for="<?php echo $prefix; ?>[<?php echo $item['id']; ?>]"><?php echo $item['name']; ?><br>
						<small class="description"><?php echo $desc; ?></small>
					</label>
				</th>
				
				<td class="forminp">
					<input class="wolf-options-colorpicker" name="<?php echo $prefix; ?>[<?php echo $item['id']; ?>]" id="<?php echo $item['id']; ?>" type="text" value="<?php echo ( $this->get_option( $item['id'] ) ) ? htmlentities( stripslashes( $this->get_option( $item['id'] ) ) ) : $def; ?>">
				</td>
			</tr>

		<?php
		endif;
		
		} // end wolf_do_input function

	} // end class

} // end class exists check