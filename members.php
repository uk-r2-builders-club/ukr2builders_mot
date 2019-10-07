<?

include "includes/header.php";

if(!($_SESSION['permissions'] & $perms['VIEW_MEMBERS'])) {
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
  table = document.getElementById("members");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
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

$sql = "SELECT * FROM members WHERE active = 'on'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    echo "<input type=\"text\" id=\"nameSearch\" onkeyup=\"myFunction()\" placeholder=\"Search for names..\">";
    echo "<br />";
    echo "<table class=members id=members>";
    echo "<tr class=members>";
    echo "<th class=members onclick=\"w3.sortHTML('#members','.item', 'td:nth-child(1)')\">ID</th>";
    echo "<th class=members onclick=\"w3.sortHTML('#members','.item', 'td:nth-child(2)')\">Name</th>";
    echo "<th class=members onclick=\"w3.sortHTML('#members','.item', 'td:nth-child(3)')\">email</th>";
    echo "<th class=members onclick=\"w3.sortHTML('#members','.item', 'td:nth-child(5)')\">PLI</th>";
    echo "<th class=members onclick=\"w3.sortHTML('#members','.item', 'td:nth-child(6)')\">Primary MOT</th>";
    echo "<th class=members onclick=\"w3.sortHTML('#members','.item', 'td:nth-child(7)')\">Droids</th>";
    echo "</tr>";
    while($row = $result->fetch_assoc()) {
        $sql = "SELECT * FROM droids WHERE member_uid = " .$row["member_uid"]. " AND active='on'";
	$droids = $conn->query($sql);
        $num_droids = $droids->num_rows;
	echo "<tr class=\"item\">";
	echo "<td class=members>" . $row["member_uid"]. "</td>";
	echo "<td class=members><a href=member.php?member_uid=" . $row["member_uid"].">".$row["forename"]. " " . $row["surname"]. "</a>";
	if (($row['permissions'] != 0 ) && ($_SESSION['permissions'] & $perms['EDIT_PERMISSIONS'])) echo "*";
	echo "</td>";
	echo "<td class=members>" . $row["email"]. "</td>";
	# Display PLI information and colour code to expiry date
	if (strtotime($row[pli_date]) > strtotime('-11 months')) {
	    echo "<td class=members bgcolor=green>".$row[pli_date]."</td>";
	} elseif ((strtotime($row[pli_date]) < strtotime('-11 months')) && (strtotime($row[pli_date]) > strtotime('-1 year'))) {
	    echo "<td class=members bgcolor=orange>".$row[pli_date]."</td>";
	} else {
	    echo "<td class=members bgcolor=red>".$row[pli_date]."</td>";
	}
	# Display valid MOT information and colour code to expiry date
        $date = "No Valid MOT";
        $advisory = "";
        $color = "red";
	while ($droid = $droids->fetch_object()) {
		if ($droid->primary_droid == "Yes") {
			$sql = "SELECT * FROM mot WHERE (approved='Yes' OR approved='WIP' OR approved='Advisory') AND droid_uid = " .$droid->droid_uid. " AND date >= DATE_SUB(NOW(), INTERVAL 1 YEAR) ORDER BY date DESC LIMIT 1";
			$mot = $conn->query($sql)->fetch_object();
		        if (strtotime($mot->date) > strtotime('-11 months')) {
			    $color = "green";
			    $date = $mot->date;
                        } elseif ((strtotime($mot->date) < strtotime('-11 months')) && (strtotime($mot->date) > strtotime('-1 year'))) {
			    $color = "orange";
			    $date = $mot->date;
                        } else {
			    $color = "red";
                        }
		}
		$sql = "SELECT * FROM mot WHERE approved='Advisory' AND droid_uid = " .$droid->droid_uid. " AND date >= DATE_SUB(NOW(), INTERVAL 1 YEAR) ORDER BY date";
		$advisories = $conn->query($sql)->num_rows;
		if ($advisories == 1) {
			$advisory = "($advisories Advisory)";
			$color = "orange";
		} elseif ($advisories > 1) {
			$advisory = "($advisories Advisories)";
			$color = "orange";
		}
	}
	if ($num_droids == 0) {
		echo "<td class=members>No Droids</td>";
	} else {
		echo "<td bgcolor=$color>$date $advisory</td>";
	}
	echo "<td class=members align=center><a href=list_droids.php?member_uid=" .$row["member_uid"]. ">";
	if ($num_droids > 0) {
	     if ($num_droids == 1) {
		 echo "1 Droid";
	     } else {
	         echo $num_droids." Droids";
	     }
	} else {
             echo "No Droids";
	}
	echo "</a></td></tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}
echo "<a href=new_member.php>Add new member</a>";

echo "<hr>";
echo "<h2>Inactive Members</h2>";

$sql = "SELECT * FROM members WHERE active = ''";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	echo "<br />";
	echo "<table class=members id=members>";
	echo "<tr class=members>";
	echo "<th class=members>ID</th>";
	echo "<th class=members>Name</th>";
	echo "<th class=members>email</th>";
	echo "<th class=members>Droids</th>";
	echo "</tr>";
	while($row = $result->fetch_assoc()) {
	        $sql = "SELECT * FROM droids WHERE member_uid = " .$row["member_uid"]. " AND active='on'";
        	$droids = $conn->query($sql);
        	$num_droids = $droids->num_rows;
        	echo "<tr class=\"item\">";
        	echo "<td class=members>" . $row["member_uid"]. "</td>";
        	echo "<td class=members><a href=member.php?member_uid=" . $row["member_uid"].">".$row["forename"]. " " . $row["surname"]. "</a></td>";
        	echo "<td class=members>" . $row["email"]. "</td>";
        	echo "<td class=members align=center><a href=list_droids.php?member_uid=" .$row["member_uid"]. ">";
        	if ($num_droids > 0) {
             		if ($num_droids == 1) {
                 		echo "1 Droid";
             		} else {
                 		echo $num_droids." Droids";
             		}
        	} else {
	             	echo "No Droids";
        	}
        	echo "</a></td></tr>";
	}
	echo "</table>";
} else {
	echo "No inactive members";
}

include "includes/footer.php";
?>

