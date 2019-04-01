<?

include "includes/header.php";

if($_SESSION['role'] != "admin") {
	die();
}

?>
<script src="https://www.w3schools.com/lib/w3.js"></script>

<script>
function myFunction() {
  // Declare variables
  var input, filter, table, tr, td, i;
  input = document.getElementById("nameSearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("achievements_list");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[2];
    if (td) {
      if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}
</script>

<?
// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if (isset($_REQUEST['achievement_uid'])) {
    $sql = "SELECT * FROM achievements WHERE achievement_uid=".$_REQUEST['achievement_uid'];
    $achievement=$conn->query($sql)->fetch_assoc();
    echo "Display achievement";
    echo "<table>";
    echo "<tr>";
    echo "<td rowspan=6>";
    if ($achievement['image'] != "") {
	    echo "<img id=image src=data:image/jpeg;base64,".base64_encode( $achievement['image'] )." width=240>";
    } else {
	    echo "No image";
    }
    echo "</td>";
    echo "<td>ID</td><td>".$achievement['achievement_uid']."</td></tr>";
    echo "<tr><td>Name</td><td>".$achievement['name']."</td></tr>";
    echo "<tr><td>Description</td><td>".$achievement['description']."</td></tr>";
    echo "<tr><td>Date Created</td><td>".$achievement['date_created']."</td></tr>";
    echo "<tr><td>Date Updated</td><td>".$achievement['date_updated']."</td></tr>";
    echo "<tr><td>Icon</td><td>";
    if ($achievement['icon'] != "") {
            echo "<img id=icon src=data:image/jpeg;base64,".base64_encode( $achievement['icon'] )." width=40>";
    } else {
            echo "No icon";
    }
    echo "</td></tr>";
    echo "</table>";


}


$sql = "SELECT * FROM achievements";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    if ($_REQUEST['achievements_uid'] == "" ) {
            echo "<input type=\"text\" id=\"nameSearch\" onkeyup=\"myFunction()\" placeholder=\"Search for names..\">";
    }

    // output data of each row
    echo "<table class=achievements_list id=achievements_list>";
    echo "<tr>";
    echo "<th onclick=\"w3.sortHTML('#achievements_list','.item', 'td:nth-child(1)')\">ID</th>";
    echo "<th onclick=\"w3.sortHTML('#achievements_list','.item', 'td:nth-child(2)')\">Name</th>";
    echo "<th onclick=\"w3.sortHTML('#achievements_list','.item', 'td:nth-child(3)')\">Description</th>";
    echo "<th onclick=\"w3.sortHTML('#achievements_list','.item', 'td:nth-child(4)')\">Date Created</th>";
    echo "<th onclick=\"w3.sortHTML('#achievements_list','.item', 'td:nth-child(5)')\">Date Updated</th>";
    echo "<th onclick=\"w3.sortHTML('#achievements_list','.item', 'td:nth-child(6)')\">Icon</th></tr>";
    while($row = $result->fetch_assoc()) {
	    echo "<tr class=\"item\">";
	    echo " <td class=achievements_list>".$row['achievement_uid']."</td>";
	    if ($_SESSION['role'] == "admin") {
		echo " <td class=achievements_list><a href=achievements.php?achievement_uid=".$row['achievement_uid'].">".$row['name']."</a></td>";
	    } else {
	        echo " <td class=achievements_list>".$row['name']."</td>";
	    }
            echo " <td class=achievements_list>".$row['description']."</td>";
            echo " <td class=achievements_list>".$row['date_created']."</td>";
	    echo " <td class=achievements_list>".$row['date_updated']."</td>";
	    echo " <td class=achievements_list>";
	    if ($row['icon'] != "") {
	        echo "<img id=icon src=data:image/jpeg;base64,".base64_encode( $row['icon'] )." width=20>";
            } else {
	        echo "none";
            }
	    echo "</td>";

	    echo "</tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}

include "includes/footer.php";

?>
