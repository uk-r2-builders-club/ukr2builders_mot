
<?

echo "<hr>";
echo "<p class=footer>";
if (isset($_SESSION['username'])) {
   echo "Logged in as ".$_SESSION['username'];
}

$conn->close();
?>
