<?php

include "includes/config.php";
include "includes/session.php";

/* Get post details */
$post = isset($_POST) ? $_POST: array();
switch($post['action']) {
	case 'save' :
	savePicTmp($post['type'], $post['member_uid']);
	break;
	default:
	changePic($post['type'], $post['member_uid']);
}
/* Function to change profile picture */
function changePic($type, $member_uid) {
	$post = isset($_POST) ? $_POST: array();
	$max_width = "500"; 
	$path = 'uploads/members';
	$valid_formats = array("jpg", "png", "gif", "jpeg");
	$name = $_FILES['pic']['name'];
	$size = $_FILES['pic']['size'];
	if(strlen($name)) {
		list($txt, $ext) = explode(".", $name);
		if(in_array($ext,$valid_formats)) {
			if($size<(2048*2048)) {
				$actual_image_name = 'mug_shot.'.$ext;
				$filePath = $path .'/'.$member_uid.'/'.$actual_image_name;
				$tmp = $_FILES['pic']['tmp_name'];
				if(move_uploaded_file($tmp, $filePath)) {
					$width = getWidth($filePath);
					$height = getHeight($filePath);
					//Scale the image if it is greater than the width set above
					if ($width > $max_width){
						$scale = $max_width/$width;
						$uploaded = resizeImage($filePath,$width,$height,$scale, $ext);
					} else {
						$scale = 1;
						$uploaded = resizeImage($filePath,$width,$height,$scale, $ext);
					}					
					echo "<img id='photo' file-name='".$actual_image_name."' class='' src='showImage.php?member_id=".$member_uid."&type=member&name=mug_shot&width=500' class='preview'/>";
				}
				else
				echo "failed";
			}
			else
			echo "Image file size max 1 MB"; 
		}
		else
		echo "Invalid file format.."; 
	}
	else
	echo "Please select image..!";
	exit;
}
	
/* Function to update image */
function savePicTmp($type, $uid) {
	$post = isset($_POST) ? $_POST: array();
	$path ='uploads/members/'.$post['member_uid'];
	$t_width = 480; // Maximum thumbnail width
	$t_height = 640;    // Maximum thumbnail height	
	if(isset($_POST['t']) and $_POST['t'] == "ajax") {
		extract($_POST);		
		$imagePath = $path.'/'.$_POST['image_name'];
		$newImage = $path.'/480-'.$_POST['image_name'];
		$ratio = ($t_width/$w1); 
		$nw = ceil($w1 * $ratio);
		$nh = ceil($h1 * $ratio);
		$nimg = imagecreatetruecolor($nw,$nh);
		$im_src = imagecreatefromjpeg($imagePath);
		imagecopyresampled($nimg,$im_src,0,0,$x1,$y1,$nw,$nh,$w1,$h1);
		imagejpeg($nimg,$newImage,90);		
		$t_width = 240; // Maximum thumbnail width
		$t_height = 320;    // Maximum thumbnail height
		$newImage = $path.'/240-'.$_POST['image_name'];
		$ratio = ($t_width/$w1);
                $nw = ceil($w1 * $ratio);
                $nh = ceil($h1 * $ratio);
                $nimg = imagecreatetruecolor($nw,$nh);
                $im_src = imagecreatefromjpeg($imagePath);
                imagecopyresampled($nimg,$im_src,0,0,$x1,$y1,$nw,$nh,$w1,$h1);
                imagejpeg($nimg,$newImage,90);
	}
	echo $imagePath.'?'.time();;
	exit(0);    
}    
/* Function  to resize image */
function resizeImage($image,$width,$height,$scale, $ext) {
	$newImageWidth = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale);
	$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
	switch ($ext) {
        case 'jpg':
        case 'jpeg':
            $source = imagecreatefromjpeg($image);
            break;
        case 'gif':
            $source = imagecreatefromgif($image);
            break;
        case 'png':
            $source = imagecreatefrompng($image);
            break;
        default:
            $source = false;
            break;
    }	
	imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
	imagejpeg($newImage,$image,90);
	chmod($image, 0777);
	return $image;
}
/*  Function to get image height. */
function getHeight($image) {
    $sizes = getimagesize($image);
    $height = $sizes[1];
    return $height;
}
/* Function to get image width */
function getWidth($image) {
    $sizes = getimagesize($image);
    $width = $sizes[0];
    return $width;
}
?>
