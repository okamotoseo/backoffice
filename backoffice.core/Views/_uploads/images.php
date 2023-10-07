<?php

//https://pqina.nl/blog/creating-thumbnails-with-php/
// require_once './thumbnail.php';
// createThumbnail('profile.jpg', 'profile_thumb.jpg', 160);
// createThumbnail('sunset.jpg', 'sunset_thumb.jpg', 160, 160);
// Make sure file is not cached (as it happens for example on iOS devices)


// Link image type to correct image loader and saver
// - makes it easier to add additional types later on
// - makes the function easier to read
const IMAGE_HANDLERS = [
		IMAGETYPE_JPEG => [
				'load' => 'imagecreatefromjpeg',
				'save' => 'imagejpeg',
				'quality' => 90
		],
		IMAGETYPE_PNG => [
				'load' => 'imagecreatefrompng',
				'save' => 'imagepng',
				'quality' => 0
		],
		IMAGETYPE_GIF => [
				'load' => 'imagecreatefromgif',
				'save' => 'imagegif'
		]
];


/**
 * @param $src - a valid file location
 * @param $dest - a valid file target
 * @param $maxWidth - desired output width
 * @param $maxHeight - desired output height or null
 */
function createImage($src, $dest, $maxWidth, $maxHeight = null) {

	// 1. Load the image from the given $src
	// - see if the file actually exists
	// - check if it's of a valid image type
	// - load the image resource

	// get the type of the image
	// we need the type to determine the correct loader
	$type = exif_imagetype($src);
	// if no valid type or no handler found -> exit
	if (!$type || !IMAGE_HANDLERS[$type]) {
		return null;
	}
	// load the image with the correct loader
	$imgLoaded = call_user_func(IMAGE_HANDLERS[$type]['load'], $src);
	// no image found at supplied location -> exit
	if (!$imgLoaded) {
		return null;
	}

	// 2. Create a thumbnail and resize the loaded $imgLoaded
	// - get the image dimensions
	// - define the output size appropriately
	// - create a thumbnail based on that size
	// - set alpha transparency for GIFs and PNGs
	// - draw the final thumbnail

	// get original image width and height
	$widthImgLoaded = imagesx($imgLoaded);  //largura width
	$heightImgLoaded = imagesy($imgLoaded);  //altura height
	
	
	
	if ($maxHeight == null) {
	
		// get width to height ratio
		$ratio = $widthImgLoaded / $heightImgLoaded;
	
		// if is portrait
		// use ratio to scale height to fit in square
		if ($widthImgLoaded > $heightImgLoaded) {
			$maxHeight = floor($maxWidth / $ratio);
		}
		// if is landscape
// 		// use ratio to scale width to fit in square
		else {
			$maxHeight = $maxWidth;
			$maxWidth = floor($maxWidth * $ratio);
		}
	}
// 	pre($maxWidth);
// 	pre($maxHeight);
// 	pre(array($widthImgLoaded, $heightImgLoaded));
	
// 	// verifica se a largura ou altura da imagem é maior que o valor
// 	// máximo permitido
// 	if ( ( $widthImgLoaded > $maxWidth ) || ( $heightImgLoaded > $maxHeight ) ){
// 		$ratio = $widthImgLoaded / $heightImgLoaded;
// 		// verifica o que é maior na imagem, largura ou altura?
// 		if ( $widthImgLoaded > $heightImgLoaded ) {
// // 			$maxHeight	= ( $maxWidth * $heightImgLoaded ) / $widthImgLoaded;
// 			$maxHeight = floor($maxWidth / $ratio);
// 		}else{
// // 			$maxWidth	= ( $maxHeight * $widthImgLoaded ) / $heightImgLoaded;
// 			$maxHeight = $maxWidth;
// 			$maxWidth = floor($maxWidth * $ratio);
			
// 		}
		
		
// 	}else{
		
		return call_user_func(
				IMAGE_HANDLERS[$type]['save'],
				$imgLoaded,
				$dest,
				IMAGE_HANDLERS[$type]['quality']
				);
		
// 	}
// 	pre(array($maxWidth, $maxHeight));
// 	// create duplicate image based on calculated target size
// // 	$newImage = imagecreatetruecolor($maxWidth, $maxHeight);
	
// 	$newImage = imagecreatetruecolor($widthImgLoaded, $heightImgLoaded);
		
	// set transparency options for GIFs and PNGs
	if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG) {
	
		// make image transparent
		imagecolortransparent(
				$newImage,
				imagecolorallocate($newImage, 0, 0, 0)
				);
	
		// additional settings for PNGs
		if ($type == IMAGETYPE_PNG) {
			imagealphablending($newImage, false);
			imagesavealpha($newImage, true);
		}
	}
		
	// copy entire source image to duplicate image and resize
	imagecopyresampled(
			$newImage,
			$imgLoaded,
			0, 0, 0, 0,
			$maxWidth, $maxHeight,
			$widthImgLoaded, $heightImgLoaded
			);
	
	// 3. Save the $thumbnail to disk
	// - call the correct save method
	// - set the correct quality level
	
	// save the duplicate version of the image to disk
	return call_user_func(
			IMAGE_HANDLERS[$type]['save'],
			$newImage,
			$dest,
			IMAGE_HANDLERS[$type]['quality']
			);
	
}







