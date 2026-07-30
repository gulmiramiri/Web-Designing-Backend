<?php
    include("../functions/pdo_conection.php");
    include("../functions/helpers.php");

    session_start();
    global $pdo;
    $err = "";

    if (isset($_POST["email"]) && $_POST["email"] !=""
    && isset($_POST["password"]) && $_POST["password"] !=""
    ){
        $pass = "";
        $email = "";
        $first = "";

        $q = "SELECT * FROM users WHERE email=?";
        $statment = $pdo->prepare($q);
        $statment->execute([$_POST["email"]]);
        $re = $statment->fetchAll();

        foreach($re as $r){
            $pass = $r->password;
            $email = $r->email;
            $first = $r->first_name;
        }

        if (count($re) >= 1){
            if(password_verify($_POST["password"] , $pass)){
                $_SESSION["user"] = $first;
                redirect("admin/posts");
            } else {
                $err = "پسورد شما اشتباه است";
            }
        } else {
            $err = "ایمیل شما اشتباه هست";
        }
    } else {
        if(!empty($_POST)){
            $err = "فیلد ها باید پر شود";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Log in to My Blog admin panel">
    <title>Log In - My Blog</title>
    <link rel="stylesheet" href="<?= asset("asset/css/output.css") ?>">
<script>function toggleTheme(){var d=document.documentElement;d.classList.toggle('dark');localStorage.setItem('theme',d.classList.contains('dark')?'dark':'light')}document.documentElement.classList.toggle('dark',localStorage.getItem('theme')==='dark');</script>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-950 flex items-center justify-center px-4 transition-colors duration-300">

    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8">
            <div class="text-center mb-8">
                <a href="<?= url("index.php") ?>" class="text-2xl font-bold text-gray-900 dark:text-gray-100 font-[family-name:var(--font-blog-heading)]">My Blog</a>
                <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mt-4">Welcome back</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Log in to manage your blog</p>
            </div>

            <?php if ($err) { ?>
            <div class="mb-6 p-3 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm text-center" role="alert">
                <?= htmlspecialchars($err) ?>
            </div>
            <?php } ?>

            <form action="<?= url("auth/login.php") ?>" method="post" class="space-y-5" novalidate>
                <div>
                    <label for="login-email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" id="login-email" name="email" required autocomplete="email" placeholder="you@example.com" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition-all duration-200">
                </div>

                <div>
                    <label for="login-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                    <input type="password" id="login-password" name="password" required autocomplete="current-password" placeholder="Enter your password" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition-all duration-200">
                </div>

                <button type="submit" class="w-full bg-amber-400 text-gray-900 font-semibold py-2.5 rounded-xl hover:bg-amber-500 focus:ring-2 focus:ring-amber-300 focus:outline-none transition-all duration-200 cursor-pointer">Log In</button>
            </form>

            <div class="mt-6 text-center space-y-2">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Don't have an account?
                    <a href="<?= url("auth/register.php") ?>" class="text-amber-500 hover:text-amber-600 dark:hover:text-amber-400 font-medium">Sign up</a>
                </p>
                <a href="<?= url("index.php") ?>" class="text-sm text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200">&larr; Back to home</a>
            </div>
        </div>
    </div>

<script src="<?= asset("asset/js/app.js") ?>"></script>
</body>
</html>
