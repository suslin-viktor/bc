<?php
include_once WOLF_REFINESLIDE_DIR . '/inc/functions.php';

global $wpdb, $options; 

$wolf_refineslide_table = $wpdb->prefix.'wolf_refineslide';
$wolf_refineslide_settings = get_option('wolf_refineslide_settings');

$wolf_refineslide_width = 1140;
$wolf_refineslide_height = $wolf_refineslide_settings['height'];

// --------------------------------------------------------------------------


// Edit image
if(isset( $_GET['edit']))
	require 'wolf-refineslide-slide-edit.php';


// --------------------------------------------------------------------------

// Re-order Slide by drag & drop with jquery-ui sortable
if(isset($_POST['sortable'])){
	$sortlist = $_POST['sortable'];
	/*
	* $k = position
	* $v = id
	*/
	foreach ($sortlist as $k => $v) {
		//echo $k.' = '. $v;
		$wpdb->query("UPDATE $wolf_refineslide_table SET position=$k WHERE id = $v");

	}
}

// --------------------------------------------------------------------------


// Delete image
if(isset($_POST['wolf-slides-action'])){

	if(isset($_POST['box'])){
		$boxes = $_POST['box'];

		foreach($boxes as $id){
			$del = $wpdb->get_row("SELECT * FROM $wolf_refineslide_table WHERE id = $id");
	
			if( $del ){

				wolf_delete_file(WOLF_REFINESLIDE_FILES_DIR.'/'.$del->img);
				wolf_delete_file(WOLF_REFINESLIDE_FILES_DIR.'/slides/'.$del->img);

				$wpdb->query("DELETE FROM $wolf_refineslide_table WHERE id = $id");
				
			}
		}

		wolf_admin_notice(__('Slides deleted', 'wolf'), 'updated');
	}else{
		wolf_admin_notice(__('No slide selected', 'wolf'), 'error');
	}
	

}


// Delete image
if(isset($_GET['delete'])){
	
	$delid = $_GET['delete'];
	/* Delete from database */
	$deli = $wpdb->get_row("SELECT * FROM $wolf_refineslide_table WHERE id = $delid");
	
	if($deli){

		wolf_delete_file(WOLF_REFINESLIDE_FILES_DIR.'/'.$deli->img);
		wolf_delete_file(WOLF_REFINESLIDE_FILES_DIR.'/slides/'.$deli->img);

		$wpdb->query("DELETE FROM $wolf_refineslide_table WHERE id = $delid");
		
	}

	
	unset($_GET);
}


// --------------------------------------------------------------------------

/**
* Image Upload
*
*/
if( isset($_POST['submitimg']) && wp_verify_nonce($_POST['wolf_upload_refineslide_image_nonce'],'wolf_upload_refineslide_image') ){

	/*Verif*/
	$tmp = $_FILES['img']['tmp_name'];
	$ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION); 
	//$img_name = mktime().'.'.$ext;
	
	$raw_img_name = $_FILES['img']['name'];
	$img_name = wp_unique_filename(WOLF_REFINESLIDE_FILES_DIR, $raw_img_name);

	if(empty($raw_img_name)){
		$error_message = __('Please choose an image to upload', 'wolf');
		wolf_admin_notice( $error_message, 'error' );

	}elseif(strpos($_FILES['img']['type'], 'image') === false){
		$error_message = '<strong>'.$raw_img_name.'</strong> '. __('is not an image. Only jpg, png or gif are allowed', 'wolf');
		wolf_admin_notice( $error_message, 'error' );

	}elseif($_FILES['img']['size'] > ini_get('upload_max_filesize') * 1000000){
		$error_message = '<strong>'.$raw_img_name.'</strong> '.__('is too big for your server. Your server upload max size is' , 'wolf') . ini_get('upload_max_filesize');
		wolf_admin_notice($error_message, 'error');

	}else{
		/* Upload */ 
		move_uploaded_file($tmp,  WOLF_REFINESLIDE_FILES_DIR.'/'.$img_name);
		wolf_resizeImage(WOLF_REFINESLIDE_FILES_DIR.'/'.$img_name, 1920, 1500);
		wolf_thumbnail(WOLF_REFINESLIDE_FILES_DIR.'/'.$img_name, WOLF_REFINESLIDE_FILES_DIR.'/slides/', $img_name, $wolf_refineslide_width, $wolf_refineslide_height );
		
		$coordinates_array = wolf_get_default_crop_coordinates(WOLF_REFINESLIDE_FILES_DIR.'/'.$img_name, $wolf_refineslide_width, $wolf_refineslide_height);
		$coordinates = serialize( $coordinates_array );

		/* insert db */
		$data = array('img' =>$img_name, 'position' => -1, 'caption_position' => 'bottom-right', 'coordinates' => $coordinates, 'language_code' => ICL_LANGUAGE_CODE);
		$format = array('%s', '%d', '%s');
		if($wpdb->insert( $wolf_refineslide_table, $data, $format ))
			wolf_admin_notice(__('Image uploaded', 'wolf'), 'updated');
		else
			wolf_admin_notice(__('Error inserting database', 'wolf'), 'error');
		
	}

}

