<?php

session_start();

if (isset($_POST['email'])) {

  //Udana walidacja
  $everything_is_OK = true;

  //Sprawdź poprawność loginu
  $login = $_POST['login'];

  //Sprawdzenie długości nicka
  if ((strlen($login) < 3) || (strlen($login) > 20)) {
    $everything_is_OK = false;
    $_SESSION['e_login'] = "Login musi posiadać od 3 do 20 znaków!";
  }

  if (ctype_alnum($login) == false) {
    $everything_is_OK = false;
    $_SESSION['e_login'] = "Login może składać się tylko z liter i cyfr (bez polskich znalów)";
  }

  //Sprawdź poprawność adresu email
  $email = $_POST['email'];
  $emailB = filter_var($email, FILTER_SANITIZE_EMAIL);


  if ((filter_var($emailB, FILTER_VALIDATE_EMAIL) == false) || ($emailB != $email)) {
    $everything_is_OK = false;
    $_SESSION['e_email'] = "Podaj poprawny adres e-mail";
  }

  //Sprawdź poprawność hasła
  $password1 = $_POST['password1'];
  $password2 = $_POST['password2'];

  if ((strlen($password1) < 8) || (strlen($password1) > 20)) {
    $everything_is_OK = false;
    $_SESSION['e_password'] = "Hasło musi posiadać od 8 do 20 znaków";
  }

  if ($password1 != $password2) {
    $everything_is_OK = false;
    $_SESSION['e_password'] = "Podane hasła nie są identyczne";
  }

  $password_hash = password_hash($password1, PASSWORD_DEFAULT);

  //Czy zaakceptowano regulamin
  if (!isset($_POST['terms'])) {
    $everything_is_OK = false;
    $_SESSION['e_terms'] = "Potwierdż akceptację regulaminu!";
  }

  //Recaptcha
  $secret = "6LdJi1kqAAAAALo14d7EN0pfpyyUKObNwTKSRQK1";

  $check = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $_POST['g-recaptcha-response']);

  $answer = json_decode($check);
  if ($answer->success == false) {
    $everything_is_OK = false;
    $_SESSION['e_bot'] = "Potwierdź, że nie jesteś botem!";
  }

  //Zapamiętaj wprowadzone dane
  $_SESSION['fr_login'] = $login;
  $_SESSION['fr_email'] = $email;
  $_SESSION['fr_password1'] = $password1;
  $_SESSION['fr_password2'] = $password2;
  if (isset($_POST['terms']))
    $_SESSION['fr_terms'] = true;

  require_once "connect.php";
  mysqli_report(MYSQLI_REPORT_STRICT);

  try {
    $connect = new mysqli($host, $db_user, $db_password, $db_name);
    if ($connect->connect_errno != 0) {
      throw new Exception(mysqli_connect_error());

    } else {
      // Czy email już istnieje ?
      $result = $connect->query("SELECT id FROM users WHERE email='$email'");

      if (!$result) {
        throw new Exception($connect->error);
      }
      $how_many_emails = $result->num_rows;
      if ($how_many_emails > 0) {
        $everything_is_OK = false;
        $_SESSION['e_email'] = "Istnieje już konto przypisane do tego adresu email";
      }

      //Czy nick jest zarezerwowany?
      $result = $connect->query("SELECT id FROM users WHERE username='$login'");

      if (!$result) {
        throw new Exception($connect->error);
      }
      $how_many_login = $result->num_rows;
      if ($how_many_login > 0) {
        $everything_is_OK = false;
        $_SESSION['e_login'] = "Istnieje już użytkownik o takim loginie! Wybierz inny.";
      }

      if ($everything_is_OK == true) {

        //Jeśli wszystko jest okey (true) dodajemy użytkownka do bazy danych

        $result = $connect->query("INSERT INTO users VALUES (NULL, '$login', '$password_hash', '$email')");

        if (!$result) {
          throw new Exception($connect->error);
        }

        $new_user_id = $connect->insert_id;

        // Kopiujemy dane z tabeli default do tabeli users

        $table_users = array("incomes_category_assigned_to_users", "payment_methods_assigned_to_users", "expenses_category_assigned_to_users");

        $table_default = array("incomes_category_default", "payment_methods_default", "expenses_category_default");

        for ($i = 0; $i < count($table_default); $i++) {
          $result = $connect->query("INSERT INTO {$table_users[$i]} (user_id, name) SELECT '$new_user_id', name FROM {$table_default[$i]}");
          if (!$result) {
            throw new Exception($connect->error);
          }
        }

        $_SESSION['successful_registration'] = true;
        header('Location: welcome.php');

      }

      $connect->close();

    }
  } catch (Exception $e) {
    error_log("Błąd aplikacji: " . $e->getMessage());
    echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!</span>';
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Budget Manager</title>
  <script src="https://www.google.com/recaptcha/enterprise.js" async defer></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  <link rel="stylesheet" href="../css/style.css" />
</head>

<body>
  <header>
    <div class="container col-xxl-10 d-flex justify-content-lg-start">
      <h1 class="display-1 fw-bold text-white lh-1 mt-4">Budget Manager</h1>
    </div>
  </header>
  <main>
    <div class="container col-xxl-10 px-4 py-1">
      <div class="row d-flex flex-column flex-lg-row align-items-center">
        <div class="col-12 col-sm-6 col-lg-6 order-2">
          <img src="../images/account.svg" class="d-block mx-lg-auto img-fluid" alt="Account" width="700" height="500"
            loading="lazy" />
        </div>
        <div class="container col-12 col-sm-12 col-lg-6">
          <form class="w-100" method="post">
            <h1 class="h1 mb-3 fw-bold text-white">Rejestracja</h1>
            <div class="form-floating my-4">
              <input type="login" class="form-control" id="floatingInput" placeholder="login" name="login" />
              <label class="label-login" for="floatingInput">Login</label>
            </div>
            <?php

            if (isset($_SESSION['e_login'])) {
              echo '<div class="error">' . $_SESSION['e_login'] . '</div>';
              unset($_SESSION['e_login']);
            }

            ?>
            <div class="form-floating">
              <input type="email" class="form-control" id="floatingInput" placeholder="Email" name="email" />
              <label for="floatingInput">Email</label>
            </div>
            <?php

            if (isset($_SESSION['e_email'])) {
              echo '<div class="error">' . $_SESSION['e_email'] . '</div>';
              unset($_SESSION['e_email']);
            }

            ?>
            <div class="form-floating my-4">
              <input type="password" class="form-control" id="floatingInput" placeholder="Password" name="password1" />
              <label for="floatingInput">Hasło</label>
            </div>
            <?php

            if (isset($_SESSION['e_password'])) {
              echo '<div class="error">' . $_SESSION['e_password'] . '</div>';
              unset($_SESSION['e_password']);
            }

            ?>
            <div class="form-floating my-4">
              <input type="password" class="form-control" id="floatingInput" placeholder="Password" name="password2" />
              <label for="floatingInput">Powtórz hasło</label>
            </div>
            <div class="form-check text-start my-3">
              <input class="form-check-input" type="checkbox" value="remember-me" id="flexCheckDefault" name="terms" />
              <label class="form-check-label text-white" for="flexCheckDefault">
                Akceptuję regulamin
              </label>
            </div>
            <?php
            if (isset($_SESSION['e_terms'])) {
              echo '<div class="error">' . $_SESSION['e_terms'] . '</div>';
              unset($_SESSION['e_terms']);
            }

            ?>
            <div class="g-recaptcha" data-sitekey="6LdJi1kqAAAAAKEHlW_V58FfoshJbfQ3bHsi_dxg" data-action="LOGIN"></div>
            <?php

            if (isset($_SESSION['e_bot'])) {
              echo '<div class="error">' . $_SESSION['e_bot'] . '</div>';
              unset($_SESSION['e_bot']);
            }
            ?>
            <br>
            <button class="btn btn-warning w-100 py-3 btn-sign-in text-white" type="submit">
              Zarejestruj
            </button>
          </form>
        </div>
      </div>
    </div>
  </main>
  <div class="container mt-5">
    <footer class="d-flex flex-wrap justify-content-between align-items-center py-1 my-1 border-top">
      <div class="col-md-4 d-flex align-items-center">
        <a href="/" class="mb-3 me-2 mb-md-0 text-body-secondary text-decoration-none lh-1">
          <svg class="bi" width="30" height="24">
            <use xlink:href="#bootstrap"></use>
          </svg>
        </a>
        <span class="mb-3 mb-md-0 text-white">© 2024 Sebastian J</span>
      </div>
      <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
        <li class="ms-3">
          <a class="text-body-secondary" href="https://github.com/SebastianJast"><img
              src="../fonts/github-brands-solid.svg" alt="Github Icon" width="24" height="24" /></a>
        </li>
      </ul>
    </footer>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>