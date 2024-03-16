<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = 'u67310';
    $pass = '8200920';
    $db = new PDO('mysql:host=localhost;dbname=u67310', $user, $pass);

    //Данные из формы
    $name = $_POST['FIO'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $birthdate = $_POST['birthdate'];
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $bio = $_POST['bio'];
    $check = isset($_POST['check']) ? 1 : 0;

    // Проверка корректности заполнения полей
	echo "<div class='error-message-container'>";
    $errors = [];

    if (!preg_match("/^[a-zA-Zа-яА-Я ]+$/u", $name)) {
        $errors[] = "Пожалуйста, введите корректное имя.";
    }

    if (!preg_match("/^\+?[0-9]{1,4}[0-9]{10}$/", $phone)) {
        $errors[] = "Пожалуйста, введите корректный номер телефона";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Введите корректный email.";
    }

    $birthdateObj = DateTime::createFromFormat('Y-m-d', $birthdate);
    if (!$birthdateObj || $birthdateObj->format('Y-m-d') !== $birthdate) {
        $errors[] = "Дата рождения должна быть в формате ГОД-МЕСЯЦ-ДЕНЬ (например, 2000-01-31)!";
    }

    if (empty($gender)) {
        $errors[] = "Пожалуйста, выберите пол.";
    }

    if (!preg_match("/^[a-zA-Zа-яА-Я.,! ]+$/u", $bio)) {
        $errors[] = "Поле Биография не может содержать специальные символы!";
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<div error-message style='margin-top: 10px;font: small-caps 1.2rem sans-serif;font-size: 36px;text-align: center;color: #68006C;background: #FDE8FD;'>$error</div>";
        }
    }
    else {
        $stmt = $db->prepare("INSERT INTO Applications (FIO, Phone, Email, Birthdate, Gender, Contract, Bio) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if($stmt->execute([$name, $phone, $email, $birthdate, $gender, $check, $bio])){
            $last_id = $db->lastInsertId();
            foreach ($_POST['language'] as $language) {
                $stmt = $db->prepare("SELECT ID FROM Programming_Languages WHERE ProgrammingLanguage = ?");
                $stmt->execute([$language]);
                $programming_language_id = $stmt->fetchColumn();

                $stmt = $db->prepare("INSERT INTO Application_Ability (ApplicationID, ProgrammingLanguageID) VALUES (?, ?)");
                $stmt->execute([$last_id, $programming_language_id]);
                }
        header('Location: form.php?actionsCompleted=1');
        exit();
        }
        else {
            echo "<div class='error-message' style='color: red;'>Ошибка при вставке данных в базу.</div>";
        }
    }
}
include('form.php');
?>
