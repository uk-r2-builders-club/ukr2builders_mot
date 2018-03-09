<?

include "includes/session.php";

?>
<html>
 <head>
  <title>UK R2 Builders MOT Database</title>
 </head>

 <body>

<?

include "includes/config.php";

include "includes/menu.php";
// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT * FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    echo "<table>";
    echo "<tr><th>ID</th><th>Username</th><th>email</th><th>Name</th><th>Enabled</th></tr>";
    while($row = $result->fetch_assoc()) {
	    echo "<tr>";
	    echo " <td>".$row['user_uid']."</td>";
	    if ($_SESSION['admin'] == 1) {
		echo " <td><a href=user.php?user_uid=".$row['user_uid'].">".$row['username']."</a></td>";
	    } else {
	        echo " <td>".$row['username']."</td>";
	    }
            echo " <td>".$row['email']."</td>";
            echo " <td>".$row['name']."</td>";
            echo " <td>".$row['enabled']."</td>";
	    echo "</tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}
$conn->close();
?>
<a href=new_user.php>Add new user</a>
