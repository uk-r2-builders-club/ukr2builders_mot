<?

include "includes/header.php";
include "includes/image_upload.php";

// Create connection
$conn = new mysqli($database_host, $database_user, $database_pass, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// Lets grab the droid details into memory, they're getting used a lot on this page
$sql = "SELECT * FROM droids WHERE droid_uid = ". $_REQUEST['droid_uid'];
$droid = $conn->query($sql)->fetch_assoc();

$sql = "SELECT * FROM club_config WHERE club_uid = ".$droid['club_uid'];
$club_config = $conn->query($sql)->fetch_assoc();

// Make sure the user is allowed to view this droid
if (($_SESSION['role'] == "user") && ( ($droid['member_uid'] != $_SESSION['user']) || ( $_SESSION['permissions'] & $perms['EDIT_DROIDS'] ) )) {
        echo "Oi, stop trying to look at other peoples droids!";
        die();
}
// And load in the member details for this droid
$sql = "SELECT * FROM members WHERE member_uid=".$droid['member_uid'];
$member = $conn->query($sql)->fetch_assoc();

$update = 0;
if ($_REQUEST['member_notes'] != "") {
    $sql = "UPDATE droids SET notes=? WHERE droid_uid=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $_REQUEST['member_notes'], $_REQUEST['droid_uid']);
    $stmt->execute();
    if ($stmt->sqlstate != "00000") {
        printf("Error: %s.\n", $stmt->sqlstate);
        printf("Error: %s.\n", $stmt->error);
    }

    $stmt->close();
    $update = 1;
}

if ($_REQUEST['back_story'] != "") {
    $sql = "UPDATE droids SET back_story=? WHERE droid_uid=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $_REQUEST['back_story'], $_REQUEST['droid_uid']);
    $stmt->execute();
    if ($stmt->sqlstate != "00000") {
        printf("Error: %s.\n", $stmt->sqlstate);
        printf("Error: %s.\n", $stmt->error);
    }

    $stmt->close();
    $update = 1;
}

if ($_REQUEST['public'] != "") {
    $sql = "UPDATE droids SET public=? WHERE droid_uid=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $_REQUEST['public'], $_REQUEST['droid_uid']);
    $stmt->execute();
    if ($stmt->sqlstate != "00000") {
        printf("Error: %s.\n", $stmt->sqlstate);
        printf("Error: %s.\n", $stmt->error);
    }

    $stmt->close();
    $update = 1;
}

/*

function imageUpload($box) {
	global $perms;
	global $member;
	if ($_SESSION['permissions'] & $perms['EDIT_DROIDS']) {
	    echo "<form method=POST enctype=\"multipart/form-data\">";
	    echo "<input type=hidden name=droid_uid value=".$_REQUEST['droid_uid'].">";
	    echo "<input type=hidden name=member_uid value=".$member['member_uid'].">";
	    echo "<input type=file name=$box>";
	    echo "<input type=submit name=upload value=$box>";
	    echo "</form>";
	}
} */

if (($_REQUEST['update'] != "") && ( $_SESSION['permissions'] & $perms['EDIT_DROIDS'] )) {
    $sql = "UPDATE droids SET primary_droid=?, style=?, radio_controlled=?, transmitter_type=?, material=?, weight=?, battery=?, drive_voltage=?, drive_type=?, top_speed=?, sound_system=?, value=?, tier_two=?, topps_id=?, active=?, club_uid=? WHERE droid_uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssssisii", $_REQUEST['primary_droid'], $_REQUEST['style'], $_REQUEST['radio_controlled'], $_REQUEST['transmitter_type'], $_REQUEST['material'], $_REQUEST['weight'],
	    $_REQUEST['battery'], $_REQUEST['drive_voltage'], $_REQUEST['drive_type'], $_REQUEST['top_speed'], $_REQUEST['sound_system'], $_REQUEST['value'], $_REQUEST['tier_two'], 
	    $_REQUEST['topps_id'], $_REQUEST['active'], $_REQUEST['club_uid'], $_REQUEST['droid_uid']);
    $stmt->execute();
    if ($stmt->sqlstate != "00000") {
        printf("Error: %s.\n", $stmt->sqlstate);
        printf("Error: %s.\n", $stmt->error);
    }

    $stmt->close();
    $update = 1;

}

$sql = "SELECT * FROM members WHERE member_uid=".$droid['member_uid'];
$member = $conn->query($sql)->fetch_assoc();


