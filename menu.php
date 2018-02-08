<div name=menu>
 <a href=index.php>Main Menu</a> | 
<? 

if (isset($_SESSION['username'])) {
   echo "Logged in as ".$_SESSION['username'];
}

?>
</div>
<hr>

