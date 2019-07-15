<?

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "includes/header.php";

if((!$_SESSION['permissions'] & $perms['EDIT_MEMBERS'])) {
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
    $url = "https://chart.googleapis.com/chart?cht=qr&chld=L|1&chs=100x100&chl=".urlencode($link);
    $image = imagecreatefrompng($url);
    imagejpeg($image, $path, 75);
    return "Ok";
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
	if (!empty($postcode)) {
       	    $address = str_replace(' ','+',$postcode);
	    $url = 'https://maps.google.com/maps/api/geocode/json?key='.$config->google_map_api.'&address='.$address.'&sensor=false';
       	    $geocode=file_get_contents($url);
       	    $output= json_decode($geocode);
       	    $latitude = $output->results[0]->geometry->location->lat;
       	    $longitude = $output->results[0]->geometry->location->lng;
	    return array($latitude, $longitude);
	}
}


if (isset($_REQUEST['email'])) {
    $sql = "INSERT INTO members(forename, surname, email, county, postcode, latitude, longitude, badge_id, created_on, created_by, username) VALUES (?,?,?,?,?,?,?,?, NOW(), ?, ?)";
    list($latitude, $longitude) = calcLocation($_REQUEST['postcode']);
    $badge_id = generateID(60);
    //generateQR($badge_id);
    if (!file_exists("uploads/members/".$row['member_uid'])) {
         mkdir("uploads/members/".$row['member_uid']);
    }
    if (!file_exists("uploads/members/".$row['member_uid']."/qr_code.jpg")) {
         generateQR($row[badge_id], $row['member_uid']);
    }
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssis", $_REQUEST['forename'], $_REQUEST['surname'], $_REQUEST['email'], $_REQUEST['county'], $_REQUEST['postcode'], $latitude, $longitude, $badge_id, $_SESSION['user'], $_REQUEST['username']);
    $stmt->execute();
    if ($stmt->error == "") {
	    echo "User created";
    } else {
	    echo "There was an error, please check the input and if it still looks ok, contact an admin <br />";
	    printf("Error: %s.\n", $stmt->sqlstate);
            printf("Error: %s.\n", $stmt->error);
    }
    $stmt->close();
}

echo "<form>";
echo "<ul>";
echo " <li>Forename: <input type=text name=forename size=50></li>";
echo " <li>Surname: <input type=text name=surname size=50></li>";
echo " <li>Email: <input type=email name=email size=50></li>";
echo " <li>County: <input type=text name=county size=50></li>";
echo " <li>Postcode: <input type=text name=postcode size=10></li>";
echo " <li>Forum Username: <input type=text name=username size=50></li>";
echo "</ul>";
echo "<input type=submit name=add value=Add>";

include "includes/footer.php";
?>


