<?php
class Wolf_Live_Upgrader{

	function __construct() {

		if ( isset( $_GET['restore'] ) && $_GET['restore'] == true ) {

			ini_set( 'max_execution_time', 1200 );
			//sleep(3);
			$this->init();
			add_option( '_has_restored', '1.6.5' );
			// wp_redirect( admin_url() );
			// exit();

		}

	}

	function init() {

		$theme_slug = WOLF_THE_THEME;

		$uploads = wp_upload_dir();

		$uploads_dir = $uploads['basedir'] . '/' . $theme_slug;

		$this->update_templates();
		$this->update_formats();
		$this->update_tables();
		$this->update_meta();
		$this->update_options();
		$this->update_taxonomy();
		$this->update_shortcodes();
		$this->create_folder($uploads_dir);
		$this->create_folder($uploads_dir . '/wolf-flexslider');
		$this->create_folder($uploads_dir . '/wolf-flexslider/slides');
		$this->create_folder($uploads_dir . '/wolf-refineslide');
		$this->create_folder($uploads_dir . '/wolf-refineslide/slides');
		$this->move_files('bd-refineslide', 'wolf-refineslide');
		$this->move_files('bd-refineslide/slides', 'wolf-refineslide/slides');
		$this->move_files('bd-flexslider', 'wolf-flexslider');
		$this->move_files('bd-flexslider/slides', 'wolf-flexslider/slides');

	}

	function move_files( $old_dir_name, $new_dir_name ) {

		$theme_slug = 'live';

		$uploads = wp_upload_dir();

		$uploads_dir = $uploads['basedir'] . '/' . $theme_slug;

		$old_dir = $uploads_dir . '/' . $old_dir_name;
		$new_dir = $uploads_dir . '/' . $new_dir_name;

		if( is_dir($old_dir) && is_dir($new_dir) ){

			$files = scandir($old_dir);
			foreach ($files as $file) {

				if ($file === '.' || $file === '..') continue;

				if (is_file($old_dir . '/' . $file)) {
					//debug($file);
					copy( $old_dir .'/'. $file, $new_dir .'/'. $file );
				}
			}

		}
	}

	function create_folder($dir){
		if ( ! is_dir( $dir ) ) {

			$old_mask = umask(0);
			if( ! mkdir( $dir, 0777 ) ) {
				echo 'Error creating the folder <strong>' . $dir . '</strong>. Please create it manually and set the permission to 777 through your FTP client.';
			}
			umask( $old_mask );

		}else{
			return null;
		}
	}

	function update_tables() {

		global $wpdb;

		$table ="CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}wolf_sidebars` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `wp_id` varchar(255) NULL,
		  `name` varchar(255) NULL,
		  `desc` text NULL,
		  `type` text NULL,
		  `cols` varchar(255) NULL,
		  `position` int(11) NOT NULL DEFAULT 0,
		  PRIMARY KEY (`id`)
		);";
		$wpdb->query($table);

		/* Flexslider */
		$old_flexslider_table = $wpdb->prefix.'bd_flexslider';
		$new_flexslider_table = $wpdb->prefix.'wolf_flexslider';

		$old_slides = $wpdb->get_results("SELECT * FROM $old_flexslider_table");

		if( $old_slides ){

			foreach($old_slides as $s){

				$check = $wpdb->get_row("SELECT * FROM $new_flexslider_table WHERE img = '$s->img'");

				$data = array(
					'img' => $s->img ,
					'link' => $s->link,
					'caption' => $s->caption,
					'position' => $s->position,
					'coordinates' => $s->coordinates,
					'language_code' => 'en'
				);

				$format = array('%s', '%s', '%s', '%s', '%d', '%s', '%s');

				if( ! $check )
					$wpdb->insert( $new_flexslider_table, $data, $format );

			}

		}

		/* Refineslide */
		$old_refineslide_table = $wpdb->prefix.'bd_refineslide';
		$new_refineslide_table = $wpdb->prefix.'wolf_refineslide';

		$old_slides = $wpdb->get_results("SELECT * FROM $old_refineslide_table");

		if( $old_slides ){

			foreach($old_slides as $s){

				$check = $wpdb->get_row("SELECT * FROM $new_refineslide_table WHERE img = '$s->img'");

				$data = array(
					'img' => $s->img ,
					'link' => $s->link,
					'caption' => $s->caption,
					'position' => $s->position,
					'coordinates' => $s->coordinates,
					'language_code' => 'en'
				);

				$format = array('%s', '%s', '%s', '%s', '%d', '%s', '%s');

				if( ! $check )
					$wpdb->insert( $new_refineslide_table, $data, $format );

			}

		}

		/* Sidebars */

		$old_sidebars_table = $wpdb->prefix.'bd_sidebars';
		$new_sidebars_table = $wpdb->prefix.'wolf_sidebars';