# Image uploads
if (($_REQUEST['upload'] != "") && ( $_SESSION['permissions'] & $perms['EDIT_DROIDS'] )) {
        $imagename=$_FILES[$_REQUEST['upload']]["name"];
	$imagetype=$_FILES[$_REQUEST['upload']]["type"];
	echo "Upload: ".$_REQUEST['upload'] ."<br />";
	echo "Image Name: $imagename | Image Type: $imagetype <br />";
	list($orig_width, $orig_height) = getimagesize($_FILES[$_REQUEST['upload']]['tmp_name']);
        $width = 640; // max size of image to upload
        $path = "uploads/members/".$_REQUEST['member_uid']."/".$_REQUEST['droid_uid']."/".$_REQUEST['upload'].".jpg";
        $height = (($orig_height * $width) / $orig_width);

        imagejpeg($thumb, $path, 75);

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
        $thumb = imagecreatetruecolor($width, $height);
        imagecopyresampled($thumb, $newimg, 0, 0, 0, 0, $width, $height, $orig_width, $orig_height);
        if (!file_exists("uploads/members/".$_REQUEST['member_uid']."/".$_REQUEST['droid_uid'])) {
                mkdir("uploads/members/".$_REQUEST['member_uid']."/".$_REQUEST['droid_uid']);
        }
        imagejpeg($thumb, $path, 75);

}

if (($_REQUEST['delete_comment'] == "yes") && ( $_SESSION['permissions'] & $perms['DELETE_DROIDS'])) {
    echo "Deleting comment";
    $sql = "DELETE from droid_comments WHERE uid=".$_REQUEST['uid'];
    $result = $conn->query($sql);
    $update = 1;
}

if (($_REQUEST['delete_image'] != "") && ( $_SESSION['permissions'] & $perms['DELETE_IMAGES'] )) {
    echo "Deleting image";
    unlink("uploads/members/".$member['member_uid']."/".$_REQUEST['droid_uid']."/".$_REQUEST['delete_image'].".jpg");
}

if (($_REQUEST['new_comment'] != "") && ( $_SESSION['permissions'] & $perms['EDIT_DROIDS'] )) {
    $sql = "INSERT INTO droid_comments(droid_uid, comment, added_by) VALUES (?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $_REQUEST['droid_uid'], $_REQUEST['new_comment'], $_REQUEST['officer']);
    $stmt->execute();
    if ($stmt->sqlstate != "00000") {
        printf("Error: %s.\n", $stmt->sqlstate);
        printf("Error: %s.\n", $stmt->error);
    }

    $stmt->close();
    $update = 1;
}

// If database has been altered, refresh the droid object
//
$sql = "SELECT * FROM droids WHERE droid_uid = ". $_REQUEST['droid_uid'];
$droid = $conn->query($sql)->fetch_assoc();

echo "<div id=main class=droid-container>";

