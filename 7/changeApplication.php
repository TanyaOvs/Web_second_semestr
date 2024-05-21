<?php
// Подключение к базе данных
include('admin.php');
include('../db.php');
$db = new PDO($dbconnet, $user, $pass);

// Проверка наличия идентификатора заявки
if(isset($_GET['applicationID'])) {
    $applicationID = $_GET['applicationID'];
    echo $applicationID;
    header("Location: index.php?applicationID={$applicationID}");
    exit();
} else {
    header("Location: adminTable.php");
    exit();
}
?>
