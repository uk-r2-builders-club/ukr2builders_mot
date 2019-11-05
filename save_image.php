<?

$file = fopen("test.txt", "a");
fwrite($file, $_POST['image']);
fclose($file);


?>
