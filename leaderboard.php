<html>
 <head>
  <title>UK R2 Builders MOT Database</title>
  <link rel="stylesheet" href="main.css">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
 </head>

 <body>

<script src="https://www.w3schools.com/lib/w3.js"></script>

<script>
function myFunction() {
  // Declare variables
  var input, filter, table, tr, td, i;
  input = document.getElementById("nameSearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("leaderboard");
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


include "includes/menu.php";
include "includes/config.php";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

echo "<div class=leaderboard>";
$sql = "SELECT * FROM course_runs ORDER BY final_time ASC";
$runs = $conn->query($sql);
if ($runs->num_rows > 0) {
    $pos = 0;
    echo "<input type=\"text\" id=\"nameSearch\" onkeyup=\"myFunction()\" placeholder=\"Search for names..\">";
    echo "<table class=leaderboard id=leaderboard>";
    echo "<tr>";
    echo "<th onclick=\"w3.sortHTML('#leaderboard','.item', 'td:nth-child(1)')\">Run date</th>";
    echo "<th onclick=\"w3.sortHTML('#leaderboard','.item', 'td:nth-child(2)')\">Position</th>";
    echo "<th onclick=\"w3.sortHTML('#leaderboard','.item', 'td:nth-child(3)')\">Member</th>";
    echo "<th onclick=\"w3.sortHTML('#leaderboard','.item', 'td:nth-child(4)')\">Droid</th>";
    echo "<th onclick=\"w3.sortHTML('#leaderboard','.item', 'td:nth-child(5)')\">First Half</th>";
    echo "<th onclick=\"w3.sortHTML('#leaderboard','.item', 'td:nth-child(6)')\">Second Half</th>";
    echo "<th onclick=\"w3.sortHTML('#leaderboard','.item', 'td:nth-child(7)')\">Clock Time</th>";
    echo "<th onclick=\"w3.sortHTML('#leaderboard','.item', 'td:nth-child(8)')\">Penalties</th>";
    echo "<th onclick=\"w3.sortHTML('#leaderboard','.item', 'td:nth-child(9)')\">Final Time</th>";
    echo "<th onclick=\"w3.sortHTML('#leaderboard','.item', 'td:nth-child(10)')\">-</th></tr>";
    while($row = $runs->fetch_assoc()) {
	$pos++;
        echo "<tr class=item>";
        echo "<td>".substr($row['run_timestamp'],0,10)."</td>";
	echo "<td>".$pos."</td>";
	$sql = "SELECT forename, surname FROM members WHERE member_uid = ".$row['member_uid'];
        $member_name = $conn->query($sql)->fetch_assoc();
        $sql = "SELECT name FROM droids WHERE droid_uid = ".$row['droid_uid'];
        $droid_name = $conn->query($sql)->fetch_assoc();
	echo "<td class=leaderboard>".$member_name['forename']." ".$member_name['surname']."</td>";
        echo "<td>".$droid_name['name']."</td>";
	if ($row['first_half'] == 0) {
		echo "<td></td>";
	} else {
                echo "<td>".gmdate("H:i:s", $row['first_half'])."</td>";
	}
	if ($row['second_half'] == 0) {
		echo "<td></td>";
	} else {
                echo "<td>".gmdate("H:i:s", $row['second_half'])."</td>";
	}
        echo "<td>".gmdate("H:i:s", $row['clock_time'])."</td>";
        echo "<td>".$row['num_penalties']."</td>";
        echo "<td>".gmdate("H:i:s", $row['final_time'])."</td>";
	echo "<td>";
	if ($row['dribble'] == "1") {
		echo "<img src=images/soccer-ball.png>";
	}
	echo "</td>";
        echo "</tr>";
    }

} else {
    echo "No runs";
}
echo "</table>";
echo "</div>";

$conn->close();

?>


