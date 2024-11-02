<?php

session_start();

if (!isset($_SESSION['logged_in'])) {
  header('Location: login.php');
  exit();
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
                <a href="main_page.php" class="nav-link text-white" aria-current="page">Strona główna</a>
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
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button"
                  data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Wybierz okres
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item" href="#">Bieżacy miesiąc</a>
                  <a class="dropdown-item" href="#">Poprzedni miesiąc</a>
                  <a class="dropdown-item" href="#">Bieżący rok</a>
                  <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#exampleModal"
                    href="#">Niestandardowy</a>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </div>
  </header>
  <main>
    <h2 class="display-5 fw-bold text-white lh-1 mt-4 text-center mb-4">
      Bieżacy miesiąc
    </h2>
    <div
      class="row d-flex flex-column flex-lg-row justify-content-center align-items-center gap-4 row-cols-1 row-cols-md-3 mb-3 text-center">
      <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm">
          <div class="card-header py-3">
            <h3 class="my-0 fw-normal">Przychody</h3>
          </div>
          <div class="card-body">
            <ul class="list-unstyled mt-1 mb-4">
              <?php
              require_once "connect.php";
              mysqli_report(MYSQLI_REPORT_STRICT);

              try {
                $connect = new mysqli($host, $db_user, $db_password, $db_name);

                if ($connect->connect_errno != 0) {
                  throw new Exception(mysqli_connect_error());
                } else {

                  $id = $_SESSION["id"];

                  $result = $connect->query("SELECT incomes.amount, incomes.date_of_income, incomes.income_comment, incomes_category_assigned_to_users.name AS 'category' FROM incomes
                    INNER JOIN incomes_category_assigned_to_users ON incomes_category_assigned_to_users.user_id = incomes.user_id
                    WHERE incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id AND incomes.user_id = '$id'");

                  while ($row = $result->fetch_assoc()) {
                    echo '<li class="fw-bold py-2">' . $row['category'] . ': ' . $row['amount'] . '</li>';
                    echo '<li>' . $row['date_of_income'] . ' ' . $row['income_comment'] . '<span><img class="pen" src="../fonts/pen-solid.svg" alt="pen" height="15" width="15" /></span><span><img
                    class="trash" src="../fonts/trash-can-solid.svg" alt="trash" height="15" width="15" /></span>' . '</li>';
                  }

                  $connect->close();
                }
              } catch (Exception $e) {
                echo '<option value="">Błąd ładowania przychodu </option>';
              }
              ?>
            </ul>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card mb-4 rounded-3 shadow-sm">
          <div class="card-header py-3">
            <h3 class="my-0 fw-normal">Wydatki</h3>
          </div>
          <div class="card-body">
            <ul class="list-unstyled mt-1 mb-4">
              <li class="fw-bold py-2">Ubranie: 500</li>
              <li>
                2024-09-28 500 kurtka zimowa
                <span><img class="pen" src="../fonts/pen-solid.svg" alt="pen" height="15" width="15" /></span><span><img
                    class="trash" src="../fonts/trash-can-solid.svg" alt="trash" height="15" width="15" /></span>
              </li>
              <li class="fw-bold py-2">Wycieczka: 400</li>
              <li>
                2024-09-28 400
                <span><img class="pen" src="../fonts/pen-solid.svg" alt="pen" height="15" width="15" /></span><span><img
                    class="trash" src="../fonts/trash-can-solid.svg" alt="trash" height="15" width="15" /></span>
              </li>
              <li class="fw-bold py-2">Jedzenie: 300</li>
              <li>
                2024-09-28 300
                <span><img class="pen" src="../fonts/pen-solid.svg" alt="pen" height="15" width="15" /></span><span><img
                    class="trash" src="../fonts/trash-can-solid.svg" alt="trash" height="15" width="15" /></span>
              </li>
              <li class="fw-bold py-2">Rozrywka: 200</li>
              <li>
                2024-09-28 200 Wyjazd na narty
                <span><img class="pen" src="../fonts/pen-solid.svg" alt="pen" height="15" width="15" /></span><span><img
                    class="trash" src="../fonts/trash-can-solid.svg" alt="trash" height="15" width="15" /></span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="d-flex justify-content-center text-center">
      <div class="col-xxl-8 col-md-8 col-sm-12 mx-lg-auto">
        <div class="card mb-4 rounded-3 shadow-sm">
          <div class="card-body">
            <ul class="list-unstyled mt-1 mb-4">
              <li class="fw-bold py-2">Bilans: 5900</li>
              <li class="text-success fw-bold">
                Gratulacje. Świetnie zarządzasz finansami!
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="mx-auto col-xxl-8 col-md-8 col-sm-12">
      <div id="chartContainer" style="height: 300px; width: 100%"></div>
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Wybierz zakres dat:</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="my-2">Zakres od:</p>
            <div class="form-floating my-1">
              <input type="date" class="form-control" id="dateInput" required />
              <label for="dateInput">Data</label>
            </div>
            <p class="my-2">Zakres do:</p>
            <div class="form-floating my-1">
              <input type="date" class="form-control" id="dateInput" required />
              <label for="dateInput">Data</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              Close
            </button>
            <button type="button" class="btn btn-primary">
              Ok
            </button>
          </div>
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
  <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
  <script src="../js/index.js"></script>
</body>

</html>