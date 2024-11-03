<?php

session_start();

if (!isset($_SESSION['logged_in'])) {
  header('Location: login.php');
  exit();
}

//okres przychodów i wydatków
$first_day_month = date('Y-m-01');
$last_day_month = date('Y-m-t');
$title = "Bieżący miesiąc";


if (isset($_POST["previous_month"])) {
  $first_day_month = date('Y-m-d', strtotime('first day of last month'));
  $last_day_month = date('Y-m-d', strtotime('last day of last month'));
  $title = "Poprzedni miesiąc";
}

if (isset($_POST["current_year"])) {
  $first_day_month = date('Y-01-01');
  $last_day_month = date('Y-12-t');
  $title = "Bieżący rok";
}

if (isset($_POST["submit_date"])) {
  $date1 = $_POST['range_from'];
  $dateTime1 = DateTime::createFromFormat('Y-m-d', $date1);
  if ($dateTime1 === false) {
    echo "Błąd: Niepoprawny format daty!";
    exit();
  }
  $format_date1 = $dateTime1->format('Y-m-d');
  $first_day_month = $format_date1;

  $date2 = $_POST['range_to'];
  $dateTime2 = DateTime::createFromFormat('Y-m-d', $date2);
  if ($dateTime2 === false) {
    echo "Błąd: Niepoprawny format daty!";
    exit();
  }
  $format_date2 = $dateTime2->format('Y-m-d');
  $last_day_month = $format_date2;
  $title = 'Okres od ' . $first_day_month . ' do ' . $last_day_month;

}

require_once "connect.php";
mysqli_report(MYSQLI_REPORT_STRICT);

