<?php
// Подключение к базе данных
include('admin.php');
include('../db.php');
$db = new PDO('mysql:host=localhost;dbname=u67310', $user, $pass);

// Получение идентификатора заявки из запроса
$appID = isset($_POST['ApplicationID']) ? $_POST['ApplicationID'] : null;

// Проверка наличия идентификатора заявки
if ($appID !== null) {
    try {
        // Начало транзакции
        $db->beginTransaction();

        // Удаление записей из таблицы Application_Ability
        $appAb = $db->prepare("DELETE FROM Application_Ability WHERE ApplicationID = :id");
        $appAb->bindParam(':id', $appID, PDO::PARAM_INT);
        $appAb->execute();

        // Удаление записей из таблицы User_Login_Data
        $user = $db->prepare("DELETE FROM User_Login_Data WHERE UserID = :id");
        $user->bindParam(':id', $appID, PDO::PARAM_INT);
        $user->execute();

        // Удаление записей из таблицы Applications
        $app = $db->prepare("DELETE FROM Applications WHERE ID = :id");
        $app->bindParam(':id', $appID, PDO::PARAM_INT);
        $app->execute();

        // Фиксация транзакции
        $db->commit();

        // Успешное удаление
        echo "ApplicationDeleted";
    } catch (PDOException $e) {
        // Ошибка при выполнении запросов или транзакции
        $db->rollBack();
        echo "Error";
    }
} else {
    // Идентификатор заявки не передан или некорректен
    echo "Incorrect ID!";
}
?>