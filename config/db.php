<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbname = 'saw_project';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die('Koneksi database gagal: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
