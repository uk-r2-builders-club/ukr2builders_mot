<?

include "includes/header.php";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

function imageUpload($box) {
	echo "<form method=POST enctype=\"multipart/form-data\">";
	echo "<input type=hidden name=droid_uid value=".$_REQUEST['droid_uid'].">";
	echo "<input type=file name=$box>";
	echo "<input type=submit name=upload value=$box>";
	echo "</form>";
}

# Image uploads
if ($_REQUEST['upload'] != "") {
        $imagename=$_FILES[$_REQUEST['upload']]["name"];
	$imagetype=$_FILES[$_REQUEST['upload']]["type"];
	echo "Image Type: $imagetype <br />";
	$exif = exif_read_data($_FILES[$_REQUEST['upload']]['tmp_name']);
	if( isset($exif['Orientation']) )
            $orientation = $exif['Orientation'];
        elseif( isset($exif['IFD0']['Orientation']) )
            $orientation = $exif['IFD0']['Orientation'];
        else
            $orientation = 0;
	echo "Orientation: $orientation<br/>";
	if ($imagetype != "image/jpeg") {
		$img = imagecreatefromstring(file_get_contents($_FILES[$_REQUEST['upload']]["tmp_name"]));
	} else {
		$img = imagecreatefromjpeg($_FILES[$_REQUEST['upload']]['tmp_name']);
	}
	switch($orientation)
	{
            case 3: // 180 rotate left
            $newimg = imagerotate($img, 180, -1);
            break;

            case 6: // 90 rotate right
            $newimg = imagerotate($img, -90, -1);
            break;

            case 8:    // 90 rotate left
            $newimg = imagerotate($img, 90, -1);
            break;

            case 0:    // 90 rotate left
            $newimg = imagerotate($img, 0, 0);
            break;

	    default:
	    $newimg = $img;
	    break;
        }
	ob_start();
	imagejpeg($newimg);
	$contents = ob_get_contents();
	ob_end_clean();
	$insert_image="UPDATE droids SET ".$_REQUEST['upload']."='".addslashes($contents)."' WHERE droid_uid=".$_REQUEST['droid_uid'];
	$result=$conn->query($insert_image);

}

if (($_REQUEST['delete_comment'] == "yes") && ($_SESSION['admin'] == 1)) {
    echo "Deleting comment";
    $sql = "DELETE from droid_comments WHERE uid=".$_REQUEST['uid'];
    $result = $conn->query($sql);
}

if (($_REQUEST['delete_image'] != "") && ($_SESSION['admin'] == 1)) {
    echo "Deleting image";
    $sql = "UPDATE droids SET ".$_REQUEST['delete_image']."='' WHERE droid_uid=".$_REQUEST['droid_uid'];
    $result = $conn->query($sql);
}

if ($_REQUEST['new_comment'] != "") {
    $sql = "INSERT INTO droid_comments(droid_uid, comment, added_by) VALUES (?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $_REQUEST['droid_uid'], $_REQUEST['new_comment'], $_REQUEST['officer']);
    $stmt->execute();
    printf("Error: %s.\n", $stmt->sqlstate);
    printf("Error: %s.\n", $stmt->error);

    $stmt->close();
}

if ($_REQUEST['update'] != "") {
    # $sql = "INSERT INTO droid_comments(droid_uid, comment, added_by) VALUES (?,?,?)";
    $sql = "UPDATE droids SET primary_droid=?, style=?, radio_controlled=?, transmitter_type=?, material=?, weight=?, battery=?, drive_voltage=?, sound_system=?, value=?, tier_two=?, topps_id=? WHERE droid_uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssii", $_REQUEST['primary_droid'], $_REQUEST['style'], $_REQUEST['radio_controlled'], $_REQUEST['transmitter_type'], $_REQUEST['material'], $_REQUEST['weight'], 
	    $_REQUEST['battery'], $_REQUEST['drive_voltage'], $_REQUEST['sound_system'], $_REQUEST['value'], $_REQUEST['tier_two'], $_REQUEST['topps_id'], $_REQUEST['droid_uid']);
    $stmt->execute();
    printf("Error: %s.\n", $stmt->sqlstate);
    printf("Error: %s.\n", $stmt->error);

    $stmt->close();
}

echo "<div class=main>";

echo "<div class=droid_column_left>";

$sql = "SELECT * FROM droids WHERE droid_uid = ". $_REQUEST['droid_uid'];
$droid = $conn->query($sql)->fetch_assoc();
$sql = "SELECT * FROM members WHERE member_uid=".$droid['member_uid'];
$member = $conn->query($sql)->fetch_object();

