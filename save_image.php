<?

$data = $_POST['image'];

list($type, $data) = explode(';', $data);
list(, $data)      = explode(',', $data);
$data = base64_decode($data);
$lrgimage = imagecreatefromstring($data);
$smlimage = imagescale($lrgimage, 240);


if ($_POST['type'] == "mug_shot") {
    $newImage = 'uploads/members/'.$_POST['member'].'/480-mug_shot.jpg';
    imagejpeg($lrgimage,$newImage,90);
    $newImage = 'uploads/members/'.$_POST['member'].'/240-mug_shot.jpg';
    imagejpeg($smlimage,$newImage,90);
} else {
    $newImage = 'uploads/members/'.$_POST['member'].'/'.$_POST['droid'].'/480-'.$_POST['type'].'.jpg';
    imagejpeg($lrgimage,$newImage,90);
    $newImage = 'uploads/members/'.$_POST['member'].'/'.$_POST['droid'].'/240-'.$_POST['type'].'.jpg';
    imagejpeg($smlimage,$newImage,90);

}

?>
