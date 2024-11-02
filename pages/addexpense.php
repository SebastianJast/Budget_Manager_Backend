<?php
session_start();

if (!isset($_SESSION['logged_in'])) {
  header('Location: login.php');
  exit();
}

require_once "connect.php";

mysqli_report(MYSQLI_REPORT_STRICT);

try {
  $connect = new mysqli($host, $db_user, $db_password, $db_name);

  if ($connect->connect_errno != 0) {
    throw new Exception(mysqli_connect_errno());
  } else {
    if (isset($_POST["submit"])) {

      $category = $_POST['category'];
      $amount = $_POST['amount'];
      $pay_method = $_POST['pay_method'];

      if (!is_numeric($amount) || $amount <= 0) {
        $_SESSION['e_amount'] = "Kwota musi być liczbą dodatnią!";
        header("Location: addexpense.php");
        exit();
      }

      $comment = $_POST['comment'];

      $date = $_POST['date'];
      $dateTime = DateTime::createFromFormat('Y-m-d', $date);
      if ($dateTime === false) {
        echo "Błąd: Niepoprawny format daty!";
        exit();
      }
      $format_date = $dateTime->format('Y-m-d');

      $id = $_SESSION['id'];

      $result_category = $connect->query("SELECT id FROM expenses_category_assigned_to_users WHERE name = '$category' AND user_id = '$id'");
      if ($result_category === false) {
        throw new Exception($connect->error);
      }

      $category_id = $result_category->fetch_assoc()['id'];
      if ($category_id === null) {
        echo "Błąd: kategoria nie została znaleziona";
        exit();
      }

      $result_method = $connect->query("SELECT id FROM payment_methods_assigned_to_users WHERE name = '$pay_method' AND user_id = '$id'");
      if ($result_method === false) {
        throw new Exception($connect->error);
      }

      $method_id = $result_method->fetch_assoc()['id'];
      if ($method_id === null) {
        echo "Błąd: metoda płatności nie została znaleziona";
        exit();
      }

      if ($connect->query("INSERT INTO expenses VALUES (NULL, '$id', '$category_id','$method_id' ,'$amount', '$format_date', '$comment')")) {
        $_SESSION['successful_addexpense'] = true;
        header('Location: addexpense_success.php');
      } else {
        throw new Exception($connect->error);
      }
    }
    $connect->close();
  }
} catch (Exception $e) {
  echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o wizytę w innym terminie!</span>';
  // echo '<br />Informacja developerska: ' . $e;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Budget Manager</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  <link rel="stylesheet" href="../css/style.css" />
</head>

<body>
  <header>
    <div class="container col-xxl-10 d-flex justify-content-lg-start justify-content-center">
      <h1 class="display-1 fw-bold text-white lh-1 mt-4">Budget Manager</h1>
    </div>
    <div class="container">
      <nav class="navbar navbar-expand-lg navbar-light">
        <div class="d-flex justify-content-end container-fluid">
          <button class="navbar-toggler bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav flex-lg-row flex-column align-items-center">
              <li class="nav-item px-2">
                <a href="#" class="nav-link text-white" aria-current="page">Strona główna</a>
              </li>
              <li class="nav-item px-2">
                <a href="addincome.php" class="nav-link text-white">Dodaj przychód</a>
              </li>
              <li class="nav-item px-2">
                <a href="addexpense.php" class="nav-link text-white">Dodaj wydatek</a>
              </li>
              <li class="nav-item px-2">
                <a href="balance.php" class="nav-link text-white">Przeglądaj bilans</a>
              </li>
              <li class="nav-item px-2">
                <a href="#" class="nav-link text-white">Ustawienia</a>
              </li>
              <li class="nav-item px-2  ">
                <a href="logout.php" class="nav-link text-white">Wyloguj się</a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </div>
  </header>
  <main>
    <div class="container col-xxl-10 px-1 py-1">
      <div class="row d-flex flex-column flex-lg-row align-items-center">
        <div class="col-12 col-sm-6 col-lg-6 order-2">
          <img src="../images/area_chart.svg" class="d-block mx-lg-auto img-fluid" alt="Account" width="500"
            height="500" loading="lazy" />
        </div>
        <div class="container col-12 col-sm-12 col-lg-6">
          <form class="w-100" method="post">
            <h1 class="h1 mb-3 fw-bold text-white">Wprowadź dane</h1>
            <div class="form-floating my-4">
              <input type="number" class="form-control" id="floatingInput" placeholder="Wprowadź kwotę" name="amount"
                required />
              <label for="floatingInput">Kwota</label>
            </div>
            <?php

            if (isset($_SESSION['e_amount'])) {
              echo '<div class="error">' . $_SESSION['e_amount'] . '</div>';
              unset($_SESSION['e_amount']);
            }

            ?>
            <div class="form-floating my-4">
              <input type="date" class="form-control" id="dateInput" name="date" required />
              <label for="dateInput">Data</label>
            </div>
            <div class="form-floating my-4">
              <select id="categorySelect" class="form-control" name="category" required>
                <option value="">-- Wybierz kategorię --</option>
                <?php
                require_once "connect.php";
                mysqli_report(MYSQLI_REPORT_STRICT);

                try {
                  $connect = new mysqli($host, $db_user, $db_password, $db_name);

                  if ($connect->connect_errno != 0) {
                    throw new Exception(mysqli_connect_error());
                  } else {

                    $id = $_SESSION["id"];

                    $result = $connect->query("SELECT name FROM expenses_category_assigned_to_users WHERE user_id ='$id'");

                    while ($category = $result->fetch_assoc()) {
                      echo '<option value="' . $category['name'] . '">' . $category['name'] . '</option>';
                    }

                    $connect->close();
                  }
                } catch (Exception $e) {
                  echo '<option value="">Błąd ładowania kategorii </option>';
                }
                ?>
              </select>
              <label for="categorySelect">Kategoria</label>
            </div>
            <div class="form-floating my-4">
              <select id="categorySelect" class="form-control" name="pay_method" required>
                <option value="">-- Wybierz metodę --</option>
                <?php
                require_once "connect.php";
                mysqli_report(MYSQLI_REPORT_STRICT);

                try {
                  $connect = new mysqli($host, $db_user, $db_password, $db_name);

                  if ($connect->connect_errno != 0) {
                    throw new Exception(mysqli_connect_error());
                  } else {

                    $id = $_SESSION["id"];

                    $result = $connect->query("SELECT name FROM payment_methods_assigned_to_users WHERE user_id ='$id'");

                    while ($category = $result->fetch_assoc()) {
                      echo '<option value="' . $category['name'] . '">' . $category['name'] . '</option>';
                    }

                    $connect->close();
                  }
                } catch (Exception $e) {
                  echo '<option value="">Błąd ładowania kategorii </option>';
                }
                ?>
              </select>
              <label for="categorySelect">Metoda płatności</label>
            </div>
            <div class="form-floating my-4">
              <input type="text" class="form-control" id="commentInput" placeholder="Komentarz" name="comment" />
              <label for="commentInput">Komentarz</label>
            </div>
            <div class="container d-flex gap-5 px-0">
              <button class="btn btn-danger w-100 py-3 text-white" type="reset">Anuluj</button>
              <button class="btn btn-success w-100 py-3 text-white" type="submit" name="submit">Dodaj</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </main>
  <div class="container">
    <footer class="d-flex flex-wrap justify-content-between align-items-center py-1 my-5 border-top">
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