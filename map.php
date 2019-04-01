<?

include "includes/header.php";

if(!($_SESSION['permissions'] & $perms['VIEW_MAP'])) {
	die();
}

?>

  <script src="https://maps.google.com/maps/api/js?key=<? echo $config->google_map_api; ?>&sensor=false" 
          type="text/javascript"></script>
  <div id="map" style="width: 1024px; height: 800px;"></div>

  <script type="text/javascript">
    var locations = [
<?

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
	if ($row['latitude'] != "" ) {
		echo "[\"".$row['forename']." ".$row['surname']."\", ".$row['latitude'].", ".$row['longitude']."],";
	}
   }
}
echo "['blank', 0,0]";


?>
    ];

    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 6,
      center: new google.maps.LatLng(54.19, -3.2),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow();

    var marker, i;

    for (i = 0; i < locations.length; i++) {  
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        map: map
      });

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(locations[i][0]);
          infowindow.open(map, marker);
        }
      })(marker, i));
    }
  </script>
<?
include "includes/footer.php";
?>
</body>
</html>

