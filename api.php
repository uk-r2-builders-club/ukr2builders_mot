<?

include "includes/config.php";

$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
mysqli_set_charset($conn, "utf8");

$api = $_REQUEST['api'];
$request = $_REQUEST['request'];
$id = $_REQUEST['id'];


function ytdCharity($conn) {
	$sql = "SELECT ROUND(SUM(charity_raised), 2) FROM events WHERE YEAR(date) = YEAR(CURRENT_DATE)";
	$result = $conn->query($sql)->fetch_row();
	return $result[0];
}

function getDriver($conn, $member_uid) {
	$sql = "SELECT member_uid, forename, surname, email FROM members WHERE member_uid = ". $member_uid;
	$result = $conn->query($sql);
	$member = $result->fetch_assoc();
        return json_encode($member);
}

function getDroid($conn, $droid_uid) {
        $sql = "SELECT droid_uid, member_uid, name, material, weight, transmitter_type FROM droids WHERE droid_uid = ". $droid_uid;
        $result = $conn->query($sql);
        $droid = $result->fetch_assoc();
        return json_encode($droid);
}

function getUids($conn, $table) {
	$uids = array();
	$column = substr($table,0,-1);
        $sql = "SELECT ".$column."_uid FROM ".$table;
        $result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_row()) {
			array_push($uids, $row[0]);
		}
	}
        return json_encode($uids);
}

function getMugShot($conn, $member_uid) {
	$width = 300;
	$image = "uploads/members/$member_uid/mug_shot.jpg";
	if (file_exists($image)) {
            $file = $image;
        } else {
            $file = "images/blank_mug_shot.jpg";
        }
	header('Content-Length: ' . filesize($file));
        list($orig_width, $orig_height) = getimagesize($file);
        $source = imagecreatefromjpeg($file);
        $height = (($orig_height * $width) / $orig_width);

        $thumb = imagecreatetruecolor($width, $height);
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $width, $height, $orig_width, $orig_height);
        // save new thumb with quality 75
        imagejpeg($thumb, $path, 75);
}

function getDroidShot($conn, $droid_uid) {
        $sql = "SELECT member_uid FROM droids WHERE droid_uid = ". $droid_uid;
        $result = $conn->query($sql);
        $member = $result->fetch_assoc();

        $width = 300;
        $image = "uploads/members/".$member['member_uid']."/$droid_uid/photo_front.jpg";
        if (file_exists($image)) {
            $file = $image;
        } else {
            $file = "images/blank_photo_front.jpg";
        }
        header('Content-Length: ' . filesize($file));
        list($orig_width, $orig_height) = getimagesize($file);
        $source = imagecreatefromjpeg($file);
        $height = (($orig_height * $width) / $orig_width);

        $thumb = imagecreatetruecolor($width, $height);
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $width, $height, $orig_width, $orig_height);
        // save new thumb with quality 75
        imagejpeg($thumb, $path, 75);
}

function insertRun($conn, $data) {
	$json = json_decode(str_replace("'", '"', $data), true);
	echo json_encode($json['penalties']);
	$sql = "INSERT INTO course_runs(member_uid, droid_uid, first_half, second_half, clock_time, final_time, num_penalties, penalties, run_timestamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
	$stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiiiiiss", $json['member_uid'], $json['droid_uid'], $json['first_half'], $json['second_half'], $json['clock_time'], $json['final_time'], $json['num_penalties'], json_encode($json['penalties']), $json['start']);
        $stmt->execute();
        echo $stmt->sqlstate;
        $stmt->close();
	return "";
}

// Decode request
if ($api == $config->course_api) {
    switch($request) 
    {
	    case "droid": // Requesting droid details
	    	$droid = getDroid($conn, $id);
	    	echo $droid;
            	break;

	    case "member": // Requesting droid details
	    	$member = getDriver($conn, $id);
	    	echo $member;
            	break;

	    case "mug_shot": // Request member mug shot
            	$mug_shot = getMugShot($conn, $id);
	    	header("Content-Type: image/jpeg");
	    	break;

            case "droid_shot": // Request droid mug shot
            	$droid_shot = getDroidShot($conn, $id);
	    	header("Content-Type: image/jpeg");
            	break;

	    case "list_droid_uid": // List all droid uids
	    	$uids = getUids($conn, "droids");
	    	echo $uids;
	    	break;

            case "list_member_uid": // List all droid uids
            	$uids = getUids($conn, "members");
            	echo $uids;
            	break;

	    case "insert_course_run": // Insert a course run
		$insert = insertRun($conn, $id);
		echo $insert;
		break;

	    case "ytd_charity":
		$amount = ytdCharity($conn);
		echo $amount;
		break;

    }
} else {
    echo "Invalid key";
}

?>

