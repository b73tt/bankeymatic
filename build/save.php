<?php
$data = json_decode(file_get_contents("php://input"), true)["data"];
file_put_contents("saved.txt", $data);
?>
