<?php
$data = $_POST["data"];
file_put_contents("saved.txt", $data);
?>