echo "<div class=info>";
echo "<form>";
echo "<h2>". $droid['name'] ."</h2>";
echo "<table style=droid>";
echo "<input type=hidden name=droid_uid value=".$_REQUEST['droid_uid'].">";
echo " <tr><td>Owner: </td><td><a href=member.php?member_uid=$member->member_uid>$member->forename $member->surname</a></td></tr>";
echo " <tr><td>Primary Droid: </td><td><select name=primary_droid>";
if ($droid['primary_droid'] == No) {
        echo "<option value=Yes>Yes</option><option value=No selected>No</option>";
} else {
        echo "<option value=Yes selected>Yes</option><option value=No>No</option>";
}
echo "</select></td></tr>";
echo " <tr><td>Type:</td><td> ".$droid['type']."</td></tr>";
echo " <tr><td>Style: </td><td><input type=text name=style size=50 value=\"".$droid['style']."\"></td></tr>";
echo " <tr><td>Radio Controlled?: </td><td><select name=radio_controlled>";
if ($droid['radio_controlled'] == Yes) {
        echo "<option value=Yes selected>Yes</option><option value=No>No</option>";
} else {
        echo "<option value=Yes>Yes</option><option value=No selected>No</option>";
}
echo "</select></td></tr>";
echo " <tr><td>Transmitter Type: </td><td><input type=text name=transmitter_type size=50 value=\"".$droid['transmitter_type']."\"></td></tr>";
echo " <tr><td>Material: </td><td><input type=text name=material size=50 value=\"".$droid['material']."\"></td></tr>";
echo " <tr><td>Approx Weight: </td><td><input type=text name=weight size=50 value=\"".$droid['weight']."\"></td></tr>";
echo " <tr><td>Battery Type: </td><td><input type=text name=battery size=50 value=\"".$droid['battery']."\"></td></tr>";
echo " <tr><td>Drive Voltage: </td><td><input type=text name=drive_voltage size=50 value=\"".$droid['drive_voltage']."\"></td></tr>";
echo " <tr><td>Sound System: </td><td><input type=text name=sound_system size=50 value=\"".$droid['sound_system']."\"></td></tr>";
echo " <tr><td>Approx Value: </td><td><input type=text name=value size=50 value=\"".$droid['value']."\"></td></tr>";
echo " <tr><td>Tier 2 Approved: </td><td><select name=tier_two>";
if ($droid['tier_two'] == Yes) {
	echo "<option value=Yes selected>Yes</option><option value=No>No</option>";
} else {
	echo "<option value=Yes>Yes</option><option value=No selected>No</option>";
}
echo "</select></td></tr>";
echo " <tr><td>Topps Number: </td><td><input type=text name=topps_id size=50 value=\"".$droid['topps_id']."\"></td></tr>";
echo " <tr><td>Last Updated: </td><td>".$droid['last_updated']."</td></tr>";
echo "</table>";
echo "<input type=submit value=Update name=update>";
echo "</form>";
echo "</div>";

$sql = "SELECT * FROM mot WHERE droid_uid = " .$_REQUEST["droid_uid"] ." ORDER BY date DESC";
$mot_result = $conn->query($sql);