echo "<div class=\"Droid-Info\">";
echo "This profile is currently: ";
if ($droid['public'] == 'No') {
        echo "Private [<a href=\"?droid_uid=".$droid['droid_uid']."&public=Yes\">Toggle</a>]";
} else {
	echo "Public [<a href=\"?droid_uid=".$droid['droid_uid']."&public=No\">Toggle</a>]";
	echo "<br />";
        echo " <a target=\"_blank\" href=\"public/display.php?droid_uid=".$droid['droid_uid']."\"> View Public Profile</a>";
}
echo "<form>";
echo "<h2>". $droid['name'] ."</h2>";
echo "<a href=display_sheet.php?droid_uid=".$_REQUEST['droid_uid'].">Get Droid Info Sheet</a>";
echo "<table class=droid>";
echo "<input type=hidden name=droid_uid value=".$_REQUEST['droid_uid'].">";
echo " <tr><th>Owner: </th><td><a href=member.php?member_uid=".$member['member_uid'].">".$member['forename']." ".$member['surname']."</a></td></tr>";
echo " <tr><th>Club: </th><td><select name=club_uid>";
$sql = "SELECT club_uid, name FROM club_config";
$clubs = $conn->query($sql);
while($row = $clubs->fetch_assoc()) {
        echo "<option ";
	echo ($row['club_uid'] == $droid['club_uid']) ? "selected" : "";
        echo " value=".$row['club_uid'].">".$row['name']."</option>";
}
echo " </select></td></tr>";
echo " <tr><th>Primary Droid: </th><td><select name=primary_droid>";
if ($droid['primary_droid'] == 'No') {
        echo "<option value=Yes>Yes</option><option value=No selected>No</option>";
} else {
        echo "<option value=Yes selected>Yes</option><option value=No>No</option>";
}
echo "</select></td></tr>";
echo " <tr><th>Type:</th><td> ".$droid['type']."</td></tr>";
echo " <tr><th>Style: </th><td><input type=text name=style size=50 value=\"".$droid['style']."\"></td></tr>";
echo " <tr><th>Radio Controlled?: </th><td><select name=radio_controlled>";
if ($droid['radio_controlled'] == 'Yes') {
        echo "<option value=Yes selected>Yes</option><option value=No>No</option>";
} else {
        echo "<option value=Yes>Yes</option><option value=No selected>No</option>";
}
echo "</select></td></tr>";
echo " <tr><th>Transmitter Type: </th><td><input type=text name=transmitter_type size=50 value=\"".$droid['transmitter_type']."\"></td></tr>";
echo " <tr><th>Material: </th><td><input type=text name=material size=50 value=\"".$droid['material']."\"></td></tr>";
echo " <tr><th>Approx Weight: </th><td><input type=text name=weight size=4 value=\"".$droid['weight']."\">Kg</td></tr>";
echo " <tr><th>Battery Type: </th><td><input type=text name=battery size=50 value=\"".$droid['battery']."\"></td></tr>";
echo " <tr><th>Drive Voltage: </th><td><input type=text name=drive_voltage size=4 value=\"".$droid['drive_voltage']."\">V</td></tr>";
echo " <tr><th>Drive Type: </th><td><input type=text name=drive_type size=50 value=\"".$droid['drive_type']."\"></td></tr>";
echo " <tr><th>Top Speed: </th><td><input type=text name=top_speed size=4 value=\"".$droid['top_speed']."\">km/h</td></tr>";
echo " <tr><th>Sound System: </th><td><input type=text name=sound_system size=50 value=\"".$droid['sound_system']."\"></td></tr>";
echo " <tr><th>Approx Value: </th><td>£<input type=text name=value size=40 value=\"".$droid['value']."\"></td></tr>";
if ($club_config['options'] & $club_options['TIER_TWO']) {
    echo " <tr><th>Tier 2 Approved: </th><td><select name=tier_two>";
    if ($droid['tier_two'] == 'Yes') {
    	echo "<option value=Yes selected>Yes</option><option value=No>No</option>";
    } else {
	echo "<option value=Yes>Yes</option><option value=No selected>No</option>";
    }
    echo "</select></td></tr>";
}
if ($config->site_options & $options['TOPPS'] && $club_config['options'] & $club_options['TOPPS']) {
    echo " <tr><th>Topps Number: </th><td><input type=text name=topps_id size=50 value=\"".$droid['topps_id']."\"></td></tr>";
} else {
    echo "<input type=hidden name=topps_id size=50 value=0>";
}
echo " <tr><th>Active?: </th><td><input name=active type=checkbox";
echo ($droid['active'] == "on") ? " checked" : "";
echo "></td></tr>";
echo " <tr><th>Last Updated: </th><td>".$droid['last_updated']."</td></tr>";
echo "</table>";
if ($_SESSION['role'] != "user") {
    echo "<input type=submit value=Update name=update>";
}
echo "</form>";
echo "</div>";

