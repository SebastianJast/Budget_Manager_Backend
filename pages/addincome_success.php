<?php
session_start();

if ((!isset($_SESSION['successful_addincome']))) {
  header('Location: addincome.php');
  exit();
} else {
  unset($_SESSION['successful_addincome']);
}

if (isset($_SESSION['e_amount']))
  unset($_SESSION['e_amount']);
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
              <li class="nav-item px-2">
                <a href="logout.php" class="nav-link text-white">Wyloguj się</a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </div>
  </header>
  <main>
    <h2 class="fw-bold mb-5 text-center mt-5 text-success">Przychód został dodany!</h2>
  </main>
  <div class="container">
    <footer class="d-flex flex-wrap justify-content-between align-items-center py-1 my-5 border-top fixed-bottom">
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