<?php
//////////////////////////
////      SESSION     ////
//// Автор – GSergeyP ////
////   Версия 1.0.0   ////
//////////////////////////
//Подключение к БД
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'database';
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error){
    die("Ошибка подключения: " . $conn->connect_error);
}
?>