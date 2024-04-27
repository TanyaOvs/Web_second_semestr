<?php
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SESSION хранятся переменные сессии.
// Будем сохранять туда логин после успешной авторизации.
$session_started = false;
if (isset($_COOKIE[session_name()]) && $_COOKIE[session_name()] /*&& session_start()*/) {
  $session_started = true;

  // Если есть логин в сессии, то пользователь уже авторизован.
  if (!empty($_SESSION['login'])) {
    // TODO: Сделать выход (окончание сессии вызовом session_destroy(), при нажатии на кнопку Выход).
    // Делаем перенаправление на форму.
    header('Location: ./'); //ВОЗМОЖНО НАДО ПИСАТЬ FORM.PHP
    exit();
  }
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
// Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {
    $formPassword = $_POST['pass'];
    $formLogin = $_POST['login'];
    include('../db.php');
    $db = new PDO('mysql:host=localhost;dbname=u67310', $user, $pass);
    $dataBaseCheck = $db->prepare("SELECT Password FROM User_Login_Data where Login = ?");
    $dataBaseCheck->execute([$formLogin]);

    $dataBasePassword = $dataBaseCheck->fetch(PDO::FETCH_ASSOC);
    $hashedPassword = $dataBasePassword['Password'];

    if (!$dataBaseCheck) {
        echo "<div class='errorLog'>Такой пользователь не существует!</div>";
        exit();
    }

    // Проверяем, соответствует ли введенный пароль хэшированному паролю из базы данных
    if (password_verify($formPassword, $hashedPassword)) {
        // Если пароли соответствуют, то авторизуем пользователя
        if (!$session_started) {
            session_start();
        }
        $_SESSION['login'] = $_POST['login'];
        $_SESSION['uid'] = 123;
        header('Location: ./');
    } else {
        // Если пароли не совпадают, выводим сообщение об ошибке
        echo "<div class='errorLog'>Неверный пароль!</div>";
        exit();
    }
}