/**
 * @param $src - a valid file location
 * @param $dest - a valid file target
 * @param $targetWidth - desired output width
 * @param $targetHeight - desired output height or null
 */
function createThumbnail($src, $dest, $targetWidth, $targetHeight = null) {

	// 1. Load the image from the given $src
	// - see if the file actually exists
	// - check if it's of a valid image type
	// - load the image resource
	// get the type of the image
	// we need the type to determine the correct loader
	$type = exif_imagetype($src);
	// if no valid type or no handler found -> exit
	if (!$type || !IMAGE_HANDLERS[$type]) {
		return null;
	}

	// load the image with the correct loader
	$image = call_user_func(IMAGE_HANDLERS[$type]['load'], $src);

	// no image found at supplied location -> exit
	if (!$image) {
		return null;
	}


	// 2. Create a thumbnail and resize the loaded $image
	// - get the image dimensions
	// - define the output size appropriately
	// - create a thumbnail based on that size
	// - set alpha transparency for GIFs and PNGs
	// - draw the final thumbnail

	// get original image width and height
	$width = imagesx($image);
	$height = imagesy($image);

	// maintain aspect ratio when no height set
	if ($targetHeight == null) {

		// get width to height ratio
		$ratio = $width / $height;

		// if is portrait
		// use ratio to scale height to fit in square
		if ($width > $height) {
			$targetHeight = floor($targetWidth / $ratio);
		}
		// if is landscape
		// use ratio to scale width to fit in square
		else {
			$targetHeight = $targetWidth;
			$targetWidth = floor($targetWidth * $ratio);
		}
	}

	// create duplicate image based on calculated target size
	$thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);

	// set transparency options for GIFs and PNGs
	if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG) {

		// make image transparent
		imagecolortransparent(
				$thumbnail,
				imagecolorallocate($thumbnail, 0, 0, 0)
				);

		// additional settings for PNGs
		if ($type == IMAGETYPE_PNG) {
			imagealphablending($thumbnail, false);
			imagesavealpha($thumbnail, true);
		}
	}

	// copy entire source image to duplicate image and resize
	imagecopyresampled(
			$thumbnail,
			$image,
			0, 0, 0, 0,
			$targetWidth, $targetHeight,
			$width, $height
			);


	// 3. Save the $thumbnail to disk
	// - call the correct save method
	// - set the correct quality level

	// save the duplicate version of the image to disk
// 	return $thumbnail;
	
	return call_user_func(
			IMAGE_HANDLERS[$type]['save'],
			$thumbnail,
			$dest,
			IMAGE_HANDLERS[$type]['quality']
			);
	
// 	$type = exif_imagetype($thumbnail);
// 	return call_user_func(IMAGE_HANDLERS[$type]['load'], $thumbnail);
}










function upload($tmp, $arquivo, $max_x, $max_y, $pasta){
	//$max_x = 800 $max_y = 630
	$img		= imagecreatefromjpeg($tmp);
	$original_x	= imagesx($img); //largura
	$original_y	= imagesy($img); //altura
	$diretorio	= $pasta."/".$arquivo;
	// verifica se a largura ou altura da imagem é maior que o valor
	// máximo permitido
	if ( ( $original_x > $max_x ) || ( $original_y > $max_y ) ){
		// verifica o que é maior na imagem, largura ou altura?
		if ( $original_x > $original_y ) {
			$max_y	= ( $max_x * $original_y ) / $original_x;
		}else{
			$max_x	= ( $max_y * $original_x ) / $original_y;
		}
		$nova = imagecreatetruecolor($max_x, $max_y);
		imagecopyresampled($nova, $img, 0, 0, 0, 0, $max_x, $max_y, $original_x, $original_y);
		imagejpeg($nova, $diretorio);
		imagedestroy($nova);
		imagedestroy($img);
		// se for menor, nenhuma alteração é feita
	}else{
		imagejpeg($img, $diretorio);
		imagedestroy($img);
	}
	return($arquivo);
}