if ($club_config['options'] & $club_options['MOT']) {
$sql = "SELECT * FROM mot WHERE droid_uid = " .$_REQUEST["droid_uid"] ." ORDER BY date DESC";
$mot_result = $conn->query($sql);

echo "<div class=\"MOT-List\">";
echo "<h2>MOT Details</h2>";
if ($mot_result->num_rows > 0) {
    // output data of each row
    echo "<table>";
    echo "<tr><th>Date</th><th>Location</th><th>Officer</th><th>Approved</th><th></th></tr>";
    while($row = $mot_result->fetch_assoc()) {
	$sql = "SELECT forename, surname FROM members WHERE member_uid = ".$row["user"];
	$officer = $conn->query($sql)->fetch_assoc();
        $officer_name = $officer['forename']." ".$officer['surname'];
	if ((strtotime($row['date']) > time()-28930000) && ($row['approved'] == "Yes")) {
            echo "<tr bgcolor=green>";
        } elseif ((strtotime($row['date']) > time()-28930000) && ($row['approved'] == "Advisory")) {
	    echo "<tr bgcolor=orange>";
	} else {
            echo "<tr bgcolor=red>";
        }
	echo "<td>" . $row["date"]. "</td><td>" . $row["location"]. "</td><td>" . $officer_name. "</td><td>".$row["approved"]."</td>";
	echo "<td><a href=mot.php?mot_uid=". $row["mot_uid"]. "><img src=\"images/view_button.png\"></a>";
	if ($_SESSION['permissions'] & $perms['DELETE_DROIDS']) {
             echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=mot.php?mot_uid=". $row["mot_uid"]. "&delete=yes><img src=\"images/delete_button.png\"></a>";
        }
	echo "</td>";
	echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No MOT<br />";
}
if ($_SESSION['permissions'] & $perms['ADD_MOT']) {
    echo "<a href=new_mot.php?droid_uid=".$_REQUEST['droid_uid'].">Add new MOT</a>";
}

echo "</div>";
}
# Comments

$sql = "SELECT * FROM droid_comments WHERE droid_uid = " .$_REQUEST["droid_uid"] ." ORDER BY added_on";
$comments_result = $conn->query($sql);

echo "<div class=\"Comments\">";
echo "<table id=comment>";
if ($comments_result->num_rows > 0) {
    // output data of each row
    while($row = $comments_result->fetch_assoc()) {
        $sql = "SELECT forename,surname FROM members WHERE member_uid = ".$row["added_by"];
        $officer = $conn->query($sql)->fetch_assoc();
        $officer_name = $officer['forename']." ".$officer['surname'];
        echo "<tr><td id=officer>$officer_name</td>";
        echo "<td id=time>".$row['added_on']."</td></tr>";
        echo "<tr><td colspan=2 id=text>".$row['comment'];
	if ($_SESSION['permissions'] & $perms['DELETE_COMMENTS']) {
		echo "<br/> <a href=droid.php?droid_uid=".$row['droid_uid']."&uid=".$row['uid']."&delete_comment=yes>Delete comment</a>";
	} 
	echo "</td></tr>";
    }
} else {
    echo "No Comments";
}
if ($_SESSION['permissions'] & $perms['EDIT_DROIDS']) {
        echo "<tr><td colspan=2><form>";
        echo "<textarea name=new_comment>New comment</textarea>";
        echo "<input type=hidden name=droid_uid value=".$_REQUEST['droid_uid'].">";
        echo "<input type=hidden name=officer value=".$_SESSION['user']."><br />";
        echo "<input type=submit value=Add>";
        echo "</form></td></tr>";
}

echo "</table>";

echo "</div>";

echo "<div class=\"Builders-Notes\">";
echo "<h2>Member Notes</h2>";
echo "<form>";
echo "<textarea rows=10 cols=60 name=member_notes>".$droid['notes']."</textarea>";
echo "<input type=hidden name=droid_uid value=".$droid['droid_uid'].">";
echo "<input type=submit value=Add>";
echo "</form>";
echo "</div>";

echo "<div class=\"Back-Story\">";
echo "<h2>Back Story</h2>";
echo "<form>";
echo "<textarea rows=10 cols=60 name=back_story>".$droid['back_story']."</textarea>";
echo "<input type=hidden name=droid_uid value=".$droid['droid_uid'].">";
echo "<input type=submit value=Add>";
echo "</form>";
echo "</div>";


echo "<div class=\"Droid-Images\">";

echo "<table class=droid_images border=0px>";
echo "<tr border=0px>\n";
$photos = array("photo_front", "photo_side", "photo_rear");
foreach($photos as $photo) {
	echo "<td border=0px>";
	echo "<div id=$photo class=\"droid_image\"><img id=$photo src=\"showImage.php?club_uid=".$droid['club_uid']."&member_id=".$member['member_uid']."&droid_id=".$_REQUEST['droid_uid']."&type=droid&name=$photo&width=240\">";
	// echo "<a target=_blank href=\"showImage.php?club_uid=".$droid['club_uid']."&member_id=".$member['member_uid']."&droid_id=".$_REQUEST['droid_uid']."&type=droid&name=$photo&width=480\">Zoom</a></div>";
	echo "<a href=\"droid.php?delete_image=$photo&droid_uid=".$droid['droid_uid']."\">Delete</a></div>";
	echo "<div id=$photo class=\"w3-cell image_upload\">";
	imageUpload($photo, $droid['member_uid'], $droid['droid_uid']);
	echo "</div>";
	echo "</td>";
}
echo "</tr></table>";

if (($droid['topps_id'] != "0") &&  ($config->site_options & $options['TOPPS'])) {
	echo "<div class=topps>";
	echo "<table><tr><td>";
        	echo "<div id=topps_front class=droid_image><img id=topps_front src=\"showImage.php?member_id=".$member['member_uid']."&droid_id=".$_REQUEST['droid_uid']."&type=topps&name=topps_front&width=240\">";
		echo "<a href=\"droid.php?delete_image=topps_front&droid_uid=".$droid['droid_uid']."\">Delete</a></div>";
	        echo "<div id=topps_front class=image_upload>";
	        imageUpload('topps_front', $droid['member_uid'], $droid['droid_uid']);
	        echo "</div>";
	echo "</td><td>";
	        echo "<div id=topps_rear class=droid_image><img id=topps_rear src=\"showImage.php?member_id=".$member['member_uid']."&droid_id=".$_REQUEST['droid_uid']."&type=topps&name=topps_rear&width=240\">";
		echo "<a href=\"droid.php?delete_image=topps_rear&droid_uid=".$droid['droid_uid']."\">Delete</a></div>";
        	echo "<div id=image_side class=image_upload>";
        	imageUpload('topps_rear', $droid['member_uid'], $droid['droid_uid']);
        	echo "</div>";
	echo "</td></tr></table>";
	echo "</div>";
}

echo "</div>"; # column right


echo "</div>"; # main

?>