try {
  $connect = new mysqli($host, $db_user, $db_password, $db_name);

  if ($connect->connect_errno != 0) {
    throw new Exception(mysqli_connect_error());
  } else {

    $id = $_SESSION["id"];

    // suma wydatków w zależności od zakresu daty i kategorii
    $result = $connect->query("SELECT expenses_category_assigned_to_users.name, SUM(expenses.amount) AS 'expensesSUM' FROM expenses
    INNER JOIN expenses_category_assigned_to_users ON expenses_category_assigned_to_users.user_id = expenses.user_id
    WHERE expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id 
    AND expenses.date_of_expense BETWEEN '$first_day_month' AND '$last_day_month'
    AND expenses.user_id = '$id'
    GROUP BY expenses.expense_category_assigned_to_user_id
    ORDER BY expensesSUM DESC");
    if ($result === false) {
      throw new Exception($connect->error);
    }

    $dataPoints = array();
    $how_many_expenses = $result->num_rows;
    if ($how_many_expenses > 0) {
      while ($row = $result->fetch_assoc()) {
        $dataPoints[] = array("label" => $row['name'], "y" => $row['expensesSUM']);
      }
    } else {
      $dataPoints = array(array("label" => "Zero expenses", "y" => 100));
    }

    $connect->close();
  }
} catch (Exception $e) {
  echo '<option>Błąd ładowania wydatku </option>';
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

<!-- wykres wydaków w js -->
<script>
  window.onload = function () {

    var chart = new CanvasJS.Chart("chartContainer", {
      animationEnabled: true,
      exportEnabled: true,
      title: {
        text: "Twoje wydatki z wybranego okresu"
      },
      subtitles: [{
        text: "Używana waluta (PLN)"
      }],
      data: [{
        type: "pie",
        showInLegend: "true",
        legendText: "{label}",
        indexLabelFontSize: 16,
        indexLabel: "{label} - #percent%",
        yValueFormatString: "PLN #,##0",
        dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
      }]
    });
    chart.render();
  }

</script>

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
                  <form action="" method="post">
                    <button type="submit" class="dropdown-item" name="current_month" value="current_month">Bieżący
                      miesiąc</button>
                    <button type="submit" class="dropdown-item" name="previous_month" value="previous_month">Poprzedni
                      miesiąc</button>
                    <button type="submit" class="dropdown-item" name="current_year" value="current_year">Bieżący
                      rok</button>
                    <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#exampleModal"
                      href="#">Niestandardowy</a>
                  </form>
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
      <?php echo $title ?>
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

                  //wybierz przychód w zależności od zakresu daty
                  $result = $connect->query("SELECT incomes.amount, incomes.date_of_income, incomes.income_comment, incomes_category_assigned_to_users.name AS 'category' FROM incomes
                    INNER JOIN incomes_category_assigned_to_users ON incomes_category_assigned_to_users.user_id = incomes.user_id
                    WHERE incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id 
                    AND incomes.user_id = '$id' 
                    AND incomes.date_of_income BETWEEN '$first_day_month' AND '$last_day_month'");
                  if ($result === false) {
                    throw new Exception($connect->error);
                  }

                  while ($row = $result->fetch_assoc()) {
                    echo '<li class="fw-bold py-2">' . $row['category'] . ': ' . round($row['amount']) . '</li>';
                    echo '<li>' . $row['date_of_income'] . ' ' . $row['income_comment'] . ' ' . '<span><img class="pen" src="../fonts/pen-solid.svg" alt="pen" height="15" width="15" /></span><span><img
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
              <?php

              require_once "connect.php";
              mysqli_report(MYSQLI_REPORT_STRICT);

              try {
                $connect = new mysqli($host, $db_user, $db_password, $db_name);

                if ($connect->connect_errno != 0) {
                  throw new Exception(mysqli_connect_error());
                } else {

                  $id = $_SESSION["id"];

                  //wybierz wydatek w zależności od zakresu daty
                  $result = $connect->query("SELECT expenses.amount, expenses.date_of_expense, expenses.expense_comment, expenses_category_assigned_to_users.name AS 'category' FROM expenses
                    INNER JOIN expenses_category_assigned_to_users ON expenses_category_assigned_to_users.user_id = expenses.user_id
                    WHERE expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id 
                    AND expenses.user_id = '$id' 
                    AND expenses.date_of_expense BETWEEN '$first_day_month' AND '$last_day_month'");
                  if ($result === false) {
                    throw new Exception($connect->error);
                  }

                  while ($row = $result->fetch_assoc()) {
                    echo '<li class="fw-bold py-2">' . $row['category'] . ': ' . round($row['amount']) . '</li>';
                    echo '<li>' . $row['date_of_expense'] . ' ' . $row['expense_comment'] . ' ' . '<span><img class="pen" src="../fonts/pen-solid.svg" alt="pen" height="15" width="15" /></span><span><img
                    class="trash" src="../fonts/trash-can-solid.svg" alt="trash" height="15" width="15" /></span>' . '</li>';
                  }

                  $connect->close();
                }
              } catch (Exception $e) {
                echo '<option value="">Błąd ładowania wydatku </option>';
              }
              ?>
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
              <?php

              require_once "connect.php";
              mysqli_report(MYSQLI_REPORT_STRICT);

              try {
                $connect = new mysqli($host, $db_user, $db_password, $db_name);

                if ($connect->connect_errno != 0) {
                  throw new Exception(mysqli_connect_error());
                } else {

                  $id = $_SESSION["id"];

                  //suma przychodów w zależności od zakresu daty
                  $result = $connect->query("SELECT incomes.user_id, SUM(incomes.amount) AS 'incomesSUM' FROM incomes
                  WHERE incomes.date_of_income BETWEEN '$first_day_month' AND '$last_day_month' AND incomes.user_id = '$id'");
                  if ($result === false) {
                    throw new Exception($connect->error);
                  }

                  $row = $result->fetch_assoc();

                  $incomesSUM = $row['incomesSUM'];

                  //suma wydatków w zależności od zakresu daty
                  $result = $connect->query("SELECT expenses.user_id, SUM(expenses.amount) AS 'expensesSUM' FROM expenses
                  WHERE expenses.date_of_expense BETWEEN '$first_day_month' AND '$last_day_month' AND expenses.user_id = '$id'");
                  if ($result === false) {
                    throw new Exception($connect->error);
                  }

                  $row = $result->fetch_assoc();

                  $expensesSUM = $row['expensesSUM'];

                  $balance = $incomesSUM - $expensesSUM;

                  echo '<li class="fw-bold py-2">' . 'Bilans: ' . $balance . '</li>';

                  $connect->close();
                }
              } catch (Exception $e) {
                echo '<option value="">Błąd ładowania wydatku </option>';
              }

              if ($balance > 0) {
                echo '<li class="text-success" fw-bold> Gratulacje. Świetnie zarządzasz finansami! </li>';
              } elseif ($balance == 0) {
                echo '<li class="text-warning" fw-bold> Bilans wynosi zero - warto przemyśleć oszczędności. </li>';
              } else {
                echo '<li class="text-danger" fw-bold> Ostrożnie! Przekroczyłeś budżet – czas na oszczędności </li>';
              }

              ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="mx-auto col-xxl-8 col-md-8 col-sm-12">
      <div id="chartContainer" style="height: 370px; width: 100%;"></div>
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Wybierz zakres dat:</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="post">
            <div class="modal-body">
              <p class="my-2">Zakres od:</p>
              <div class="form-floating my-1">
                <input type="date" class="form-control" id="dateInput" name="range_from" required />
                <label for="dateInput">Data</label>
              </div>
              <p class="my-2">Zakres do:</p>
              <div class="form-floating my-1">
                <input type="date" class="form-control" id="dateInput" name="range_to" required />
                <label for="dateInput">Data</label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                Close
              </button>
              <button type="submit" class="btn btn-primary" name="submit_date">
                Ok
              </button>
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
  <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
  <script src="../js/index.js"></script>
</body>

</html>