<?php
  session_start();

  if((!isset($_SESSION['successful_registration'])))
  {
    header('Location: login.php');
    exit();
  }
  else {
    unset($_SESSION['successful_registration']);
  }

if(isset($_SESSION['fr_login'])) unset($_SESSION['fr_login']);
if(isset($_SESSION['fr_email'])) unset($_SESSION['fr_email']);
if(isset($_SESSION['fr_password1'])) unset($_SESSION['fr_password1']);
if(isset($_SESSION['fr_password2'])) unset($_SESSION['fr_password2']);
if(isset($_SESSION['fr_terms'])) unset($_SESSION['fr_terms']);

if(isset($_SESSION['e_login'])) unset($_SESSION['e_login']);
if(isset($_SESSION['e_email'])) unset($_SESSION['e_email']);
if(isset($_SESSION['e_password'])) unset($_SESSION['e_password']);
if(isset($_SESSION['e_terms'])) unset($_SESSION['e_terms']);
if(isset($_SESSION['e_bot'])) unset($_SESSION['e_bot']);

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Budget Manager</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="../css/style.css" />
  </head>
  <body>
    <header>
      <div
        class="container col-xxl-10 d-flex justify-content-lg-center justify-content-center mb-5" 
      >
        <h1 class="display-1 fw-bold text-white text-center">Budget Manager</h1>
      </div>
    </header>
    <main>
      <div class="px-4 text-center">
        <h2 class="fw-bold text-white mb-5">Dziękujemy za rejestrację! Możesz już zalogować się na swoje konto.</h2>
        <a class="h3 text-success text-decoration-none" href="login.php">Zaloguj się na swoje konto!</a>
      </div>
    </main>
      <footer
      class="d-flex flex-wrap justify-content-between align-items-center py-1 my-5 border-top fixed-bottom"
    >
      <div class="col-md-4 d-flex align-items-center">
        <a
          href="/"
          class="mb-3 me-2 mb-md-0 text-body-secondary text-decoration-none lh-1"
        >
          <svg class="bi" width="30" height="24">
            <use xlink:href="#bootstrap"></use>
          </svg>
        </a>
        <span class="mb-3 mb-md-0 text-white">© 2024 Sebastian J</span>
      </div>
      <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
        <li class="ms-3">
          <a
            class="text-body-secondary"
            href="https://github.com/SebastianJast"
            ><img
              src="../fonts/github-brands-solid.svg"
              alt="Github Icon"
              width="24"
              height="24"
          /></a>
        </li>
      </ul>
    </footer>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
