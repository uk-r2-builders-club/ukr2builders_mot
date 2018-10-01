<html>
 <head>
  <title>UK R2 Builders MOT Database</title>
  <link rel="stylesheet" href="main.css">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
 </head>

 <body>

<?

include "includes/menu.php";
include "includes/config.php";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

echo "<center>";
echo "<table class=stats style=\"width: 80%; height: 80%;\">";
echo "<tr class=stats style=\"height: 15%; font-size: 110px;\">";
echo " <th>Cleared Operators</th>";
echo " <th>Cleared Droids</th>";
echo "</tr>";
$sql = "SELECT count(*) as total FROM members where pli_date > DATE(DATE_SUB(NOW(), INTERVAL 1 YEAR))";
$members = $conn->query($sql)->fetch_object()->total;
$sql = "SELECT count(*) as total FROM mot where date > DATE(DATE_SUB(NOW(), INTERVAL 1 YEAR)) AND (approved = \"Yes\" OR approved = \"Advisory\")";
$droids = $conn->query($sql)->fetch_object()->total;

echo "<tr class=stats style=\"height: 85%; font-size: 340px;\"><td align=center>$members</td><td align=center>$droids</td></tr>";
echo "</table>";
echo "</center>";


$conn->close();

?>


