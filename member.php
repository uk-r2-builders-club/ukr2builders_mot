<?

include "session.php";

?>
<html>
 <head>
  <title>UK R2 Builders MOT Database</title>
  <link rel="stylesheet" href="main.css">
 </head>

 <body>

<?

include "menu.php";
include "config.php";

echo "<div id=main>";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if ($_REQUEST['update'] != "") {
    $sql = "UPDATE members SET email=?, pli_date=?, username=? WHERE member_uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $_REQUEST['email'], $_REQUEST['pli_date'], $_REQUEST['username'], $_REQUEST['member_uid']);
    $stmt->execute();
    printf("Error: %s.\n", $stmt->sqlstate);
    printf("Error: %s.\n", $stmt->error);
}

$sql = "SELECT * FROM members WHERE member_uid = ". $_REQUEST['member_uid'];
$result = $conn->query($sql);
$member = $result->fetch_assoc();
$sql = "SELECT name FROM users WHERE user_uid = ".$member["created_by"];
$officer = $conn->query($sql)->fetch_object()->name;


echo "<div id=info>";
echo "<form>";
echo "<input type=hidden name=member_uid value=".$member[member_uid].">";
echo "<h2>". $member['forename'] ." ".$member['surname']."</h2>";
echo "<ul>";
echo " <li>email: <input type=email size=40 name=email value=".$member['email']."></li>";
echo " <li>Forum Username: <input type=text name=username value=".$member['username']."></li>";
echo " <li>Created On: ".$member['create_on']."</li>";
echo " <li>Created By: ".$officer."</li>";
echo " <li>PLI Cover Last Paid: <input type=date name=pli_date value=".$member['pli_date']."></li>";
echo "</ul>";
echo "<input type=submit name=update value=Update>";
echo "</div>";

echo "</div>";

$conn->close();
?>


