<?

error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    imagejpeg($image);
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


if ($_REQUEST['email'] != "") {
    $sql = "INSERT INTO members(forename, surname, email, county, postcode, latitude, longitude, badge_id, qr_code, created_on, created_by, username) VALUES (?,?,?,?,?,?,?,?,?, NOW(), ?, ?)";
    list($latitude, $longitude) = calcLocation($_REQUEST['postcode']);
    $badge_id = generateID(60);
    $qr = addslashes(generateQR($badge_id));
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssis", $_REQUEST['forename'], $_REQUEST['surname'], $_REQUEST['email'], $_REQUEST['county'], $_REQUEST['postcode'], $latitude, $longitude, $badge_id, $qr, $_SESSION['user'], $_REQUEST['username']);
    $stmt->execute();
    if ($stmt->error == "") {
	    echo "User created";
    } else {
	    echo "There was an error, please check the input and if it still looks ok, contact an admin";
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

$conn->close();
?>


