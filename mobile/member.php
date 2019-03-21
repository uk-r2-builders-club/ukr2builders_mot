<?

include "../includes/config.php";
include "session.php";
include "header.php";


if ($_SESSION['role'] == "user") {
        $_REQUEST['member_uid'] = $_SESSION['user'];
}

echo "<div id=main>";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT * FROM members WHERE member_uid = ". $_REQUEST['member_uid'];
$result = $conn->query($sql);
$member = $result->fetch_assoc();
$sql = "SELECT forename,surname FROM members WHERE member_uid = ".$member["created_by"];
$officer = $conn->query($sql)->fetch_object();
$officer_name = $officer->forename." ".$officer->surname;

# Member details
echo "<div class=info>";
echo "<h2>". $member['forename'] ." ".$member['surname']."</h2>";
echo "<table class=member>";
echo " <tr><td>email: </td><td>".$member['email']."</td></tr>";
echo " <tr><td>County: </td><td>".$member['county']."</td></tr>";
echo " <tr><td>Postcode: </td><td>".$member['postcode']."</td></tr>";
echo " <tr><td>Forum Username: </td><td>".$member['username']."</td></tr>";
echo " <tr><td>Created On: </td><td>".$member['created_on']."</td></tr>";
echo " <tr><td>Created By: </td><td>".$officer_name."</td></tr>";
echo " <tr><td>PLI Cover Last Paid: </td><td>".$member['pli_date'];
if (strtotime($member['pli_date']) > strtotime('-1 year')) {
	echo "<a target=_blank href=cover_note.php?member_uid=".$member['member_uid'].">Cover Note</a>";
}
echo "</td></tr>";
echo " <tr><td>Last Updated: </td><td>".$member['last_updated']."</td></tr>";
echo "</table>";
echo "</div>";

echo "<div class=mug_shot>";
if ($member['mug_shot'] != "") {
	echo "<div class=\"mug_shot\"><img id=mug_shot src=data:image/jpeg;base64,".base64_encode( $member['mug_shot'] )." width=240>";
        if ($_SESSION['role'] == "admin") {
                echo "<a href=\"member.php?delete_mug=1&member_uid=".$member['member_uid']."\">Delete</a>";
        }
	echo "</div>";
}

echo "</div>";

# Droid list
echo "<div class=droid_list>";

$sql = "SELECT * FROM droids WHERE member_uid = ". $_REQUEST['member_uid'];
$droids = $conn->query($sql);

if ($droids->num_rows > 0) {
    // output data of each row
    echo "<table class=droid_list id=droid_list>";
    echo "<tr><th colspan=7>Droid info</th></tr>";
    echo "<tr>";
    echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(1)')\">Valid MOT</th>";
    echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(2)')\">Droid</th>";
    echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(3)')\">Primary</th>";
    echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(4)')\">Type</th>";
    echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(5)')\">Style</th>";
    echo "<th onclick=\"w3.sortHTML('#droid_list','.item', 'td:nth-child(8)')\">Tier Two</th>";
    echo "<th></th>";
    echo "</tr>";
    while($row = $droids->fetch_assoc()) {
	# Pull the latest MOT for the droid that is a pass
        $sql = "SELECT * FROM mot WHERE (approved='Yes' OR approved='WIP' OR approved='Advisory') AND droid_uid = " .$row["droid_uid"]. " AND date >= DATE_SUB(NOW(), INTERVAL 1 YEAR) ORDER BY date DESC LIMIT 1";
	$mot_result = $conn->query($sql);
	echo "<tr class=\"item\">";
	if ($mot_result->num_rows > 0) {
	    $mot_details=$mot_result->fetch_object();
	    if ($mot_details->approved == "Yes") {
	        echo "<td bgcolor=green><a href=mot.php?mot_uid=".$mot_details->mot_uid.">Valid (".$mot_details->date.")</a></td>";
	    } elseif ($mot_details->approved == "WIP") {
		echo "<td bgcolor=blue><a href=mot.php?mot_uid=".$mot_details->mot_uid.">WIP (".$mot_details->date.")</a></td>";
            } else {
		echo "<td bgcolor=orange><a href=mot.php?mot_uid=".$mot_details->mot_uid.">Advisory (".$mot_details->date.")</a></td>";
            }
        } else {
	    echo "<td bgcolor=red>Not Valid</td>";
	}
	echo "<td>" . $row["name"]. "</td>";
	echo "<td>" . $row["primary_droid"]."</td>";
	echo "<td>" . $row["type"]. "</td>";
	echo "<td>" . $row["style"]. "</td>";
	echo "<td align=center>". $row['tier_two']. "</td>";
	echo "<td><a href=droid.php?droid_uid=". $row["droid_uid"]. ">View Droid</a></td>";
	echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No Droids";
}
echo "</div>";
echo "<hr />";

# Achievements
echo "<h4>Achievements</h4>";
echo "<div class=achievements_list>";
$sql = "SELECT * FROM members_achievements WHERE member_uid=".$member['member_uid'];
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<table class=achievements_list id=achievements_list>";
    echo "<tr>";
    echo "<th onclick=\"w3.sortHTML('#achievements_list','.item', 'td:nth-child(1)')\">Name</th>";
    echo "<th onclick=\"w3.sortHTML('#achievements_list','.item', 'td:nth-child(2)')\">Notes</th>";
    echo "<th onclick=\"w3.sortHTML('#achievements_list','.item', 'td:nth-child(3)')\">Date Added</th>";
    echo "<th onclick=\"w3.sortHTML('#achievements_list','.item', 'td:nth-child(4)')\"></th></tr>";
    while($row = $result->fetch_assoc()) {
	$sql = "SELECT * FROM achievements WHERE achievement_uid=".$row['achievement_uid'];
	$achievement = $conn->query($sql)->fetch_assoc();
	echo "<td class=achievements_list><a href='#'>".$achievement['name']."<div class='tooltipcontainer'>";
	echo "<div class='tooltip'>".$achievement['description']."</div>";
	echo "</td>";
	echo "<td class=achievements_list>".$row['notes']."</td>";
	echo "<td class=achievements_list>".$row['date_added']."</td>";
	echo "<td class=achievements_list>";
        if ($achievement['icon'] != "") {
            echo "<img id=icon src=data:image/jpeg;base64,".base64_encode( $achievement['icon'] )." width=40>";
        } else {
            echo "";
        }
	echo "</td>";
	echo "</tr>";
    }

} else {
    echo "No achievements";
}
echo "</table>";
$sql="SELECT * FROM achievements";
$result=$conn->query($sql);