echo "<div class=mot>";
if ($mot_result->num_rows > 0) {
    // output data of each row
    echo "<table>";
    echo "<tr><th>Date</th><th>Location</th><th>Officer</th><th>Approved</th><th></th></tr>";
    while($row = $mot_result->fetch_assoc()) {
	$sql = "SELECT name FROM users WHERE user_uid = ".$row["user"];
	$officer = $conn->query($sql)->fetch_object()->name;
	if ((strtotime($row['date']) > time()-28930000) && ($row['approved'] == "Yes")) {
            echo "<tr bgcolor=green>";
        } elseif ((strtotime($row['date']) > time()-28930000) && ($row['approved'] == "Advisory")) {
	    echo "<tr bgcolor=orange>";
	} else {
            echo "<tr bgcolor=red>";
        }
	echo "<td>" . $row["date"]. "</td><td>" . $row["location"]. "</td><td>" . $officer. "</td><td>".$row["approved"]."</td>";
	echo "<td><a href=mot.php?mot_uid=". $row["mot_uid"]. "><img src=\"images/view_button.png\"></a>";
	if ($_SESSION['admin'] == 1) {
             echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=mot.php?mot_uid=". $row["mot_uid"]. "&delete=yes><img src=\"images/delete_button.png\"></a>";
        }
	echo "</td>";
	echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No MOT<br />";
}
echo "<a href=new_mot.php?droid_uid=".$_REQUEST['droid_uid'].">Add new MOT</a>";

echo "</div>";

# Comments

$sql = "SELECT * FROM droid_comments WHERE droid_uid = " .$_REQUEST["droid_uid"] ." ORDER BY added_on";
$comments_result = $conn->query($sql);

echo "<div class=comments>";
if ($comments_result->num_rows > 0) {
    // output data of each row
    echo "<div id=comment>";
    while($row = $comments_result->fetch_assoc()) {
        $sql = "SELECT name FROM users WHERE user_uid = ".$row["added_by"];
        $officer = $conn->query($sql)->fetch_object()->name;
        echo "<div id=officer>$officer</div>";
        echo "<div id=time>".$row['added_on']."</div>";
        echo "<div id=text>".$row['comment'];
	if ($_SESSION['admin'] == 1) {
		echo "<br/> <a href=droid.php?droid_uid=".$row['droid_uid']."&uid=".$row['uid']."&delete_comment=yes>Delete comment</a>";
	} 
	echo "</div>";
    }
    echo "</div>";
} else {
    echo "No Comments";
}

echo "<form>";
echo "<textarea name=new_comment>New comment</textarea>";
echo "<input type=hidden name=droid_uid value=".$_REQUEST['droid_uid'].">";
echo "<input type=hidden name=officer value=".$_SESSION['user']."><br />";
echo "<input type=submit value=Add>";
echo "</form>";
echo "</div>";

echo "</div>";


echo "<div class=\"droid_column_right\">";

echo "<table>";
echo "<tr>\n";
echo "<td>";
if ($droid['photo_front'] != "") {
	echo "<div id=image_front class=\"droid_image w3-cell\"><img id=photo_front src=data:image/jpeg;base64,".base64_encode( $droid['photo_front'] )." width=240>";
	echo "<a href=\"droid.php?delete_image=photo_front&droid_uid=".$droid['droid_uid']."\">Delete</a></div>";
} else {
	echo "<div id=image_front class=\"droid_image w3-cell image_upload\">";
	imageUpload('photo_front');
	echo "</div>";
}

echo "</td><td>";
if ($droid['photo_side'] != "") {
	echo "<div id=image_side class=\"droid_image w3-cell\"><img id=photo_side src=data:image/jpeg;base64,".base64_encode( $droid['photo_side'] )." width=240>";
	echo "<a href=\"droid.php?delete_image=photo_side&droid_uid=".$droid['droid_uid']."\">Delete</a></div>";
} else {
	echo "<div id=image_side class=\"droid_image w3-cell image_upload\">";
	imageUpload('photo_side');
        echo "</div>";
}

echo "</td><td>";
if ($droid['photo_rear'] != "") {
	echo "<div id=image_rear class=\"droid_image w3-cell\"><img id=photo_rear src=data:image/jpeg;base64,".base64_encode( $droid['photo_rear'] )." width=240>";
	echo "<a href=\"droid.php?delete_image=photo_rear&droid_uid=".$droid['droid_uid']."\">Delete</a></div>";
} else {
	echo "<div id=image_rear class=\"droid_image w3-cell image_upload\">";
	imageUpload('photo_rear');
        echo "</div>";
}
echo "</td></tr></table>";



if ($droid['topps_id'] != "0") {
	echo "<div class=topps>";
	echo "<table><tr><td>";
	if ($droid['topps_front'] != "") {
        	echo "<div id=topps_front class=droid_image><img id=topps_front src=data:image/jpeg;base64,".base64_encode( $droid['topps_front'] )." width=240>";
		echo "<a href=\"droid.php?delete_image=topps_front&droid_uid=".$droid['droid_uid']."\">Delete</a></div>";
	} else {
	        echo "<div id=topps_front class=image_upload>";
	        imageUpload('topps_front');
	        echo "</div>";
	}
	echo "</td><td>";
	if ($droid['topps_rear'] != "") {
	        echo "<div id=topps_rear class=droid_image><img id=topps_rear src=data:image/jpeg;base64,".base64_encode( $droid['topps_rear'] )." width=240>";
		echo "<a href=\"droid.php?delete_image=topps_rear&droid_uid=".$droid['droid_uid']."\">Delete</a></div>";
	} else {
        	echo "<div id=image_side class=image_upload>";
        	imageUpload('topps_rear');
        	echo "</div>";
	}
	echo "</td></tr></table>";
	echo "</div>";
}
echo "</div>"; # column right


echo "</div>"; # main

$conn->close();
?>
