<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Wolf_Theme_Admin_Update_Zip' ) ) {
	/**
	 * Update Zip Theme Class
	 *
	 * Allow to update the theme through zip upload
	 *
	 * @since 1.4.1
	 * @package WolfFramework
	 * @author WolfThemes
	 */
	class Wolf_Theme_Admin_Update_Zip {

		/**
		 * List of all new files
		 *
		 * @var array
		 */
		public $files_list = array();

		/**
		 * Wolf_Theme_Admin_Update_Zip Constructor.
		 */
		public function __construct() {
			
			$this->do_update_zip();
		}

		/**
		 * Display upload form
		 *
		 * Check if the server allow unzipping archives
		 *
		 * @return string
		 */
		public function do_update_zip() {

			if ( class_exists( 'ZipArchive' ) ) {

				$this->theme_dir = WOLF_THEME_DIR;
				$this->tmp_dir   = WOLF_THEME_DIR . '/tmp';

				if ( $this->check_folder( $this->tmp_dir ) ) {
					$this->form();
					$this->submit();
				}
			} else {
				echo '<br>';
				printf(
					__( '<em>You server configuration does not allow you to update by uploading a zip. You need %s installed. You will have to update the theme via FTP.</em>', 'wolf' ), 
					'ZipArchive'
					);
			}

		}

		/**
		 * Check folder permission
		 *
		 * @param string $dir
		 * @return mixed
		 */
		public function check_folder( $dir ) {

			if ( is_writable( $dir ) ) {
				return true;
			}

			elseif ( is_dir( $dir ) && ! is_writable( $dir ) ) {
				if ( chmod( $dir, 777 ) ) {
					return true;
				} else {
					$message = __(
						'To be able to update using zip file upload, the <strong>"tmp"</strong> folder inside the theme directory has to be writable. Set the folder permission to 777 through your FTP client. Otherwise you can choose to update the theme via FTP.', 
						'wolf'
						);
					wolf_admin_notice( $message, 'error', true, 'error_tmp_dir_permission' );
					return false;
				}
			}

			elseif ( ! is_dir( $dir ) ) {

				$old_mask = umask( 0 );
				if ( ! mkdir( $dir, 0777 ) ) {
					$message = sprintf(
						__(
							'Error while trying to find the folder <strong>%s</strong>. 
							Please create it manually and set the permission to 777 through your FTP client.<br>
							This folder is necessary to be able to update the theme via zip file uload.
							Otherwise you can choose to update the theme via FTP.', 'wolf' 
						), $dir 
					);
					wolf_admin_notice( $message, 'error', true, 'error_tmp_dir' );
					return false;
				}
				umask( $old_mask );
			}
		}

		/**
		 * Output form
		 */
		public function form() {
			?>
			<h4><?php _e( 'Update using zip file upload', 'wolf' ); ?></h4>
			<p><?php printf(
				__( 'To update the theme, please upload the %s file from your theme package.', 'wolf' ), 
			'<strong>' . wolf_get_theme_slug() . '.zip</strong>' 
				); ?></p>
			<form action="<?php echo esc_url( admin_url( 'admin.php?page=wolf-theme-update' ) ); ?>" enctype="multipart/form-data" method="post">
				<input type="file" name="wolf-zip">
				<input type="submit" name="wolf-zip-submit" value="<?php _e( 'Update Theme', 'wolf' ); ?>">
			</form>
			<?php
		}

		/**
		 * Check the filename on form submit
		 */
		public function submit() {

			if ( isset( $_POST['wolf-zip-submit'] ) ) {
				
				if ( ! empty( $_FILES['wolf-zip']['name'] ) ) {
					
					if ( $_FILES['wolf-zip']['name'] != wolf_get_theme_slug() . '.zip' ) {
						
						$message  = __( 'It seems that you are trying to upload the wrong file.', 'wolf' );
						$message .= sprintf( __( 'You have to upload the %s file.', 'wolf' ), '<strong>' . wolf_get_theme_slug() . '.zip</strong>' );
						wolf_admin_notice( $message, 'error' );
						return false;

					} else {
						if ( $this->unzip( $_FILES['wolf-zip'] ) ) {
							$message = __( 'Theme Updated.', 'wolf' );
							wolf_admin_notice( $message, 'updated' );
							return false;
						}
					}
				} else {
					$message = __( 'Please select a file to upload.', 'wolf' );
					wolf_admin_notice( $message, 'error' );
					return false;
				}
			}
		}

		/**
		 * Unzip files in tmp folder, then replace the files
		 *
		 * @param string $file
		 * @return bool
		 */
		public function unzip( $file ) {
			
			$this->remove_tmp_files( $this->tmp_dir ); // ensure that the tmp folder is empty
			$file_path = $file['tmp_name'];
			$zip = new ZipArchive;
			$res = $zip->open( $file_path );
			if ( $res === TRUE ) {
				$zip->extractTo( $this->tmp_dir );
				$zip->close();
				$this->recurse_copy( $this->tmp_dir . '/' . wolf_get_theme_slug() , $this->theme_dir );
				$this->remove_tmp_files( $this->tmp_dir );
				$this->remove_deprecated_files( $this->theme_dir );
				return true;
			
			} else {
				$message = __( 'An occur occured while trying to update the theme.', 'wolf' );
				wolf_admin_notice( $message, 'error' );
				return false;
			}
		}

		/**
		 * Recursive copy
		 *
		 * @param string $src
		 * @param string $dst
		 */
		public function recurse_copy( $src, $dst ) { 
			
			if ( is_dir( $src ) ) {
				$dir = opendir( $src );
				@mkdir( $dst ); 
				while ( false !== ( $file = readdir( $dir ) ) ) { 
					if ( ( $file != '.' ) && ( $file != '..' ) ) { 
						if ( is_dir( $src . '/' . $file ) && $dir != $src ) { 
							
							self::recurse_copy( $src . '/' . $file, $dst . '/' . $file ); 
						} else { 

							copy( $src . '/' . $file, $dst . '/' . $file );
							$this->files_list[] = $dst . '/' . $file;
						} 
					} 
				} 
				closedir( $dir );
			}
		}

		/**
		 * Remove temporary files from tmp folder
		 *
		 * @param string $dirname
		 */
		public function remove_tmp_files( $dirname ) {
			
			if ( is_dir( $dirname ) )
				$dir_handle = opendir( $dirname );
			
			if ( ! $dir_handle )
				return false;
			
			while ( $file = readdir( $dir_handle ) ) {
				if ( $file != '.' && $file != '..' ) {
					if ( ! is_dir( $dirname . '/' . $file ) )
						unlink( $dirname . '/' . $file );
					else
						self::remove_tmp_files( $dirname . '/' . $file );
				}
			}

			closedir( $dir_handle );

			if ( $dirname != $this->tmp_dir )
				rmdir( $dirname );
			
			return true;
		}

		/**
		 * Remove old files that no longer belong to the theme
		 *
		 * @param string $dirname
		 */
		public function remove_deprecated_files( $dirname ) {
			
			if ( is_dir( $dirname ) )
				$dir_handle = opendir( $dirname );
			else
				return;

			while ( $file = readdir( $dir_handle ) ) {
				if ( $file != '.' && $file != '..' ) {
					if ( ! is_dir( $dirname . '/' . $file ) && ! in_array( $dirname . '/' . $file, $this->files_list ) ) {
						
						unlink( $dirname . '/' . $file );
					
					} else {
						self::remove_deprecated_files( $dirname . '/' . $file );
					}
				}
			}

			closedir( $dir_handle );

			if ( $dirname != $this->tmp_dir && count( glob( "$dirname/*" ) ) === 0 )
				rmdir( $dirname );

			// var_dump( $this->files_list );
			
			return true;
		}

	} // end class

} // end class exists check