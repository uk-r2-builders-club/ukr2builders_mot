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

function generateQR($id) {
    global $config;
    $link = $config->site_base."/id.php?id=".$id;
    $url = "https://chart.googleapis.com/chart?cht=qr&chld=H|1&chs=200x200&chl=".urlencode($link);
    $image = imagecreatefrompng($url);
    ob_start();
    imagepng($image);
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}

function generateID($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;

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
		if (($row['badge_id'] == "")) {
			echo "Updating Badge ID and QR Code for ".$row['forename']." ".$row['surname']."<br/>";
			$badge_id = generateID(60);
                        $qr = addslashes(generateQR($badge_id));
			$sql = "UPDATE members SET badge_id='".$badge_id."', qr_code='".$qr."' WHERE member_uid = ".$row['member_uid'];
			$result=$conn->query($sql);


		}

	}
}

