<?php
    include("../functions/pdo_conection.php");
    include("../functions/helpers.php");

    global $pdo;
    $err = "";

    if (isset($_POST["first_name"]) && $_POST["first_name"] !=""
    && isset($_POST["last_name"]) && $_POST["last_name"] !=""
    && isset($_POST["email"]) && $_POST["email"] !=""
    && isset($_POST["password"]) && $_POST["password"] !=""
    && isset($_POST["retype_password"]) && $_POST["retype_password"] !=""
    ){
        if($_POST["password"] === $_POST["retype_password"]){
            if(strlen($_POST["password"]) > 5){
                $q = "SELECT * FROM users WHERE email=?";
                $statment = $pdo->prepare($q);
                $statment->execute([$_POST["email"]]);
                $re = $statment->fetch();

                if ($re === false){
                    $password = password_hash($_POST["password"] , PASSWORD_DEFAULT);
                    $q = "INSERT INTO php_project.`users` SET first_name=? , `last_name`=? , email=? , `password`=?, created_at= NOW() ";
                    $statment = $pdo->prepare($q);
                    $statment->execute([$_POST["first_name"] , $_POST["last_name"] , $_POST["email"] , $password]);
                    redirect("auth/login.php");
                } else {
                    $err = "ایمیل شما باید تکراری نباشد ";
                }
            } else {
                $err = "پسورد شما حدالقل باید ۵ کارکتر باشد!";
            }
        } else {
            $err = "پسورد با تکرارش یکسان نیست";
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
    <meta name="description" content="Create an account - My Blog">
    <title>Sign Up - My Blog</title>
    <link rel="stylesheet" href="<?= asset("asset/css/output.css") ?>">
<script>function toggleTheme(){var d=document.documentElement;d.classList.toggle('dark');localStorage.setItem('theme',d.classList.contains('dark')?'dark':'light')}document.documentElement.classList.toggle('dark',localStorage.getItem('theme')==='dark');</script>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-950 flex items-center justify-center px-4 py-8 transition-colors duration-300">

    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8">
            <div class="text-center mb-8">
                <a href="<?= url("index.php") ?>" class="text-2xl font-bold text-gray-900 dark:text-gray-100 font-[family-name:var(--font-blog-heading)]">My Blog</a>
                <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mt-4">Create an account</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Join us and start managing your blog</p>
            </div>

            <?php if ($err) { ?>
            <div class="mb-6 p-3 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm text-center" role="alert">
                <?= htmlspecialchars($err) ?>
            </div>
            <?php } ?>

            <form action="<?= url("auth/register.php") ?>" method="post" class="space-y-4" novalidate>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="reg-first-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First Name</label>
                        <input type="text" id="reg-first-name" name="first_name" required autocomplete="given-name" placeholder="John" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition-all duration-200">
                    </div>
                    <div>
                        <label for="reg-last-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name</label>
                        <input type="text" id="reg-last-name" name="last_name" required autocomplete="family-name" placeholder="Doe" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition-all duration-200">
                    </div>
                </div>

                <div>
                    <label for="reg-email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" id="reg-email" name="email" required autocomplete="email" placeholder="you@example.com" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition-all duration-200">
                </div>

                <div>
                    <label for="reg-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                    <input type="password" id="reg-password" name="password" required minlength="6" autocomplete="new-password" placeholder="At least 6 characters" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition-all duration-200">
                </div>

                <div>
                    <label for="reg-retype-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm Password</label>
                    <input type="password" id="reg-retype-password" name="retype_password" required minlength="6" autocomplete="new-password" placeholder="Repeat your password" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition-all duration-200">
                </div>

                <button type="submit" class="w-full bg-amber-400 text-gray-900 font-semibold py-2.5 rounded-xl hover:bg-amber-500 focus:ring-2 focus:ring-amber-300 focus:outline-none transition-all duration-200 cursor-pointer">Sign Up</button>
            </form>

            <div class="mt-6 text-center space-y-2">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Already have an account?
                    <a href="<?= url("auth/login.php") ?>" class="text-amber-500 hover:text-amber-600 dark:hover:text-amber-400 font-medium">Log in</a>
                </p>
                <a href="<?= url("index.php") ?>" class="text-sm text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200">&larr; Back to home</a>
            </div>
        </div>
    </div>

<script src="<?= asset("asset/js/app.js") ?>"></script>
</body>
</html>
