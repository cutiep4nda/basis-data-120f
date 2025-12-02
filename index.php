<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = "";

if (isset($_SESSION["login"])) {
    header("Location: dashboard.php");
    exit;
}

if (isset($_POST['submit'])) {
    if ($_POST['username'] == 'admin' && $_POST['password'] == 'admin') {
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Kredensial salah! Silahkan coba lagi!";
    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - Panti Goceng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">

                <div class="card shadow">
                    <div class="card-body">

                        <h3 class="text-center mb-4">Login Admin</h3>

                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <form action="" method="POST">

                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <button type="submit" name="submit" class="btn btn-primary w-100">
                                Login
                            </button>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

</body>

</html>