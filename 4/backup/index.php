<?php
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET'){
    // Массив для временного хранения сообщений пользователю.
    $messages = array();

    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', 100000);
        $messages[] = 'Спасибо, результаты сохранены.';
     }

    // Массив для хранения ошибок
    $errors = array();
    $errors['fio'] = !empty($_COOKIE['fio_error']);
    /* $errors['phone'] = !empty($_COOKIE['phone_error']);
    $errors['email'] = !empty($_COOKIE['email_error']);
    $errors['birthdate'] = !empty($_COOKIE['birthdate']);
    $errors['gender'] = !empty($_COOKIE['gender_error']);
    $errors['bio'] = !empty($_COOKIE['bio_error']);
    $errors['check'] = !empty($_COOKIE['check_error']);
    $errors['language'] = !empty($_COOKIE['language_error']); */

    //Выдаем сообщения об ошибках для каждого поля
    if ($errors['fio']) {
        setcookie('fio_error', '', 100000); // Удаляем куку, указывая время устаревания в прошлом.
        $error = "Пожалуйста, введите корректное имя.";
        $messages[] = "<div class='error-messages'>$error</div>";
     }
    //TODO: Дописать для остальных полей

    // Складываем предыдущие значения полей в массив, если есть.
    $values = array();
    $values['fio'] = empty($_COOKIE['fio_value']) ? '' : $_COOKIE['fio_value'];
    // TODO: аналогично все поля.
    include('form.php');
}
else {
    // Данные для подключения к БД
    $user = 'u67310';
    $pass = '8200920';
    $db = new PDO('mysql:host=localhost;dbname=u67310', $user, $pass);

    //Данные из формы
    $name = $_POST['fio'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $birthdate = $_POST['birthdate'];
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $bio = $_POST['bio'];
    $check = isset($_POST['check']) ? 1 : 0;
    $languages = isset($_POST['language']) ? $_POST['language'] : '';

    // Проверка корректности заполнения полей
	//echo "<div class='error-message-container'>";
    $errorsExist = FALSE;

    if (!preg_match("/^[a-zA-Zа-яА-Я ]+$/u", $name)) {
        //$errors[] = "Пожалуйста, введите корректное имя.";
        setcookie('fio_error', '1', time() + 24 * 60 * 60);
        $errorsExist = TRUE;
    }
    else {
        // Сохраняем ранее введенное в форму значение на год
        setcookie('fio_value', $name, time() + 360 * 24 * 60 * 60);
    }

    // *************
    // TODO: тут необходимо проверить правильность заполнения всех остальных полей.
    // Сохранить в Cookie признаки ошибок и значения полей.
    // *************


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
    } elseif ($gender !== 'male' && $gender !== 'female') {
          $errors[] = "Некорретктное значение пола.";
    }

    if (!preg_match("/^[a-zA-Zа-яА-Я.,! ]*$/u", $bio)) {
        $errors[] = "Поле Биография не может содержать специальные символы!";
    }

    // Список допустимых языков программирования
    if(empty($languages)){
        $errors[] = "Вы не выбрали Любимый язык программирования!";
    }
    else{
        $valid_languages = array("Pascal", "C", "C++", "JavaScript", "PHP", "Python", "Java", "Haskell", "Clojure", "Prolog", "Scala");
        foreach ($languages as $p_language) {
            if (!in_array($p_language, $valid_languages)) {
                $errors[] = "Некорректные данные в 'Любимый язык программирования'!";
                break;
            }
        }
    }

    if ($errorsExist) {
    // При наличии ошибок перезагружаем страницу и завершаем работу скрипта
        header('Location: index.php');
        exit();
    }
    else {
        // Удаляем Cookies с признаками ошибок.
        setcookie('fio_error', '', 100000);
        // TODO: тут необходимо удалить остальные Cookies.

        $application = $db->prepare("INSERT INTO Applications (FIO, Phone, Email, Birthdate, Gender, Contract, Bio) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if($application->execute([$name, $phone, $email, $birthdate, $gender, $check, $bio])){
            $last_id = $db->lastInsertId();

            //Подготовка данных
            $stmt_language = $db->prepare("SELECT ID FROM Programming_Languages WHERE ProgrammingLanguage = ?");
            $stmt = $db->prepare("INSERT INTO Application_Ability (ApplicationID, ProgrammingLanguageID) VALUES (?, ?)");

            foreach ($languages as $language) {
                $stmt_language->execute([$language]);
                $programming_language_id = $stmt_language->fetchColumn();
                $stmt->execute([$last_id, $programming_language_id]);
            }

        setcookie('save', '1');
        header('Location: form.php?actionsCompleted=1');
        exit();
        }
        else {
            echo "<div class='error-message' style='color: red;'>Ошибка при вставке данных в базу.</div>";
        }
    }
}
//include('form.php');
?>