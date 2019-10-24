<? 

include "includes/header.php";

if(!($_SESSION['permissions'] & $perms['VIEW_DROIDS'])) {
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
  table = document.getElementById("droid_list");
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

if ($_REQUEST['member_uid'] == "" ) {
    $sql = "SELECT * FROM droids WHERE active = 'on'";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT * FROM droids WHERE active = 'on' AND member_uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_REQUEST['member_uid']);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // output data of each row
    if ($_REQUEST['member_uid'] == "" ) {
	    echo "<input type=\"text\" id=\"nameSearch\" onkeyup=\"myFunction()\" placeholder=\"Search for names..\">";
    }
    echo "<table class=droid_list id=droid_list>";
    if ($_REQUEST['member_uid'] == "" ) {
	    echo "<tr>";
	    echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(1)')\">MOT Status</th>";
	    echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(2)')\">Droid</th>";
	    echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(3)')\">Owner</th>";
	    echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(4)')\">Owner PLI</th>";
	    echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(5)')\">Primary</th>";
	    echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(6)')\">Type</th>";
	    echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(7)')\">Style</th>";
	    echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(8)')\">RC</th>";
	    echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(9)')\">Tier Two</th>";
	    echo "<th></th></tr>";
    } else {
	    echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(1)')\">Valid MOT</th>";
            echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(2)')\">Droid</th>";
            echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(3)')\">Primary</th>";
            echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(4)')\">Type</th>";
            echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(5)')\">Style</th>";
            echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(6)')\">RC</th>";
            echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(7)')\">Tier Two</th>";
    }
    while($row = $result->fetch_assoc()) {
	# Pull the latest MOT for the droid that is a pass
        $sql = "SELECT * FROM mot WHERE (approved='Yes' OR approved='WIP' OR approved='Advisory') AND droid_uid = " .$row["droid_uid"]. " AND date >= DATE_SUB(NOW(), INTERVAL 1 YEAR) ORDER BY date LIMIT 1";
	$mot_result = $conn->query($sql);
	echo "<tr class=\"item\">";
	if ($mot_result->num_rows > 0) {
	    $mot_details=$mot_result->fetch_assoc();
	    if ($mot_details['approved'] == "Yes") {
	        echo "<td class=droid_list bgcolor=green><a href=mot.php?mot_uid=".$mot_details['mot_uid'].">Valid</a></td>";
	    } elseif ($mot_details['approved'] == "WIP") {
		echo "<td class=droid_list bgcolor=blue><a href=mot.php?mot_uid=".$mot_details['mot_uid'].">WIP</a></td>";
            } else { 
		echo "<td class=droid_list bgcolor=orange><a href=mot.php?mot_uid=".$mot_details['mot_uid'].">Advisory</a></td>";
            }
        } elseif (!$club_config[$row['club_uid']]['options'] & $club_options['MOT']) {
            echo "<td>N/A</td>";
        } else { 
	    echo "<td class=droid_list bgcolor=red>Not Valid</td>";
	}	
	echo "<td class=droid_list>" . $row["name"]. "</td>";
	if ($_REQUEST['member_uid'] == "" ) {
	    $sql = "SELECT * FROM members WHERE member_uid = ".$row['member_uid'];
	    $owner = $conn->query($sql)->fetch_assoc();
	    echo "<td class=droid_list><a href=member.php?member_uid=" . $owner["member_uid"].">".$owner["forename"]. " " . $owner["surname"]. "</a></td>";
	    if (strtotime($owner['pli_date']) > strtotime('-1 year')) {
                echo "<td class=droid_list bgcolor=green>Yes</td>";
            } else {
                echo "<td class=droid_list bgcolor=red>No</td>";
            }
	}
	echo "<td class=droid_list>" . $row["primary_droid"]."</td>";
	echo "<td class=droid_list>" . $row["type"]. "</td>";
	echo "<td class=droid_list>" . $row["style"]. "</td>";
	echo "<td class=droid_list>" . $row["radio_controlled"]. "</td>";
	echo "<td class=droid_list align=center>". $row['tier_two']. "</td>";
	echo "<td class=droid_list><a href=droid.php?droid_uid=". $row["droid_uid"]. "><img src=\"images/view_button.png\"></a></td>";
	echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No Droids";
}
echo "<hr /><a href=new_droid.php?member_uid=". $_REQUEST["member_uid"]. ">Add Droid</a>";


include "includes/footer.php";
?>


