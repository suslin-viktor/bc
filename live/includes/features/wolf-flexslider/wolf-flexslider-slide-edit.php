<?php
$id = intval($_GET['edit']);
$dir = WOLF_FLEXSLIDER_FILES_DIR;
$path = WOLF_FLEXSLIDER_FILES_URL;
$row = $wpdb->get_row("SELECT * FROM $wolf_flexslider_table WHERE id = $id");
$img = $row->img;
$size = getimagesize($dir.'/'.$img);

$actual_width = $size[0];
$actual_height = $size[1];

if( isset($_POST['updateimg']) ){
    	//debug($_POST);
	//Get the new coordinates to crop the image.

	if( $_POST["x1"] != 'NaN' ){

		$x1 = $_POST["x1"];
		$y1 = $_POST["y1"];
		$x2 = $_POST["x2"];
		$y2 = $_POST["y2"];
		$w = $_POST["w"];
		$h = $_POST["h"];

		$cropped = wolf_img_area_crop($dir.'/slides/'.$img, $dir.'/'.$img,$w,$h,$x1,$y1, $wolf_flexslider_width, $wolf_flexslider_height);

		$link = esc_url($_POST['link']);


		$coordinates_array = array( $x1, $y1, $x2, $y2, $w, $h );
		$coordinates = serialize( $coordinates_array );
		$values = array('link' => $link, 'caption' => $_POST['caption'],  'coordinates' => $coordinates, 'language_code' => ICL_LANGUAGE_CODE );
		$conditions = array( 'id' => $id);
		$values_types = array('%s');
		$conditions_types = array('%d');
		$wpdb->update($wolf_flexslider_table, $values, $conditions, $values_types, $conditions_types); 
		wolf_admin_notice(__('Image updated', 'wolf'), 'updated');
	}else{
		wolf_admin_notice(__('Select an image area to crop', 'wolf'), 'error');
	}
}

$row = $wpdb->get_row("SELECT * FROM $wolf_flexslider_table WHERE id = $id");  
$actual_coordinates = unserialize( $row->coordinates );

$x1 = $actual_coordinates[0];
$y1 = $actual_coordinates[1];			
$x2 = $actual_coordinates[2];
$y2 = $actual_coordinates[3];
$w = $actual_coordinates[4];
$h = $actual_coordinates[5];

?>
<div class="wrap">
	<h1><?php _e('Edit this Slide', 'wolf'); ?></h1>
	<hr>
	<div>        	  
		<img src="<?php echo $path . $img; ?>" style="float: left; margin-right: 10px;" id="thumbnail" alt="Create Thumbnail" />
		
	</div>

	<form action="<?php echo esc_url(admin_url('admin.php?page=wolf-flexslider-panel')); ?>&amp;edit=<?php echo $id; ?>" method="post">
		<?php wp_nonce_field('wolf_edit_flexslider_image', 'wolf_edit_flexslider_image_nonce'); ?>
		<p><strong><?php _e('All fields are optional', 'wolf'); ?></strong></p>
		<p>
			<label for="link"><?php _e('Link', 'wolf'); ?>:</label><br>
			<input type="text" name="link" value="<?php  echo esc_url($row->link); ?>">
		</p>
		<p>
			<label for="caption"><?php _e('Caption', 'wolf'); ?>:</label><br>
			<textarea name="caption" cols="50" rows="5"><?php echo stripslashes($row->caption); ?></textarea><br>
			<em><?php _e('HTML allowed', 'wolf'); ?></em>
		</p>
		<input type="hidden" name="x1" value="<?php echo $x1; ?>" id="x1">
		<input type="hidden" name="y1" value="<?php echo $y1; ?>" id="y1">
		<input type="hidden" name="x2" value="<?php echo $x2; ?>" id="x2">
		<input type="hidden" name="y2" value="<?php echo $y2; ?>" id="y2">
		<input type="hidden" name="w" value="<?php echo $w; ?>" id="w">
		<input type="hidden" name="h" value="<?php echo $h; ?>" id="h">
		<p><input id="save_thumb" type="submit" name="updateimg" class="button-primary" value="<?php _e('Update', 'wolf'); ?>"></p>
	
	</form>
	<p><a href="<?php echo esc_url(admin_url('admin.php?page=wolf-flexslider-panel')); ?>"><?php _e('back to the main page', 'wolf'); ?></a></p>
</div>
<script type="text/javascript">

function preview(img, selection) { 
	var scaleX = <?php echo $wolf_flexslider_width;?> / selection.width; 
	var scaleY = <?php echo $wolf_flexslider_height;?> / selection.height; 
	
	jQuery('#thumbnail + div > img').css({ 
		width: Math.round(scaleX * <?php echo $actual_width;?>) + 'px', 
		height: Math.round(scaleY * <?php echo $actual_height;?>) + 'px',
		marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px', 
		marginTop: '-' + Math.round(scaleY * selection.y1) + 'px' 
	});
	jQuery('#x1').val(selection.x1);
	jQuery('#y1').val(selection.y1);
	jQuery('#x2').val(selection.x2);
	jQuery('#y2').val(selection.y2);
	jQuery('#w').val(selection.width);
	jQuery('#h').val(selection.height);
} 

jQuery(function($) {
	$('#save_thumb').click(function() {
		var x1 = $('#x1').val();
		var y1 = $('#y1').val();
		var x2 = $('#x2').val();
		var y2 = $('#y2').val();
		var w = $('#w').val();
		var h = $('#h').val();
		// if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
		// 	alert("You must make a selection first");
		// 	return false;
		// }else{
		// 	return true;
		// }
	});
}); 

jQuery(window).load(function () { 
	jQuery('#thumbnail').imgAreaSelect({ 
	onSelectChange: preview , handles: true,
	aspectRatio: '1:<?php echo $wolf_flexslider_height/$wolf_flexslider_width;?>', 
	x1: <?php echo $x1 ?>, 
	y1: <?php echo $y1 ?>,
	x2: <?php echo $x2 ?>, 
	y2: <?php echo $y2 ?>,
	}); 
});

</script>