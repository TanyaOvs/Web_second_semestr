<?php
// Подключение к базе данных
include('../db.php');
$db = new PDO('mysql:host=localhost;dbname=u67310', $user, $pass);

// Если логин и пароль не заданы
if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="My site"');
    print('<h1>401 Требуется авторизация администратора</h1>');
    exit();
}

// Получаем данные админа из БД
$adminLogin = $_SERVER['PHP_AUTH_USER'];
$adminPassword = $_SERVER['PHP_AUTH_PW'];
$admin = $db->prepare("SELECT * FROM Admin_Login_Data WHERE AdminLogin = ?");
$admin->execute([$adminLogin]);
$adminData = $admin->fetch(PDO::FETCH_ASSOC);

// Проверяем логин и пароль админа
if (!$adminData || !password_verify($adminPassword, $adminData['AdminPassword'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="My site"');
    print('<h1>401 Требуется авторизация администратора</h1>');
    exit();
}
?>