/* Req */
$slides = $wpdb->get_results("SELECT * FROM $wolf_refineslide_table WHERE language_code='".ICL_LANGUAGE_CODE."' ORDER BY position");

if(!isset($_GET['edit'])){ ?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"></div>
	<h2><?php _e('Slides Manager', 'wolf'); ?></h2>
	<hr>
	<strong><?php _e('These images will be used in the Home Page Slider', 'wolf'); ?> (refineslide)</strong>
	
	<hr>
	
	<h3><?php _e('Upload', 'wolf'); ?></h3>
		
		<div class="left">
			<form action="<?php echo esc_url(admin_url('admin.php?page=wolf-refineslide-panel')); ?>" method="post" enctype="multipart/form-data">
				<?php wp_nonce_field('wolf_upload_refineslide_image', 'wolf_upload_refineslide_image_nonce'); ?>
				<label for="img"><?php  printf( __('File (%1$s, %2$s or %3$s)', 'wolf' ) , 'jpg', 'png', 'gif'  ); ?> : </label>
				<input type="file" name="img">
				<input type="submit" name="submitimg" class="button-primary" onClick="javascript:Show('loader');" value="Upload">	
			</form>
		</div>
	
	<img id="loader" src="<?php echo admin_url('images/loading.gif'); ?>" alt="loader">
	<div class="clear"></div>
	<form action="<?php echo esc_url(admin_url('admin.php?page=wolf-refineslide-panel')); ?>" method="post" id="wolf-selected-form">
	<table class="wolf-custom-table">
		<thead>
			<th><input type="checkbox" id="wolf-check-all"></th>
			<th><?php _e('Image', 'wolf'); ?></th>
			<th><?php _e('Caption', 'wolf'); ?></th>
			<th><?php _e('Caption Position', 'wolf'); ?></th>
			<th><?php _e('Link', 'wolf'); ?></th>
			<th><?php _e('Action', 'wolf'); ?></th>
		</thead>
	        	<tbody  id="sortable">
			<?php if($slides): ?>
			<?php foreach($slides as $slide): ?>
			<?php
			/*  */
			$size = getimagesize(WOLF_REFINESLIDE_FILES_DIR.'/slides/'.$slide->img);   
			$preview_width = $size[0];
			?>
			<tr class="state-default" id="sortable_<?php echo $slide->id; ?>">
	
			<td><input name="box[]" type="checkbox" value="<?php echo $slide->id; ?>"></td>

			<td><a href="<?php echo esc_url(admin_url('admin.php?page=wolf-refineslide-panel&amp;edit='.$slide->id)); ?>"><img style="margin-top:15px" src="<?php echo WOLF_REFINESLIDE_FILES_URL.'slides/'.$slide->img; ?>" alt="slider" width="<?php echo $preview_width/4; ?>"></a></td>
</a>
			</td>
			<td>
				<a title="<?php _e('Edit', 'wolf'); ?>" href="<?php echo esc_url(admin_url('admin.php?page=wolf-refineslide-panel&amp;edit='.$slide->id)); ?>">
					<?php if(!empty($slide->caption)) { echo strip_tags($slide->caption); }else { _e('No caption', 'wolf'); } ?>
				</a>
			</td>
			<td>
				<a title="<?php _e('Edit', 'wolf'); ?>" href="<?php echo esc_url(admin_url('admin.php?page=wolf-refineslide-panel&amp;edit='.$slide->id)); ?>">
					<?php  echo str_replace('-', ' ', $slide->caption_position);  ?>
				</a>
			</td>
			
			<td><?php if(!empty($slide->link)) { echo $slide->link; }else {  _e('No link', 'wolf'); } ?></td>
			
			<td>
			<a href="<?php echo esc_url(admin_url('admin.php?page=wolf-refineslide-panel&amp;edit='.$slide->id)); ?>"><img class="hastip" src="<?php echo WOLF_REFINESLIDE_URL; ?>img/admin/edit.png" alt="edit" title="<?php _e('Edit', 'wolf'); ?>"></a>
			<a href="<?php echo esc_url(admin_url('admin.php?page=wolf-refineslide-panel&amp;delete='.$slide->id)); ?>" onclick="if (window.confirm('<?php _e('Are you sure to want to delete this slide ?', 'wolf'); ?>')) {location.href='default.htm';return true;} else {return false;}"><img class="hastip" src="<?php echo WOLF_REFINESLIDE_URL; ?>img/admin/delete.png" alt="delete" title="<?php _e('Delete', 'wolf'); ?>"></a>
			<img  class="action-icon move" title="<?php _e('move', 'wolf'); ?>" src="<?php echo WOLF_REFINESLIDE_URL; ?>img/admin/move.png">
			</td>
			<?php endforeach; ?>
			<?php else : ?>
			<p><?php  _e('No image uploaded yet', 'wolf'); ?>.</p>
			<?php endif; ?>
			</tr>
	        	</tbody>
	</table>
	
	<select style="position:relative; top:-20px" name="wolf-slides-action" id="wolf-slides-action">
		<option value=""><?php _e('Action', 'wolf'); ?></option>
		<option value="1"><?php _e('Delete selected slides', 'wolf'); ?></option>
	</select>
	</form>
	
	<h3><?php _e('Informations', 'wolf'); ?></h3>
	<p><?php printf(__('Your image will be scaled and cropped to %1$s X %2$s.', 'wolf'), '<strong>'.$wolf_refineslide_width.'px</strong>', '<strong>'.$wolf_refineslide_height.'px</strong>' ); ?>.<br>
	<?php printf(__('You can change the height in the %1$s options %2$s.', 'wolf') , '<a href="'.admin_url("admin.php?page=wolf-refineslide-settings").'"><strong>', '</strong></a>' ); ?><br>
	<?php printf(__('You have to re-crop your image after changing the height.', 'wolf') ); ?><br>
	<?php printf(__('You can re-crop your slide by clicking on "edit" near your image preview.', 'wolf') ); ?><br>
	</p>
	<p><em><?php _e('Note that you can change the height, but as the slider is responsive, the width can be only 1140px to fit to the layout', 'wolf'); ?></em><br>
	</p>
	<hr>
	<p style="margin-top:20px"><?php printf(__('Visit the official %s Website', 'wolf') , 'refineslide'); ?>:<br>
	<a target="_blank" href="<?php echo esc_url('http://alexdunphy.github.com/refineslide/'); ?>">http://alexdunphy.github.com/refineslide/</a></p>
</div>

<script type="text/javascript">
jQuery(function($){

	$('#wolf-check-all').click(function () {
		$(this).parents('table:eq(0)').find(':checkbox').attr('checked', this.checked);
	});

	var select = $('#wolf-slides-action');
	select.live('change', function(){
		var val = $(this).val();

		if(val == '1'){

			if(confirm("<?php _e('Are you sure to want to delete these slides?', 'wolf'); ?>")){
				$("#wolf-selected-form").submit();
			}
				
			
		}

	});

	var fixHelper = function(e, ui) {
	    ui.children().each(function() {
	        $(this).width($(this).width());
	    });
	    return ui;
	};
	$("#sortable").sortable({
		helper: fixHelper,
		placeholder: "state-highlight",
		opacity : 0.6,
		accept : 'state-default',
		update: function(){             
			serial = $("#sortable").sortable("serialize");
			$.ajax({
				url: "<?php echo esc_url(admin_url('admin.php?page=wolf-refineslide-panel')); ?>",
				type: "post",
				data: serial,
				complete: function(data){console.log(data);}
			});
		}
	});
	$( "#sortable" ).disableSelection();

});
</script>
<?php } ?>