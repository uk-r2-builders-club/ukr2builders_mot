<div name=menu>
 <h2 id=banner><a id=logo href="http://astromech.info"></a></h2>
 <a href=index.php>Main Menu</a> | 
<? 

if (isset($_SESSION['username'])) {
   echo "Logged in as ".$_SESSION['username'];
}

?>
</div>
<hr>

