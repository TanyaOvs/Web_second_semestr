<?php

$valid_languages = array("Pascal", "C", "C++", "JavaScript", "PHP", "Python", "Java", "Haskell", "Clojure", "Prolog", "Scala");

header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET'){
    // Массив сообщений  для пользователя
    $messages = array();

    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', time() - 3600);
     }

    // Массив для хранения ошибок
    $errors = array();
    $errors['fio'] = !empty($_COOKIE['fio_error']);
    $errors['phone'] = !empty($_COOKIE['phone_error']);
    $errors['email'] = !empty($_COOKIE['email_error']);
    $errors['birthdate'] = !empty($_COOKIE['birthdate']);
    $errors['gender'] = !empty($_COOKIE['gender_error']);
    $errors['bio'] = !empty($_COOKIE['bio_error']);
    $errors['check'] = !empty($_COOKIE['check_error']);
    $errors['language_n'] = !empty($_COOKIE['language_null_error']); // Язык не выбран
    $errors['language_d'] = !empty($_COOKIE['language_data_error']); // Некорректные данные для языка

    //Выдаем сообщения об ошибках для каждого поля
    if ($errors['fio']) {
        setcookie('fio_error', '', time() - 3600); // Удаляем куку, указывая время устаревания в прошлом.
        setcookie('fio_value', '', time() - 3600);
        $error = "Пожалуйста, введите корректное имя.";
        $messages[] = "<div class='error-messages'>$error</div>";
     }

     if ($errors['phone']) {
        setcookie('phone_error', '', time() - 3600);
        setcookie('phone_value', '', time() - 3600);
        $error = "Пожалуйста, введите корректный номер телефона";
        $messages[] = "<div class='error-messages'>$error</div>";
     }

     if ($errors['email']) {
         setcookie('email_error', '', time() - 3600);
         setcookie('email_value', '', time() - 3600);
         $error = "Введите корректный email.";
         $messages[] = "<div class='error-messages'>$error</div>";
      }

     if ($errors['birthdate']) {
         setcookie('birthdate_error', '', time() - 3600);
         setcookie('birthdate_value', '', time() - 3600);
         $error = "Дата рождения должна быть в формате ДЕНЬ-МЕСЯЦ-ГОД (например, 15-03-2002)!";
         $messages[] = "<div class='error-messages'>$error</div>";
     }

     if ($errors['gender']) {
         setcookie('gender_error', '', time() - 3600);
         setcookie('gender_value', '', time() - 3600);
         $error = "Пожалуйста, выберите пол.";
         $messages[] = "<div class='error-messages'>$error</div>";
     }

     if ($errors['bio']) {
         setcookie('bio_error', '', time() - 3600);
         setcookie('bio_value', '', time() - 3600);
         $error = "Поле 'Биография' не может содержать специальные символы!";
         $messages[] = "<div class='error-messages'>$error</div>";
     }

     if($errors['language_n']){
        setcookie('language_null_error', '', time() - 3600);
        $error = "Вы не выбрали Любимый язык программирования!";
        $messages[] = "<div class='error-messages'>$error</div>";
     } else if($errors['language_d']){
        setcookie('language_data_error', '', time() - 3600);
        $error = "Некорректные данные в 'Любимый язык программирования'!";
        $messages[] = "<div class='error-messages'>$error</div>";
     }
    /*********** Поле Check? **************************************************************/

    // Складываем предыдущие значения полей в массив, если есть.
    $values = array();
    $values['fio'] = empty($_COOKIE['fio_value']) ? '' : $_COOKIE['fio_value'];
    $values['phone'] = empty($_COOKIE['phone_value']) ? '' : $_COOKIE['phone_value'];
    $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
    $values['birthdate'] = empty($_COOKIE['birthdate_value']) ? '' : $_COOKIE['birthdate_value'];
    $values['gender'] = empty($_COOKIE['gender_value']) ? '' : $_COOKIE['gender_value']; //Нужно ли сохранять это значение
    $values['bio'] = empty($_COOKIE['bio_value']) ? '' : $_COOKIE['bio_value'];
    $values['check'] = empty($_COOKIE['check_value']) ? '' : $_COOKIE['check_value'];
    $values['languages'] = empty($_COOKIE['languages']) ? array() : explode(",", $_COOKIE['languages']);
    /*********** Как обработать языки программирования? **************************************************************/

    include('form.php');
}
else {
    include('../db.php');
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
    $errorsExist = FALSE;

    if (!preg_match("/^[a-zA-Zа-яА-Я ]+$/u", $name)) {
        setcookie('fio_error', '1', time() + 24 * 60 * 60);
        $errorsExist = TRUE;
    }
    // Сохраняем ранее введенное в форму значение на год
    setcookie('fio_value', $name, time() + 360 * 24 * 60 * 60);

    if (!preg_match("/^\+?[0-9]{1,4}[0-9]{10}$/", $phone)) {
        setcookie('phone_error', '1', time() + 24 * 60 * 60);
        $errorsExist = TRUE;
    }
    setcookie('phone_value', $phone, time() + 360 * 24 * 60 * 60);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setcookie('email_error', '1', time() + 24 * 60 * 60);
        $errorsExist = TRUE;
    }
    setcookie('email_value', $email, time() + 360 * 24 * 60 * 60);

    $birthdateObj = DateTime::createFromFormat('Y-m-d', $birthdate);
    if (!$birthdateObj || $birthdateObj->format('Y-m-d') !== $birthdate) {
        setcookie('birthdate_error', '1', time() + 24 * 60 * 60);
        $errorsExist = TRUE;
    }
    setcookie('birthdate_value', $birthdate, time() + 360 * 24 * 60 * 60);

    if (empty($gender) || ($gender !== 'male' && $gender !== 'female')) {
        setcookie('gender_error', '1', time() + 24 * 60 * 60);
        $errorsExist = TRUE;
    }
    setcookie('gender_value', $gender, time() + 360 * 24 * 60 * 60);

    if (!preg_match("/^[a-zA-Zа-яА-Я.,! ]*$/u", $bio)) {
        setcookie('bio_error', '1', time() + 24 * 60 * 60);
        $errorsExist = TRUE;
    }
    setcookie('bio_value', $bio, time() + 360 * 24 * 60 * 60);

    if($check == 1){
        setcookie('check_value', $check, time() + 360 * 24 * 60 * 60);
    }

    $selected_languages = '';
    if(empty($languages)){
        setcookie('language_null_error', '1', time() + 24 * 60 * 60);
        $errorsExist = TRUE;
    }
    else{
        foreach ($languages as $p_language) {
            if (!in_array($p_language, $valid_languages)) {
                setcookie('language_data_error', '1', time() + 24 * 60 * 60);
                $errorsExist = TRUE;
                break;
            }
            if (!empty($selected_languages)) {
              $selected_languages .= ',';
            }
            $selected_languages .= $p_language;
        }
    }
    setcookie('languages', $selected_languages, time() + 360 * 24 * 60 * 60);

    if ($errorsExist) {
    // При наличии ошибок перезагружаем страницу и завершаем работу скрипта
        header('Location: index.php');
        exit();
    }
    else {
        // Удаляем Cookies с признаками ошибок.
        setcookie('fio_error', '', time() - 3600);
        setcookie('phone_error', '', time() - 3600);
        setcookie('email_error', '', time() - 3600);
        setcookie('birthdate_error', '', time() - 3600);
        setcookie('gender_error', '', time() - 3600);
        setcookie('bio_error', '', time() - 3600);
        setcookie('language_null_error', '', time() - 3600);
        /***************************************Поле check ?***************************/
        setcookie('language_data_error', '', time() - 3600);

        /*********** Удалять ли values? **************************************************************/
        // Сделать масссив со списком values и удалять их в цикле foreach

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
?>