		$old_sidebars = $wpdb->get_results("SELECT * FROM $old_sidebars_table");

		if( $old_sidebars ) {

			foreach($old_sidebars as $s) {

				$check = $wpdb->get_row("SELECT * FROM $new_sidebars_table WHERE wp_id = '$s->wp_id'");

				$data = array(
					'wp_id' => $s->wp_id ,
					'name' => $s->name,
					'desc' => $s->desc,
					'position' => $s->position,
					'type' => $s->type,
					'cols' => 4
				);

				$format = array('%s', '%s', '%s', '%d', '%s', '%d' );

				if( ! $check )
					$wpdb->insert( $new_sidebars_table, $data, $format );


			}

		}

	}

	function update_meta() {

		global $wpdb;

		$post_meta_table = $wpdb->prefix.'postmeta';

		$new_metas = array(

			// page options
			'bd_page_headline' => '_top_holder',
			'bd_page_bottomline' => '_bottom_holder',
			'bd_custom_bg_color' => '_header_bg_color',
			'bd_custom_bg_img' => '_header_bg_img',
			'bd_custom_bg_repeat' => '_header_bg_repeat',
			'bd_custom_bg_attachment' => '_header_bg_attachment',

			// releases
			'bd_release_date' => '_wolf_release_date',
			'bd_release_title' => '_wolf_release_title',
			'bd_release_itunes' => '_wolf_release_itunes',
			'bd_release_amazon' => '_wolf_release_amazon',
			'bd_paypal_price' => '',
			'bd_release_buy_cd' => '_wolf_release_buy',


			// shows
			'bd_show_date' => '_wolf_show_date',
			'bd_show_time' => '_wolf_show_time',
			'bd_show_address' => '_wolf_show_address',
			'bd_show_map' => '_wolf_show_map',
			'bd_show_city' => '_wolf_show_city',
			'bd_show_venue' => '_wolf_show_venue',
			'bd_show_ticket' => '_wolf_show_ticket',
			'bd_show_cancel' => '_wolf_show_cancel',
			'bd_show_soldout' => '_wolf_show_cancel',
			'bd_show_free' => '_wolf_show_free',
			'bd_show_fb' => '_wolf_show_fb',



		);

		foreach ( $new_metas as $o => $n ) {

			$rows = $wpdb->get_results("SELECT * FROM $post_meta_table WHERE `meta_key` = '$o'");

			foreach( $rows as $r ){

				$value = $r->meta_value;
				$post_id = $r->post_id;

				if ( $o == 'bd_paypal_price' ) {

					$paypal_link = $this->paypal_button_to_link( $post_id );
					update_post_meta( $post_id, '_wolf_release_buy', $paypal_link );

				} elseif ( $o == 'bd_page_headline' || $o == 'bd_page_bottomline' ) {

					$value = $this->replace( $r->meta_value );
					update_post_meta( $post_id, $n, $value );

				} elseif ( $o == 'bd_release_buy_cd' && ! get_post_meta( $post_id, 'bd_paypal_price', true ) ) {

					update_post_meta( $post_id, '_wolf_release_buy', $value );

				} else {
					update_post_meta( $post_id, $n, $value );
				}

			}
		}

	}

	function paypal_button_to_link( $post_id ) {

		$bd_paypal_settings = get_option('bd_paypal_settings');

		if ( isset( $bd_paypal_settings['email'] ) && isset( $bd_paypal_settings['currency'] ) ) {

			$email = $bd_paypal_settings['email'];
			$currency = $bd_paypal_settings['currency'];

			$price = get_post_meta( $post_id, 'bd_paypal_price', true );
			$item_name = get_post_meta( $post_id, 'bd_paypal_item_name', true );

			// https://www.paypal.com/cgi-bin/webscr?&cmd=_xclick
			// &business=qsd@qsd.com
			// &currency_code=USD
			// &amount=12
			// &item_name=test

			if ( $price && $item_name ) {

				return "https://www.paypal.com/cgi-bin/webscr?&cmd=_xclick&business=$email&currency_code=$currency&amount=$price&item_name=$item_name";

			}

		}

	}

	function update_formats() {

		$loop = new WP_Query("post_type=post&posts_per_page=-1");

		if ( $loop->have_posts() ) {

			while ( $loop->have_posts() ) { $loop->the_post();

				global $post;
				$format = get_post_format();
				$post_id = get_the_ID();
				$meta = null;

				if ( $format == 'video' ) {

					$meta = '<p>' . get_post_meta( $post_id, '_format_video_embed', true ) . '</p>';

				} elseif ( $format == 'quote' && get_post_meta( $post_id, '_format_quote_text', true ) ) {

					$meta = '<blockquote>';
					$meta .= '<p>' . get_post_meta( $post_id, '_format_quote_text', true ) . '</p>';

					if ( get_post_meta( $post_id, '_format_quote_source_name', true ) ) {
						$meta .= '<cite><p>';
						$meta .= get_post_meta( $post_id, '_format_quote_source_name', true );
						$meta .= '</p></cite>';

					}

					$meta .= '</blockquote>';

				} elseif ( $format == 'link' && get_post_meta( $post_id, '_format_link_url', true ) ) {

					$meta = '<p>' . get_post_meta( $post_id, '_format_link_url', true ) . '</p>';

				} elseif ( $format == 'audio' && get_post_meta( $post_id, '_format_audio_mp3', true ) ) {

					$meta = '[audio mp3="' .  get_post_meta( $post_id, '_format_audio_mp3', true ) . '"][/audio]';

				}

				if (
					$meta
					&& ! get_option( '_w_format_updated_1.6.5' )
				) {
					// var_dump($meta . get_the_content() );

					$update_post = array(
						'ID'           => $post_id,
						'post_content' => $meta . $post->post_content
					);

					wp_update_post( $update_post );

				}

			}

		}
		add_option( '_w_format_updated_1.6.5', true );
		wp_reset_query();
		wp_reset_postdata();

	}

	function replace( $string ) {


		$pattern = '\[(\[?)(bd_jplayer)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';

		if ( preg_match( "/$pattern/s", $string, $match ) ) {

			if ( 'bd_jplayer' == $match[2] ) {

				if ( strpos( $match[3], 'mp3' ) ) {

					$string = str_replace( $match[0], '[audio' . $match[3] . '][/audio]', $string );

				}


			}
		}

		$search = array(
			'bd_space', '[bd_bigtweet]', '[bd_center]', '[/bd_center]', '[bd_separator]',  'bd_'
		);

		$replace = array(
			'wolf_spacer', '[wolf_tweet username="wp_wolf"]', '<div style="text-align:center;">', '</div>', '<hr>', 'wolf_'
		);

		return str_replace( $search, $replace, $string );

	}

	function update_shortcodes() {


		global $wpdb;
		$post_table = $wpdb->prefix.'posts';

		$posts = $wpdb->get_results("SELECT `post_content`, `ID` FROM $post_table");
		//debug($posts);

		foreach($posts as $p){
			$post_content = $this->replace($p->post_content);
			$query = "UPDATE $post_table SET `post_content` = '$post_content' WHERE `ID` = '$p->ID'";
			$wpdb->query($query);
		}

	}

	function update_templates() {

		global $wpdb, $options;

		$post_meta_table = $wpdb->prefix.'postmeta';

		$values = array(

			/* templates */
			'template-home-fullwidth.php' => 'page-templates/full-width.php',
			'template-fullwidth.php' => 'page-templates/full-width.php',
			'template-home-blog.php' => 'page-templates/home-blog.php',
			'template-home.php' => '',
			'template-video-gallery.php' => 'videos-template.php',
			'template-galleries.php' => 'albums-template.php',
			'template-shows.php' => 'shows-template.php',
			'template-contact.php' => 'contact-template.php',
			'template-blog-archives.php' => 'page-templates/post-archives.php',
			'template-store.php' => 'store-template.php',
			'template-discography.php' => 'discography-template.php',

		);

		//$query = "UPDATE $post_meta_table SET `meta_value` = `template-home-blog.php` WHERE `meta_value` = `home_page_blog.php`";
		foreach($values as $k => $v){
			$data = array( 'meta_value' => $v );
			$condition = array( 'meta_value' => $k );
			$values_types = array('%s');
			$conditions_types = array('%s');
			$wpdb->update($post_meta_table, $data, $condition, $values_types, $conditions_types);
		}
	}

	function update_options() {

		$fs_settings = get_option('bd_flexslider_settings');
		if ( $fs_settings ) add_option( 'wolf_flexslider_settings', $fs_settings );

		$share_settings = get_option('bd_share_settings');
		if ( $share_settings ) add_option( 'wolf_share_settings', $share_settings );

		$rfs_settings = get_option('bd_rfs_settings');
		if ( $rfs_settings ) add_option( 'wolf_rfs_settings', $rfs_settings );

	}

	function update_taxonomy() {

		global $wpdb;

		$term_taxonomy_table = $wpdb->prefix.'term_taxonomy';

		/* Taxonomy */
		$values = array(

			'gallery-category' => 'gallery_type',

		);

		foreach($values as $k => $v){
			$query = "UPDATE $term_taxonomy_table SET `taxonomy` = '$v' WHERE `taxonomy` = '$k'";

			$wpdb->query($query);
		}

	}


	function update_store() {

	}



} // end class
new Wolf_Live_Upgrader;