<?php
session_start();
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
      <div class="container col-xxl-10 d-flex justify-content-lg-start">
        <h1 class="display-1 fw-bold text-white lh-1 mt-4">Budget Manager</h1>
      </div>
    </header>
    <main>
      <div class="container col-xxl-10 px-4 py-1">
        <div class="row d-flex flex-column flex-lg-row align-items-center">
          <div class="col-12 col-sm-6 col-lg-6 order-2">
            <img
              src="../images/account.svg"
              class="d-block mx-lg-auto img-fluid"
              alt="Account"
              width="700"
              height="500"
              loading="lazy"
            />
          </div>
          <div class="container col-12 col-sm-12 col-lg-6">
            <form class="w-100" action="log_in.php" method="post">
              <h1 class="h1 mb-3 fw-bold text-white">Logowanie</h1>
              <div class="form-floating my-4">
                <input
                  type="email"
                  class="form-control"
                  id="floatingInput"
                  placeholder="name@example.com"
                  name="login_email"
                />
                <label class="label-login" for="floatingInput"
                  >Email address</label
                >
              </div>
              <div class="form-floating">
                <input
                  type="password"
                  class="form-control"
                  id="floatingPassword"
                  placeholder="Password"
                  name="password"
                />
                <label for="floatingPassword">Password</label>
              </div>
              <div class="form-check text-start my-3">
                <input
                  class="form-check-input"
                  type="checkbox"
                  value="remember-me"
                  id="flexCheckDefault"
                />
                <label
                  class="form-check-label text-white"
                  for="flexCheckDefault"
                >
                  Remember me
                </label>
              </div>
              <button
                class="btn btn-success w-100 py-3 btn-sign-in"
                type="submit"
              >
                Logowanie
              </button>
            </form>
          </div>
        </div>
      </div>
    </main>
    <div class="container mt-5">
      <footer
        class="d-flex flex-wrap justify-content-between align-items-center py-1 my-1 border-top"
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

        <?php
        if(isset($_SESSION['error'])) echo $_SESSION['error'];
        ?>

      </footer>
    </div>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
  </body>
</html>

