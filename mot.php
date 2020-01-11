<?

include "includes/header.php";

echo "<div id=wrapper>";
echo "<div id=main>";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if ($_REQUEST['new_comment'] != "" && ($_SESSION['permissions'] & $perms['ADD_MOT'])) {
    $sql = "INSERT INTO mot_comments(mot_uid, comment, added_by) VALUES (?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $_REQUEST['mot_uid'], $_REQUEST['new_comment'], $_REQUEST['officer']);
    $stmt->execute();
    printf("Error: %s.\n", $stmt->sqlstate);
    printf("Error: %s.\n", $stmt->error);

    $stmt->close();
}

if (($_REQUEST['delete'] == "yes") && ($_SESSION['permissions'] & $perms['ADD_MOT'])) {
	echo "Deleting MOT record";
	$sql = "DELETE FROM mot WHERE mot_uid=".$_REQUEST['mot_uid'];
	$result = $conn->query($sql);
	$sql = "DELETE FROM mot_comments where mot_uid=".$_REQUEST['mot_uid'];
	$result = $conn->query($sql);
	$sql = "DELETE FROM mot_details where mot_uid=".$_REQUEST['mot_uid'];
        $result = $conn->query($sql);

}

# Comments

$sql = "SELECT * FROM mot_comments WHERE mot_uid = " .$_REQUEST["mot_uid"] ." ORDER BY added_on";
$comments_result = $conn->query($sql);

echo "<div class=comments>";
if ($comments_result->num_rows > 0) {
    // output data of each row
    echo "<div id=comment>";
    while($row = $comments_result->fetch_assoc()) {
        $sql = "SELECT forename,surname FROM members WHERE member_uid = ".$row["added_by"];
        $officer = $conn->query($sql)->fetch_object();
        $officer_name = $officer->forename." ".$officer->surname;
        echo "<div id=officer>$officer_name</div>";
        echo "<div id=time>".$row['added_on']."</div>";
        echo "<div id=text>".$row['comment']."</div>";
    }
    echo "</div>";
} else {
    echo "No Comments";
}

echo "<form>";
echo "<textarea name=new_comment>New comment</textarea>";
echo "<input type=hidden name=mot_uid value=".$_REQUEST['mot_uid'].">";
echo "<input type=hidden name=officer value=".$_SESSION['user']."><br />";
echo "<input type=submit value=Add>";
echo "</div>";



$sql = "SELECT * FROM mot WHERE mot_uid = ". $_REQUEST['mot_uid'];
$result = $conn->query($sql);
$mot = $result->fetch_assoc();
$sql = "SELECT mot_test,mot_test_result FROM mot_details WHERE mot_uid = ". $_REQUEST['mot_uid'];
$mot_details = $conn->query($sql);
$sql = "SELECT forename,surname FROM members WHERE member_uid = ".$mot["user"];
$officer = $conn->query($sql)->fetch_object();
$officer_name = $officer->forename." ".$officer->surname;
$sql = "SELECT * FROM droids WHERE droid_uid = ".$mot["droid_uid"];
$droid = $conn->query($sql)->fetch_object();
$sql = "SELECT * FROM members WHERE member_uid = $droid->member_uid";
$member = $conn->query($sql)->fetch_object();
$sql = "SELECT * FROM mot_sections WHERE club_uid = $droid->club_uid";
$sections = $conn->query($sql);


echo "<div id=info>";
echo "<ul>";
echo " <li>Owner: <a href=member.php?member_uid=$member->member_uid>$member->forename $member->surname</a></li>";
echo " <li>Droid: <a href=droid.php?droid_uid=".$mot["droid_uid"].">$droid->name</a></li>";
echo " <li>Date Taken: ".$mot['date']."</li>";
echo " <li>Location: ".$mot['location']."</li>";
echo " <li>MOT type: ".$mot['mot_type']."</li>";
echo " <li>Pass/Fail: ".$mot['approved']."</li>";
echo " <li>MOT Officer: ".$officer_name."</li>";
echo "</ul>";
echo "</div>";

echo "</div>";

echo "<div id=mot_test>";

$details = array();
while($detail = $mot_details->fetch_assoc()) {
	$details[$detail['mot_test']] = $detail['mot_test_result'];
}
# Blocks
if ($sections->num_rows > 0) {
	while($row = $sections->fetch_object()) {
		echo "<div id=mot_block>";
		echo "<h3>".$row->section_description."</h3>";
		echo "<table class=mot_table><tr><th>Test</th><th width=75px>Pass/Fail</th></tr>";
	        $sql = "SELECT * FROM mot_lines WHERE club_uid = ".$droid->club_uid." AND test_section = '".$row->section_name."'";	
		$lines = $conn->query($sql);
		while ($line = $lines->fetch_object()) {
			echo "<tr>";
			echo "<td>";
			echo $line->test_description;
			echo "</td>";
			echo "<td>";
        		if ($details[$line->test_name] == "Pass") {
				echo "<p class=mot_pass>Pass</p>";
			} elseif ($details[$line->test_name] == "NA") {
				echo "<p class=mot_na>NA</p>";
			} else {
				echo "<p class=mot_fail>Fail</p>";
			}
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "</div>";
	}
} else {
	echo "No sections defined for this club";
}

echo "</div>";

echo "</div>";

include "includes/footer.php";
?>

