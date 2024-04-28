<?php
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SESSION хранятся переменные сессии.
// Будем сохранять туда логин после успешной авторизации.
$session_started = false;
session_start();
// Если есть логин в сессии, то пользователь уже авторизован.
if (!empty($_SESSION['login'])) {
    $session_started = true;
    if(isset($_GET['logout'])) {
      // Завершаем сессию
      session_start();
      session_unset();
      session_destroy();
      setcookie('login', '', time() - 3600);
      setcookie('pass', '', time() - 3600);

      // Перенаправляем пользователя на форму
      header("Location: index.php");
      exit();
}
else{
    session_unset();
    session_destroy();
}

header('Location: ./');
exit();
}


// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
?>
<html lang="ru">
<head>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <title>Авторизация</title>
</head>
<div class="form">
    <div class="errorLog"></div>
    <form action="" method="post">
      <input name="login" />
      <input name="pass" />
      <input type="submit" value="Войти" />
    </form>
</div>

<?php
}
else {
    $formPassword = $_POST['pass'];
    $formLogin = $_POST['login'];
    include('../db.php');
    $db = new PDO('mysql:host=localhost;dbname=u67310', $user, $pass);
    $dataBaseCheck = $db->prepare("SELECT Password FROM User_Login_Data where Login = ?");
    $dataBaseCheck->execute([$formLogin]);
    $hashedPassword = $dataBaseCheck->fetchColumn();

    if (!$dataBaseCheck) {
        echo "<div class='errorLog'>Такой пользователь не существует! Вернитесь на предыдущю страницу через <- и исправьте данные!</div>";
        //exit("Такой пользователь не существует!");
    }

    // Проверяем, соответствует ли введенный пароль хэшированному паролю из базы данных
    if ($hashedPassword && password_verify($formPassword, $hashedPassword)) {
        // Если пароли соответствуют, то авторизуем пользователя
        if (!$session_started) {
            session_start();
        }
        $_SESSION['login'] = $_POST['login'];
        $_SESSION['uid'] = 123;
        header('Location: ./');
    } else {
        // Если пароли не совпадают, выводим сообщение об ошибке
        print('<div class="errorLog">Неверный пароль! Вернитесь на предыдущю страницу через <- и исправьте данные!</div>');
        //exit("Неверный пароль!");
    }
}
