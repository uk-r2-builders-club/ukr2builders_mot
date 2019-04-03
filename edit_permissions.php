<?

include "includes/header.php";

if(!($_SESSION['permissions'] & $perms['EDIT_PERMISSIONS'])) {
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

if (isset($_REQUEST['update']) && ($_SESSION['permissions'] & $perms['EDIT_PERMISSIONS'])) {
	$perms = 0;
        for ($i=0 ; $i < $_REQUEST['num_perms'] ; $i++ ) {
             $perm = strval(2**$i);
	     if ($_REQUEST[$perm] == "on") {
		     $perms = $perms + 2**$i;
	     }

	}
	$sql = "UPDATE members SET permissions=".$perms." WHERE member_uid=".$_REQUEST['member_uid'];
	$conn->query($sql);
}

if (isset($_REQUEST['member_uid']) && ($_SESSION['permissions'] & $perms['EDIT_PERMISSIONS'])) {
	$sql = "SELECT member_uid, forename, surname, last_login, last_login_from, permissions FROM members WHERE member_uid = ".$_REQUEST['member_uid'];
	$member = $conn->query($sql)->fetch_assoc();
	echo "<form>";
	echo "<input type=hidden name=member_uid value=".$_REQUEST['member_uid'].">";
	echo "<table>";
	echo "<tr><th>Name:</th><td>".$member['forename']." ".$member['surname']."</td></tr>";
	echo "<tr><th>Last Login:</th><td>".$member['last_login']."(".$member['last_login_from'].")</td>";
	echo "<tr><th>Permission:</th>";
	echo "<td>";
	echo "<table><tr>";
        $sql = "SELECT * FROM permissions";
        $permissions = $conn->query($sql);
        while($row = $permissions->fetch_assoc()) {
            echo "<th class=rotate><div><span>".$row['name']."</span></div></th>";
            $num_perms++;
        }
	echo "</tr><tr>";
	for ($i=0 ; $i < $num_perms ; $i++) {
                    $permission = 2**$i;
                    echo "<td class=permission><input type=checkbox name=$permission ";
                    echo ($member['permissions'] & $permission) ? "checked" : "";
                    echo "></td>";
        }
	echo "</tr></table>";
        echo "</td></tr>";
	echo "</table>";
	echo "<input type=hidden name=num_perms value=".$num_perms.">";
        echo "<input type=submit name=update value=Update>";
	echo "</form>";
}


$sql = "SELECT member_uid, forename, surname, last_login, last_login_from, permissions FROM members WHERE active = 'on' ORDER BY surname";
$result = $conn->query($sql);
$num_perms = 0;

if ($result->num_rows > 0) {
    echo "<input type=\"text\" id=\"nameSearch\" onkeyup=\"myFunction()\" placeholder=\"Search for names..\">";

    // output data of each row
    echo "<table class=members_list id=members_list>";
    echo "<tr>";
    echo "<th border=0 onclick=\"w3.sortHTML('#members_list','.item', 'td:nth-child(1)')\">ID</th>";
    echo "<th border=0 onclick=\"w3.sortHTML('#members_list','.item', 'td:nth-child(2)')\">Name</th>";
    echo "<th border=0 onclick=\"w3.sortHTML('#members_list','.item', 'td:nth-child(3)')\">Last Login</th>";
    $sql = "SELECT * FROM permissions";
    $permissions = $conn->query($sql);
    while($row = $permissions->fetch_assoc()) {
	    echo "<th class=rotate><div><span>".$row['name']."</span></div></th>";
	    $num_perms++;
    }
    echo "</tr>";
    while($row = $result->fetch_assoc()) {
	    echo "<tr class=\"item\">";
            echo " <td class=members_list><a href=?member_uid=".$row['member_uid'].">".$row['member_uid']."</a></td>";
            echo " <td class=members_list><a href=?member_uid=".$row['member_uid'].">".$row['forename']." ".$row['surname']."</a></td>";
	    echo " <td class=members_list>".$row['last_login']."(".$row['last_login_from'].")</td>";
	    for ($i=0 ; $i < $num_perms ; $i++) {
		    $permission = 2**$i;
		    echo "<td class=permission>";
		    echo ($row['permissions'] & $permission) ? "X" : "";
		    echo "</td>";
	    }
	    echo "</tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}

include "includes/footer.php";

?>
