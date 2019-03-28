<?

include "includes/header.php";

if($_SESSION['role'] == "user") {
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
  table = document.getElementById("events_list");
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

if (($_REQUEST['submit'] == "Add") && ($_SESSION['role'] != "user") ) {
    $sql = "INSERT into events(name, description, date) VALUES('".$_REQUEST['newname']."', '".addslashes($_REQUEST['newdescription'])."', '".$_REQUEST['newdate']."')";
    echo $sql;
    $result=$conn->query($sql);
}

if (($_REQUEST['update'] == "Update") && ($_SESSION['role'] != "user") ) {
    $sql = "UPDATE events SET name=?, description=?, date=?, charity_raised=? WHERE event_uid=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $_REQUEST['name'], $_REQUEST['description'], $_REQUEST['date'], $_REQUEST['charity_raised'], $_REQUEST['event_uid']);
    $stmt->execute();
    printf("Error: %s.\n", $stmt->sqlstate);
    printf("Error: %s.\n", $stmt->error);
}

if (isset($_REQUEST['event_uid'])) {
    $sql = "SELECT * FROM events WHERE event_uid=".$_REQUEST['event_uid'];
    $event=$conn->query($sql)->fetch_assoc();
    echo "<form>";
    echo "<input type=hidden name=event_uid value=".$event['event_uid'].">";
    echo "<table>";
    echo "<tr><td>Name</td><td><input type=text size=50 name=name value=\"".$event['name']."\"></td></tr>";
    echo "<tr><td>Description</td><td><input type=text size=50 name=description value=\"".$event['description']."\"></td></tr>";
    echo "<tr><td>Date Created</td><td><input type=text size=50 name=date value=\"".$event['date']."\"></td></tr>";
    echo "<tr><td>Charity Raised</td><td>£<input type=text size=50 name=charity_raised value=\"".$event['charity_raised']."\"></td></tr>";
    echo "</table>";
    echo "<input type=submit name=update value=Update>";
    echo "<br />";
}


$sql = "SELECT * FROM events ORDER BY date";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    if ($_REQUEST['events_uid'] == "" ) {
            echo "<input type=\"text\" id=\"nameSearch\" onkeyup=\"myFunction()\" placeholder=\"Search for names..\">";
    }

    // output data of each row
    echo "<table class=events_list id=events_list>";
    echo "<tr>";
    echo "<th onclick=\"w3.sortHTML('#events_list','.item', 'td:nth-child(1)')\">ID</th>";
    echo "<th onclick=\"w3.sortHTML('#events_list','.item', 'td:nth-child(2)')\">Name</th>";
    echo "<th onclick=\"w3.sortHTML('#events_list','.item', 'td:nth-child(3)')\">Description</th>";
    echo "<th onclick=\"w3.sortHTML('#events_list','.item', 'td:nth-child(4)')\">Date</th>";
    echo "<th onclick=\"w3.sortHTML('#events_list','.item', 'td:nth-child(5)')\">Charity Raised</th></tr>";
    while($row = $result->fetch_assoc()) {
	    echo "<tr class=\"item\">";
	    echo " <td class=events_list>".$row['event_uid']."</td>";
            echo " <td class=events_list><a href=events.php?event_uid=".$row['event_uid'].">".$row['name']."</a></td>";
            echo " <td class=events_list>".$row['description']."</td>";
            echo " <td class=events_list>".$row['date']."</td>";
	    echo " <td class=events_list>£".$row['charity_raised']."</td>";
	    echo "</tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}

echo "<form>";
echo "Name: <input type=text name=newname size=20> Description: <input type=text name=newdescription size=60> Date: <input type=date name=newdate><br />";
echo "<input type=submit name=submit value=Add>";
echo "</form>";

$conn->close();
?>
<a href=events.php?new>Add new event</a>

