<?php
header('Content-Type: text/html; charset=UTF-8');

//Доступные языки программирования
$valid_languages = array("Pascal", "C", "C++", "JavaScript", "PHP", "Python", "Java", "Haskell", "Clojure", "Prolog", "Scala");

// Константы для Cookies
$timeToDeleteCookie = time() - 3600;
$timeToSetCookie = time() + 360 * 24 * 60 * 60;
$timeToSetError = time() + 24 * 60 * 60;

if ($_SERVER['REQUEST_METHOD'] == 'GET'){
    // Массив сообщений  для пользователя
    $messages = array();

    if (!empty($_COOKIE['save'])) {
       setcookie('save', '', $timeToDeleteCookie);
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
         setcookie('fio_error', '', $timeToDeleteCookie ); // Удаляем куку, указывая время устаревания в прошлом.
         setcookie('fio_value', '', $timeToDeleteCookie );
         $error = "Пожалуйста, введите корректное имя.";
         $messages[] = "<div class='error-messages'>$error</div>";
      }

     if ($errors['phone']) {
        setcookie('phone_error', '', $timeToDeleteCookie);
        setcookie('phone_value', '', $timeToDeleteCookie);
        $error = "Пожалуйста, введите корректный номер телефона";
        $messages[] = "<div class='error-messages'>$error</div>";
     }

     if ($errors['email']) {
         setcookie('email_error', '', $timeToDeleteCookie);
         setcookie('email_value', '', $timeToDeleteCookie);
         $error = "Введите корректный email.";
         $messages[] = "<div class='error-messages'>$error</div>";
      }

     if ($errors['birthdate']) {
         setcookie('birthdate_error', '', $timeToDeleteCookie);
         setcookie('birthdate_value', '', $timeToDeleteCookie);
         $error = "Вы не заполнили поле 'Дата Рождения'!";
         $messages[] = "<div class='error-messages'>$error</div>";
     }

     if ($errors['gender']) {
         setcookie('gender_error', '', $timeToDeleteCookie);
         setcookie('gender_value', '', $timeToDeleteCookie);
         $error = "Пожалуйста, выберите пол.";
         $messages[] = "<div class='error-messages'>$error</div>";
     }

     if ($errors['bio']) {
         setcookie('bio_error', '', $timeToDeleteCookie);
         setcookie('bio_value', '', $timeToDeleteCookie);
         $error = "'Биография' не может содержать специальные символы или только цифры!";
         $messages[] = "<div class='error-messages'>$error</div>";
     }

     if ($errors['check']) {
          setcookie('check_error', '', $timeToDeleteCookie);
          setcookie('check_value', '', $timeToDeleteCookie);
          $error = "Вы не поставили галочку в поле 'Соглашение'!";
          $messages[] = "<div class='error-messages'>$error</div>";
     }

     if($errors['language_n']){
        setcookie('language_null_error', '', $timeToDeleteCookie);
        setcookie('languages_values', '', $timeToDeleteCookie);
        $error = "Вы не выбрали Любимый язык программирования!";
        $messages[] = "<div class='error-messages'>$error</div>";
     } else if($errors['language_d']){
        setcookie('language_data_error', '', $timeToDeleteCookie);
        setcookie('languages_values', '', $timeToDeleteCookie);
        $error = "Некорректные данные в 'Любимый язык программирования'!";
        $messages[] = "<div class='error-messages'>$error</div>";
     }


    // Складываем предыдущие значения полей в массив, если есть.
    $values = array();
    $values['fio'] = empty($_COOKIE['fio_value']) ? '' : $_COOKIE['fio_value'];
    $values['phone'] = empty($_COOKIE['phone_value']) ? '' : $_COOKIE['phone_value'];
    $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
    $values['birthdate'] = empty($_COOKIE['birthdate_value']) ? '' : $_COOKIE['birthdate_value'];
    $values['gender'] = empty($_COOKIE['gender_value']) ? '' : $_COOKIE['gender_value'];
    $values['bio'] = empty($_COOKIE['bio_value']) ? '' : $_COOKIE['bio_value'];
    $values['check'] = empty($_COOKIE['check_value']) ? '' : $_COOKIE['check_value'];
    $values['languages'] = empty($_COOKIE['languages_values']) ? array() : explode(",", $_COOKIE['languages_values']);

    include('form.php');
}
else {
    include('../db.php');
    $db = new PDO('mysql:host=localhost;dbname=u67310', $user, $pass);

    //Данные из формы
    $name = $_POST['fio'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $birthdate = isset($_POST['birthdate']) ? $_POST['birthdate'] : '';
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $bio = $_POST['bio'];
    $check = isset($_POST['check']) ? 1 : 0;
    $languages = isset($_POST['language']) ? $_POST['language'] : '';

    // Проверка корректности заполнения полей
    $errorsExist = FALSE;

    if (!preg_match("/^[a-zA-Zа-яА-Я ]+$/u", $name)) {
        setcookie('fio_error', '1', $timeToSetError);
        $errorsExist = TRUE;
    }
    // Сохраняем ранее введенное в форму значение на год
    setcookie('fio_value', $name, $timeToSetCookie);

    if (!preg_match("/^\+?[0-9]{1,4}[0-9]{10}$/", $phone)) {
        setcookie('phone_error', '1', $timeToSetError);
        $errorsExist = TRUE;
    }
    setcookie('phone_value', $phone, $timeToSetCookie);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setcookie('email_error', '1', $timeToSetError);
        $errorsExist = TRUE;
    }
    setcookie('email_value', $email, $timeToSetCookie);

    $birthdateObj = DateTime::createFromFormat('Y-m-d', $birthdate);
    if (!$birthdateObj || $birthdateObj->format('Y-m-d') !== $birthdate) {
        setcookie('birthdate_error', '1', $timeToSetError);
        $errorsExist = TRUE;
    }
    setcookie('birthdate_value', $birthdate, $timeToSetCookie);

    if (empty($gender) || ($gender !== 'male' && $gender !== 'female')) {
        setcookie('gender_error', '1', $timeToSetError);
        $errorsExist = TRUE;
    }
    setcookie('gender_value', $gender, $timeToSetCookie);

    if (!preg_match("/^[a-zA-Zа-яА-Я.,! ]*$/u", $bio)) {
        setcookie('bio_error', '1', $timeToSetError);
        $errorsExist = TRUE;
    }
    setcookie('bio_value', $bio, $timeToSetCookie);

    if(empty($check) || $check != 1) {
        setcookie('check_error', '1', $timeToSetError);
        $errorsExist = TRUE;
    }
    setcookie('check_value', $check, $timeToSetCookie);

    $selected_languages = '';
    if(empty($languages)){
        setcookie('language_null_error', '1', $timeToSetError);
        $errorsExist = TRUE;
    }
    else{
        foreach ($languages as $p_language) {
            if (!in_array($p_language, $valid_languages)) {
                setcookie('language_data_error', '1', $timeToSetError);
                $errorsExist = TRUE;
                break;
            }
            if (!empty($selected_languages)) {
              $selected_languages .= ',';
            }
            $selected_languages .= $p_language;
        }
    }
    setcookie('languages_values', $selected_languages, $timeToSetCookie);

    if ($errorsExist) {
    // При наличии ошибок перезагружаем страницу и завершаем работу скрипта
        header('Location: index.php');
        exit();
    }
    else {
        // Удаляем Cookies с признаками ошибок.
        setcookie('fio_error', '', $timeToDeleteCookie);
        setcookie('phone_error', '', $timeToDeleteCookie);
        setcookie('email_error', '', $timeToDeleteCookie);
        setcookie('birthdate_error', '', $timeToDeleteCookie);
        setcookie('gender_error', '', $timeToDeleteCookie);
        setcookie('bio_error', '', $timeToDeleteCookie);
        setcookie('language_null_error', '', $timeToDeleteCookie);
        setcookie('language_data_error', '', $timeToDeleteCookie);
        setcookie('check_error', '', $timeToDeleteCookie);

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
        header('Location: index.php?actionsCompleted=1');
        exit();
        }
        else {
            echo "<div class='error-message' style='color: red;'>Ошибка при вставке данных в базу.</div>";
        }
    }
}
?>
