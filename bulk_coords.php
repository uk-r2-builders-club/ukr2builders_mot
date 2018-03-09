<?

include "includes/header.php";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$sql = "SELECT * FROM members";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		if (($row['latitude'] == "" ) && ( $row['postcode'] != "" )) {
			echo "Updating coords for ".$row['forename']." ".$row['surname']."<br/>";
        		$address = str_replace(' ','+',$row["postcode"]);
        		$geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?key=<? echo $config->google_map_api; ?>&address='.$address.'&sensor=false');
        		$output= json_decode($geocode);
        		$latitude = $output->results[0]->geometry->location->lat;
        		$longitude = $output->results[0]->geometry->location->lng;
			echo "New coords = $latitude, $longitude<br /><br />";
			$sql = "UPDATE members SET latitude=?, longitude=? WHERE member_uid = ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("ssi", $latitude, $longitude, $row['member_uid']);
			$stmt->execute();
			printf("Error: %s.\n", $stmt->sqlstate);
			printf("Error: %s.\n", $stmt->error);
    		}	

	}
}

