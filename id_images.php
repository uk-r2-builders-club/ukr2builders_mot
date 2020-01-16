<?
include "includes/config.php";
include "includes/session.php";


if ($_SESSION['role'] == "user") {
        $_REQUEST['member_uid'] = $_SESSION['user'];
}

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM members WHERE member_uid = ". $_REQUEST['member_uid'];
$result = $conn->query($sql);
$member = $result->fetch_assoc();

$sql = "SELECT * FROM droids WHERE member_uid = ". $_REQUEST['member_uid']. " AND active='on'";
$droids = $conn->query($sql);

$zip = new ZipArchive();
$filename = $member['forename']."_".$member['surname'].".zip";
if ($zip->open("uploads/".$filename, ZipArchive::CREATE)!==TRUE) {
    exit("cannot open <$filename>\n");
}

$zip->addFile("uploads/members/".$member['member_uid']."/mug_shot.jpg", $member['forename']."_".$member['surname'].".jpg");
$zip->addFile("uploads/members/".$member['member_uid']."/qr_code.jpg", $member['forename']."_".$member['surname']."_QR.jpg");
if ($droids->num_rows > 0) {
    while($row = $droids->fetch_assoc()) {
        $zip->addFile("uploads/members/".$member['member_uid']."/".$row['droid_uid']."/photo_front.jpg", "droid_".$row['name'].".jpg");
    }
}
$zip->close();
$conn->close();

header("Content-type: application/zip");
header("Content-Disposition: attachment; filename=$filename");
header("Content-length: " . filesize("uploads/".$filename));
header("Pragma: no-cache");
header("Expires: 0");
readfile("uploads/".$filename);
unlink("uploads/".$filename);
?>
