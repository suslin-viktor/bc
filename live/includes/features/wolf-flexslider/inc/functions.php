<?php
/* *
* The slider is compatible with WPML plugin 
* We define a default language constant in english in case WPML plugin is not used
*/
if(!defined('ICL_LANGUAGE_CODE'))
	define('ICL_LANGUAGE_CODE', 'en');

if ( ! function_exists( 'wolf_delete_file' ) ) {
	function wolf_delete_file($file_path){

		if( is_file( $file_path ) )
			unlink($file_path);

	}
}

if( !function_exists('wolf_img_area_crop') ):
function wolf_img_area_crop($thumb_image_name, $image, $width, $height, $start_width, $start_height, $dest_w, $dest_h){
	list($imagewidth, $imageheight, $imageType) = getimagesize($image);
	$imageType = image_type_to_mime_type($imageType);

	$newImage = imagecreatetruecolor($dest_w,$dest_h);
	switch($imageType) {
		case "image/gif":
			$source=imagecreatefromgif($image); 
			break;
	    case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
			$source=imagecreatefromjpeg($image); 
			break;
	    case "image/png":
		case "image/x-png":
			$source=imagecreatefrompng($image); 
			break;
  	}

  	/* Keep transparency */
	if($imageType=='image/gif' || $imageType == 'image/png' || $imageType == 'image/x-png'){

		imagealphablending($newImage, false);
		imagesavealpha($newImage,true);
		$transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
		imagefilledrectangle($newImage, 0, 0, 0, 0, $transparent);
	}

	imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$dest_w,$dest_h,$width,$height);
	switch($imageType) {
		case "image/gif":
	  		imagegif($newImage,$thumb_image_name); 
			break;
      	case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
	  		imagejpeg($newImage,$thumb_image_name,90); 
			break;
		case "image/png":
		case "image/x-png":
			imagepng($newImage,$thumb_image_name);  
			break;
    	}
	chmod($thumb_image_name, 0777);
	return $thumb_image_name;
}
endif;

if( !function_exists('wolf_resizeImage') ):
function wolf_resizeImage($img,$max_w,$max_h) {
	
	$size = getimagesize($img);

	$width = $size[0];
	$height = $size[1];

	if( $max_w >= $width ){
		$max_w = $width;
	}

	if( $max_h >= $height ){
		$max_h = $height;
	}

	if( $height >= $width ){
		$scale = $max_h/$height;
	}
	
	elseif($width > $height){
		$scale = $max_w/$width;
	}
				
	$imageType = image_type_to_mime_type($size[2]);
	$newImageWidth = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale); 
	$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
	switch($imageType) {
		case "image/gif":
			$source=imagecreatefromgif($img); 
			break;
	    	case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
			$source=imagecreatefromjpeg($img); 
			break;
	    	case "image/png":
		case "image/x-png":
			$source=imagecreatefrompng($img); 
			break;
  	}

  	/* Keep transparency */
	if($imageType=='image/gif' || $imageType == 'image/png' || $imageType == 'image/x-png'){

		imagealphablending($newImage, false);
		imagesavealpha($newImage,true);
		$transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
		imagefilledrectangle($newImage, 0, 0, 0, 0, $transparent);
	}

	imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
	
	switch($imageType) {
		case "image/gif":
	  		imagegif($newImage,$img); 
			break;
      		case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
	  		imagejpeg($newImage,$img,90); 
			break;
		case "image/png":
		case "image/x-png":
			imagepng($newImage,$img);  
			break;
	}
	
	chmod($img, 0777);
	return $img;
}
endif;


if( !function_exists('wolf_get_default_crop_coordinates') ):
function wolf_get_default_crop_coordinates( $img, $max_w, $max_h ) {

	$size = getimagesize($img);

	$width = $size[0];
	$height = $size[1];

	if($width > $max_w){

		$w = $max_w;
		$x1 = ($width - $max_w) / 2;

	}elseif($width <= $max_w){

		$w = $width;
		$x1 = 0;

	}

	
	$h = ($w * $max_h)/$max_w;
	

	$y1 = ($height - $h) / 2;

	
	$x2 = $x1 + $w;
	$y2 = $y1 + $h;

	$coordinates = array( ceil($x1), ceil($y1), ceil($x2), ceil($y2), ceil($w), ceil($h) );

	return $coordinates;
}
endif;


/**
* Generate a thumbnail from an uploaded image
* @param string : filename
* @param string : path to the image
* @param string : image name
* @param int : max width in px
* @param int : max height in px
*/
if(!function_exists(('wolf_thumbnail'))):
function wolf_thumbnail($img,$path,$name,$mwidth=1140,$mheight=480)
{
    list($imagewidth, $imageheight, $imageType) = getimagesize($img);
    $imageType = image_type_to_mime_type($imageType);
    $dimension=getimagesize($img);

    if($imageType=='image/gif'){
        $source=imagecreatefromgif($img); 
    }elseif( $imageType == 'image/pjpeg' || $imageType == 'image/jpeg' || $imageType == 'image/jpg'){
        $source=imagecreatefromjpeg($img); 
    }elseif( $imageType == 'image/png' || $imageType == 'image/x-png'){
        $source=imagecreatefrompng($img); 
    }       

    $min = imagecreatetruecolor($mwidth,$mheight); 
 
    if($dimension[0]>($mwidth/$mheight)*$dimension[1] ){ 
        $dimY=$mheight; 
        $dimX=$mheight*$dimension[0]/$dimension[1]; 
        $decalX=-($dimX-$mwidth)/2; 
        $decalY=0;
    }
    if($dimension[0]<($mwidth/$mheight)*$dimension[1]){ 
        $dimX=$mwidth; 
        $dimY=$mwidth*$dimension[1]/$dimension[0]; 
        $decalY=-($dimY-$mheight)/2; 
        $decalX=0;
    }
    if($dimension[0]==($mwidth/$mheight)*$dimension[1]){ 
        $dimX=$mwidth; 
        $dimY=$mheight; 
        $decalX=0; 
        $decalY=0;
    }
    

     /* Keep transparency */
     if($imageType=='image/gif' || $imageType == 'image/png' || $imageType == 'image/x-png'){

	imagealphablending($min, false);
	imagesavealpha($min,true);
	$transparent = imagecolorallocatealpha($min, 255, 255, 255, 127);
	imagefilledrectangle($min, 0, 0, $decalX, $decalY, $transparent);
     }


    imagecopyresampled($min,$source,$decalX,$decalY,0,0,$dimX,$dimY,$dimension[0],$dimension[1]);

    if($imageType=='image/gif'){
        imagegif($min,$path.$name); 
    }elseif( $imageType == 'image/pjpeg' || $imageType == 'image/jpeg' || $imageType == 'image/jpg'){
        imagejpeg($min,$path.$name,90); 
    }elseif( $imageType == 'image/png' || $imageType == 'image/x-png'){
        imagepng($min,$path.$name);  
    }

    imagedestroy($source);
    return true;    
}
endif;
?>