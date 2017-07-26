<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<title>Гостевая книга</title>
</head>
<body>

<?php
require_once('main.php');

// session_destroy();

echo $content;
?>

</body>
</html>