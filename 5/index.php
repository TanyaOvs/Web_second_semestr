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
       setcookie('login', '', $timeToDeleteCookie);
       setcookie('password', '', $timeToDeleteCookie);
       // Если в куках есть пароль, то выводим сообщение.
       if (!empty($_COOKIE['password'])) {
         $messages[] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
           и паролем <strong>%s</strong> для изменения данных.',
           strip_tags($_COOKIE['login']),
           strip_tags($_COOKIE['password']));
       }
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
    $values['fio'] = empty($_COOKIE['fio_value']) ? '' : strip_tags($_COOKIE['fio_value']);
    $values['phone'] = empty($_COOKIE['phone_value']) ? '' : strip_tags($_COOKIE['phone_value']);
    $values['email'] = empty($_COOKIE['email_value']) ? '' : strip_tags($_COOKIE['email_value']);
    $values['birthdate'] = empty($_COOKIE['birthdate_value']) ? '' : strip_tags($_COOKIE['birthdate_value']);
    $values['gender'] = empty($_COOKIE['gender_value']) ? '' : strip_tags($_COOKIE['gender_value']);
    $values['bio'] = empty($_COOKIE['bio_value']) ? '' : strip_tags($_COOKIE['bio_value']);
    $values['check'] = empty($_COOKIE['check_value']) ? '' : strip_tags($_COOKIE['check_value']);
    $values['languages'] = empty($_COOKIE['languages_values']) ? array() : explode(",", strip_tags($_COOKIE['languages_values']));

    /*ВСТАВКА 2*/
    // Если нет предыдущих ошибок ввода,начали сессию и в сессию записан факт успешного логина.
    if (empty($errors) && session_start() && !empty($_SESSION['login'])) {
      // TODO: загрузить данные пользователя из БД И заполнить переменную $values, предварительно санитизовав.
        include('../db.php');
        $db = new PDO('mysql:host=localhost;dbname=u67310', $user, $pass);

        $user_l_d = $db->prepare("SELECT UserID FROM User_Login_Data WHERE Login = ?");
        $user_l_d->execute([$_SESSION['login']]);
        $user_row = $user_l_d->fetch(PDO::FETCH_ASSOC);
        $user_id = $user_row['UserID'];

        $select = $db->prepare("SELECT (FIO, Phone, Email, Birthdate, Gender, Contract, Bio) FROM Applications WHERE ID = ?");
        if($select->execute([$user_id])){
            $app_row = $select->fetch(PDO::FETCH_ASSOC);
            $values['fio'] = $app_row['FIO'];
            $values['phone'] = $app_row['Phone'];
            $values['email'] = $app_row['Email'];
            $values['birthdate'] = $app_row['Birthdate'];
            $values['gender'] = $app_row['Gender'];
            $values['bio'] = $app_row['Bio'];
            $values['check'] = $app_row['Contract'];
            echo "<div class='error-messages'>$app_row</div>";
        }
        else {
            echo "<div class='error-messages'>Ошибка при вставке данных в базу.</div>";
        }

    }

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
    }

    /*ВСТАВКА 3*/
    if (!empty($_COOKIE[session_name()]) && session_start() && !empty($_SESSION['login'])) {
        $user_l_d = $db->prepare("SELECT UserID FROM User_Login_Data WHERE Login = ?");
        $user_l_d->execute([$_SESSION['login']]);
        $user_row = $user_l_d->fetch(PDO::FETCH_ASSOC);
        $user_id = $user_row['UserID'];
        $update = $db->prepare("UPDATE Applications SET FIO = ?, Phone = ?, Email = ?, Birthdate = ?, Gender = ?, Bio = ?, Contract = ? WHERE ID = ?");
        if($update->execute([$name, $phone, $email, $birthdate, $gender, $bio, $check, $user_id])){
            setcookie('save', '1');
            header('Location: index.php?actionsCompleted=1');
            exit();
        }
        else {
            echo "<div class='error-messages'>Ошибка при вставке данных в базу.</div>";
        }

    }
    else{
        function generateRandomPassword($length) {
            $characters = '@!#*()/\%0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }
        // Генерируем уникальный логин и пароль.
        $login = uniqid();
        $password = generateRandomPassword(8);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Сохраняем в Cookies.
        setcookie('login', $login);
        setcookie('password', $password);

        $application = $db->prepare("INSERT INTO Applications (FIO, Phone, Email, Birthdate, Gender, Contract, Bio) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if($application->execute([$name, $phone, $email, $birthdate, $gender, $check, $bio])){
            $last_id = $db->lastInsertId();

            //Записываем в базу данных логин и пароль пользователя
            $user_login_data = $db->prepare("INSERT INTO User_Login_Data (UserId, Login, Password) VALUES (?, ?, ?)");
            $user_login_data->execute([$last_id, $login, $hashed_password]);

            //Подготовка данных для записи языков програмимрования
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
            echo "<div class='error-messages'>Ошибка при вставке данных в базу.</div>";
        }
    }
}
?>
