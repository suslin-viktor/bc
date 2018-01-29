<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists( 'wolf_admin_notice' ) ) {
	/**
	 * Custom admin notice
	 *
	 * @access public
	 * @param string $message
	 * @param string $type
	 * @param bool $dismiss
	 * @param string $id
	 */
	function wolf_admin_notice( $message = null, $type = null, $dismiss = false, $id = null ) {
		
		if ( $dismiss ) {

			$dismiss = __( 'Hide permanently', 'wolf' );

			if ( $id ) {
				if ( ! isset( $_COOKIE[ $id ] ) )
					echo "<div class='$type'><p>$message<span class='wolf-close-admin-notice'>&times;</span><span id='$id' class='wolf-dismiss-admin-notice'>$dismiss</span></p></div>";
			} else {
				echo "<div class='$type'><p>$message<span class='wolf-close-admin-notice'>&times;</span></p></div>";
			}
		} else {
			echo "<div class='$type'><p>$message</p></div>";
		}

		return false;

	}
	add_action( 'admin_notices', 'wolf_admin_notice' );
}

if ( ! function_exists( 'wolf_includes_file' ) ) {
	/**
	 * "Include file" function that checks if the "includes" directory and the required file exists before including it.
	 *
	 * @access public
	 * @param string $filename
	 */
	function wolf_includes_file( $filename = null, $folder = 'includes' ) {

		$inc_dir = '';

		if ( is_dir( WOLF_THEME_DIR . '/' . $folder ) ) {
			$inc_dir = WOLF_THEME_DIR . '/' . $folder;
		}
			
		if ( file_exists( $inc_dir . '/' . $filename ) ) {
			return include( $inc_dir . '/' . $filename );
		}
	}
}

if ( ! function_exists( 'wolf_get_theme_changelog' ) ) {
	/**
	 * Fetch XML changelog file from remote server
	 *
	 * Get the theme changelog and cache it in a transient key
	 *
	 * @return string
	 */
	function wolf_get_theme_changelog() {

		$changelog_url = WOLF_UPDATE_URL . '/' . WOLF_THEME_SLUG .'/changelog.xml';

		$trans_key = '_wolf_latest_theme_version_' . WOLF_THEME_SLUG;

		// delete_transient( $trans_key );
		
		if ( false === ( $cached_xml = get_transient( $trans_key ) ) ) {
			if ( function_exists( 'curl_init' ) ) {
				$ch = curl_init( $changelog_url );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch, CURLOPT_HEADER, 0 );
				curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
				$xml = curl_exec( $ch );
				curl_close( $ch );
			} else {
				$xml = file_get_contents( $changelog_url );
			}
		
			if ( $xml ) {
				set_transient( $trans_key, $xml, WOLF_CACHE_DURATION ); 
			}
		} else {
			$xml = $cached_xml;
		}

		if ( $xml ) {
			return @simplexml_load_string( $xml );
		}
	}
}

if ( ! function_exists( 'wolf_theme_update_notification_message' ) ) {
	/**
	 * Display the theme update notification notice
	 *
	 * @param bool $link
	 * @return string
	 */
	function wolf_theme_update_notification_message( $link = true ) {

		if ( WOLF_UPDATE_NOTICE ) {
			
			$changelog = wolf_get_theme_changelog();

			if ( $changelog && isset( $changelog->latest ) && -1 == version_compare( WOLF_THEME_VERSION, $changelog->latest ) ) {
				$message  = '';
				$message .= '<strong>' . sprintf( __( 'There is a new version of %s available.', 'wolf' ),  ucfirst( wolf_get_theme_slug() ) ) . '</strong>';
				$message .= sprintf( __( 'You have version %s installed.', 'wolf' ),  WOLF_THEME_VERSION );
				if ( $link ) {
					$message .= '<a href="' . esc_url( admin_url( 'admin.php?page=wolf-theme-update' ) ) . '">';
				}
					$message .= ' ' . sprintf( __( 'Update to version %s', 'wolf' ),  $changelog->latest );
				
				if ( $link ) {
					$message .= '</a>';
				}
					
				wolf_admin_notice( $message, 'updated', true, '_' . wolf_get_theme_slug() . 'update_notice' );
			}
		}
	}
}

if ( ! function_exists( 'debug' ) ) :
	/**
	 *  Debug function for developpment
	 *  Display less infos than a var_dump
	 *
	 * @param string $var
	 * @return string
	 */
	function debug( $var ) {
		echo '<br><pre style="border: 1px solid #ccc; padding:5px; width:98%">';
		print_r( $var );
		echo '</pre>';
	}
endif;