echo "</div>";
echo "<hr />";
# End of Achievements


# Official Events
echo "<h4>Official Events</h4>";
echo "<div class=events_list>";
$sql = "SELECT * FROM members_official_events WHERE member_uid=".$member['member_uid'];
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<table class=events_list id=events_list>";
    echo "<tr>";
    echo "<th onclick=\"w3.sortHTML('#events_list','.item', 'td:nth-child(1)')\">Date Added</th>";
    echo "<th onclick=\"w3.sortHTML('#events_list','.item', 'td:nth-child(2)')\">Details</th>";
    echo "<th onclick=\"w3.sortHTML('#events_list','.item', 'td:nth-child(3)')\">Spotter</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<td class=events_list>".$row['date_added']."</td>";
        echo "<td class=events_list>".$row['details']."</td>";
	echo "<td class=events_list>";
	if ($row['spotter'] == "on") 
		echo "Yes";
	echo "</td>";
        echo "</tr>";
    }

} else {
    echo "No events";
}
echo "</table>";

echo "</div>";
echo "<hr />";
# End of Official Events


# Course runs
echo "<h4>Driving Course Runs</h4>";
echo "<div class=course_list>";
$sql = "SELECT * FROM course_runs WHERE member_uid=".$member['member_uid']." ORDER BY final_time ASC";
$runs = $conn->query($sql);
if ($runs->num_rows > 0) {
    echo "<table class=course_list id=course_list>";
    echo "<tr>";
    echo "<th onclick=\"w3.sortHTML('#course_list','.item', 'td:nth-child(1)')\">Run date</th>";
    echo "<th onclick=\"w3.sortHTML('#course_list','.item', 'td:nth-child(2)')\">Droid</th>";
    echo "<th onclick=\"w3.sortHTML('#course_list','.item', 'td:nth-child(3)')\">First Half</th>";
    echo "<th onclick=\"w3.sortHTML('#course_list','.item', 'td:nth-child(4)')\">Second Half</th>";
    echo "<th onclick=\"w3.sortHTML('#course_list','.item', 'td:nth-child(5)')\">Clock Time</th>";
    echo "<th onclick=\"w3.sortHTML('#course_list','.item', 'td:nth-child(6)')\">Penalties</th>";
    echo "<th onclick=\"w3.sortHTML('#course_list','.item', 'td:nth-child(7)')\">Final Time</th></tr>";
    while($row = $runs->fetch_assoc()) {
	echo "<tr>";
        echo "<td class=course_list>".$row['run_timestamp']."</td>";
	$sql = "SELECT name FROM droids WHERE droid_uid = ".$row['droid_uid'];
	$droid_name = $conn->query($sql)->fetch_assoc();
        echo "<td class=course_list>".$droid_name['name']."</td>";
	echo "<td class=course_list>".$row['first_half']."</td>";
	echo "<td class=course_list>".$row['second_half']."</td>";
	echo "<td class=course_list>".$row['clock_time']."</td>";
	echo "<td class=course_list>".$row['num_penalties']."</td>";
	echo "<td class=course_list>".$row['final_time']."</td>";
        echo "</tr>";
    }

} else {
    echo "No runs";
}
echo "</table>";
echo "</div>";
echo "<hr />";
# End of Course Runs



echo "</div>";

$conn->close();
?>


