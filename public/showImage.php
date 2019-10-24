<?

include "../includes/config.php";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$name = $_REQUEST['name'];
$width = $_REQUEST['width'];
$sql = "SELECT * FROM droids WHERE droid_uid = ". $_REQUEST['droid_uid'];
$droid = $conn->query($sql)->fetch_assoc();

$image = "../uploads/members/".$droid['member_uid']."/".$droid['droid_uid']."/$name.jpg";

if (file_exists($image)) {
	$file = $image;
} else {
	$file = "../uploads/clubs/".$droid['club_uid']."/blank_$name.jpg";
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
