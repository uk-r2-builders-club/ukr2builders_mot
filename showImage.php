<?

include "includes/config.php";
include "includes/session.php";

$member_id = $_REQUEST['member_id'];
$type = $_REQUEST['type'];
$name = $_REQUEST['name'];
$width = $_REQUEST['width'];
if (isset($_REQUEST['droid_id'])) {
	$droid_id = $_REQUEST['droid_id'];
}

if (isset($_REQUEST['club_uid'])) {
        $club_uid = $_REQUEST['club_uid'];
}


if ( !$_SESSION['permissions'] & $perms['VIEW_MEMBERS'] ) {
	$member_id = $_SESSION['user'];
}

if ( !$_SESSION['permissions'] & $perms['VIEW_DROIDS'] ) {
        $member_id = $_SESSION['user'];
}


if ($type == "droid") {
	$image = "uploads/members/$member_id/$droid_id/$name.jpg";
} elseif ($type == "member") {
	$image = "uploads/members/$member_id/$name.jpg";
} elseif ($type == "topps") {
	$image = "uploads/members/$member_id/$droid_id/$name.jpg";
}

if (file_exists($image)) {
	$file = $image;
} else {
	if ($type == "droid") {
	    $file = "uploads/clubs/".$club_uid."/blank_$name.jpg";
	} else {
	    $file = "images/blank_$name.jpg";
	}
}
	
header('Content-Type: image/jpeg');
header('Content-Length: ' . filesize($file));
list($orig_width, $orig_height) = getimagesize($file);
$source = imagecreatefromjpeg($file);
$height = (($orig_height * $width) / $orig_width);

$thumb = imagecreatetruecolor($width, $height);
imagecopyresampled($thumb, $source, 0, 0, 0, 0, $width, $height, $orig_width, $orig_height);
// save new thumb with quality 75
imagejpeg($thumb, $path, 75);



?>
