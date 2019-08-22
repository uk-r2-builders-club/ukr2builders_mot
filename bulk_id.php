<?

include "includes/header.php";

if($_SESSION['role'] == "user") {
	die();
}


// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function generateQR($id, $member_uid) {
    $path = "uploads/members/$member_uid/qr_code.jpg";
    $link = "http://mot.astromech.info/id.php?id=".$id;
    $url = "https://chart.googleapis.com/chart?cht=qr&chld=L|1&chs=500x500&chl=".urlencode($link);
    echo "Generating QR Code: $url<br />";
    echo "Writing to path: $path<br />";
    $image = imagecreatefrompng($url);
    imagejpeg($image, $path, 75);
    return "Ok";
}

function calcLocation($postcode) {
	global $config;
       	$address = str_replace(' ','+',$postcode);
	$url = 'https://maps.google.com/maps/api/geocode/json?key='.$config->google_map_api.'&address='.$address.'&sensor=false';
       	$geocode=file_get_contents($url);
       	$output= json_decode($geocode);
       	$latitude = $output->results[0]->geometry->location->lat;
       	$longitude = $output->results[0]->geometry->location->lng;
	return array($latitude, $longitude);
}



$sql = "SELECT * FROM members";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		if (($row['latitude'] == "" ) && ( $row['postcode'] != "" )) {
			echo "Updating coords for ".$row['forename']." ".$row['surname']."<br/>";
			list($latitude, $longitude) = calcLocation($row['postcode']);
			echo "New coords = $latitude, $longitude<br /><br />";
			$sql = "UPDATE members SET latitude=?, longitude=? WHERE member_uid = ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("ssi", $latitude, $longitude, $row['member_uid']);
			$stmt->execute();
			printf("Error: %s.\n", $stmt->sqlstate);
			printf("Error: %s.\n", $stmt->error);
    		}	

		if (!file_exists("uploads/members/".$row['member_uid'])) {
                	mkdir("uploads/members/".$row['member_uid']);
        	}
		if (!file_exists("uploads/members/".$row['member_uid']."/qr_code.jpg")) {
			generateQR($row[badge_id], $row['member_uid']);
		}


	}
}

