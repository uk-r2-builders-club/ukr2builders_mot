<? 

include "includes/header.php";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>

<script>
function openClub(evt, clubName) {
  var i, x, tablinks;
  x = document.getElementsByClassName("club");
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablink");
  for (i = 0; i < x.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" w3-red", "");
  }
  document.getElementById(clubName).style.display = "block";
  evt.currentTarget.className += " w3-red";
}
</script>

<?

$sql = "SELECT * FROM club_config";
$clubs = $conn->query($sql);

$sql = "SELECT * FROM droids WHERE public = 'Yes' AND active = 'on'";
$droids = $conn->query($sql);

echo "<div class=\"w3-sidebar w3-bar-block w3-light-grey w3-card\" style=\"width:200px\">";
echo "<button class=\"w3-bar-item w3-button tablink\" onclick=\"openClub(event, 'Home')\">Home</button>";
while ($row = $clubs->fetch_assoc()) {
        echo "<button class=\"w3-bar-item w3-button tablink\" onclick=";
        echo "\"openClub(event, '".$row['shortname']."')\"";
        echo ">".$row['name'];
        echo "</button>";
}
echo "</div>";
$clubs->data_seek(0);

echo "<div class=\"bg\" style=\"margin-left:200px\">";
echo "<div id='Home' class=\"w3-container club\"><h2>Droid Database</h2>";
echo "</div>";
while ($club = $clubs->fetch_assoc()) {
	echo "<div id=\"".$club['shortname']."\" class=\"w3-container club\" style=\"display:none\">";
	echo "<h2>".$club['name']."</h2>";
	echo "<div class=droid_grid>";
	while($droid = $droids->fetch_assoc()) {
		if($droid['club_uid'] == $club['club_uid']) {
			echo "<a href=display.php?droid_uid=".$droid['droid_uid'].">";
			echo $droid['name'];
			echo "<div class=flip-container><div class=flipper>";
			echo "<div class=front><img id=photo_front src=\"showImage.php?droid_uid=".$droid['droid_uid']."&name=photo_front&width=240\"></div>";
			echo "<div class=back><img id=photo_front src=\"showImage.php?droid_uid=".$droid['droid_uid']."&name=photo_rear&width=240\"></div>";
			echo "</div></div>";
			echo "</a>";
		}
	}
	$droids->data_seek(0);
	echo "</div>";
	echo "</div>";
}

echo "</div>";
echo "</body>";
echo "</html>";
