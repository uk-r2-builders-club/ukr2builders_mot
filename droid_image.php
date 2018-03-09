<? 

include "includes/session.php";
include "includes/config.php";


// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$photo = $_REQUEST['photo'];
$droid_uid = $_REQUEST['droid_uid'];

$sql = "SELECT $photo FROM droids WHERE droid_uid = $droid_uid";
$image = $conn->query($sql)->fetch_object()->$photo;

header('Content-type: image/jpeg');

echo $image;

?>

