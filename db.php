<?php
$servername = "localhost";
$username = "volaresoft-accounting";
$password = "U4^Nj8r+y@C.i5X*";
$database = "accounting";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Veritabanı bağlantı hatası: " . $conn->connect_error);
}
?>
