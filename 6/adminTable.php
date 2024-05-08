<?php
require_once 'admin.php';
include('../db.php');
$db = new PDO('mysql:host=localhost;dbname=u67310', $user, $pass);
?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Админ-панель</title>
</head>
<body>
<!-- Шапка -->
<div class="row justify-content-md-between">
    <div class="main_header d-flex align-items-center">
        <img id="kitty_id" src="kitty.png" alt="Логотип сайта с котиком" class="mr-4">
        <h1 class="header_name">Админ-панель</h1>
    </div>
</div>

<!-- Админ-панель -->
<div class="adminTableDiv">
    <table class="adminTab">
        <tr>
            <th>ID</th>
            <th>ФИО</th>
            <th>Телефон</th>
            <th>Почта</th>
            <th>Дата рождения</th>
            <th>Пол</th>
            <th>Биография</th>
            <th>Язык программирования</th>
            <th>Изменение</th>
            <th>Удаление</th>
        </tr>
        <?php
        $app = $db->query("SELECT app.*, pl.ProgrammingLanguage FROM Applications app 
                                LEFT JOIN Application_Ability aa ON app.ID = aa.ApplicationID 
                                LEFT JOIN Programming_Languages pl ON aa.ProgrammingLanguageID = pl.ID");
        while ($cur_app = $app->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td id = {$cur_app['ID']}>{$cur_app['ID']}</td>";
            echo "<td>{$cur_app['FIO']}</td>";
            echo "<td>{$cur_app['Phone']}</td>";
            echo "<td>{$cur_app['Email']}</td>";
            echo "<td>{$cur_app['Birthdate']}</td>";
            echo "<td>{$cur_app['Gender']}</td>";
            echo "<td>{$cur_app['Bio']}</td>";
            echo "<td>{$cur_app['ProgrammingLanguage']}</td>";
            echo "<td><div class='change-button'><a href='changeApplication.php?applicationID={$cur_app['ID']}' target='_blank' style='color: #FDE8FD!important;text-decoration: none;'>Изменить заявку</a></div></td>";
            echo "<td><button class='change-button' onclick='delApp({$cur_app['ID']})'>Удалить заявку</button></td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>

<script>
    function delApp(applicationID) {
        if (confirm("Удалить заявку?")) {
            // Отправляем AJAX запрос на сервер для удаления заявки
            $.ajax({
                type: "POST",
                url: "deleteApplication.php",
                data: { ApplicationID: applicationID },
                success: function(message) {
                    // Проверяем успешность удаления
                    if (message === "ApplicationDeleted") {
                        // Успешное удаление, обновляем страницу
                        alert("Заявка успешно удалена :)");
                        location.reload();
                    } else {
                        alert("Ошибка при удалении заявки :c");
                    }
                },
                error: function() {
                    alert("Ошибка при отправке запроса :c");
                }
            });
        }
    }
</script>

</body>
</html>


