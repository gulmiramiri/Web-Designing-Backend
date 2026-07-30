<?php
    include("functions/pdo_conection.php");
    include("functions/helpers.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    global $pdo;
    $q = "SELECT `title` FROM php_project.`posts` WHERE id=?";
    $statment = $pdo->prepare($q);
    $statment->execute([$_GET["id"]]);
    $catagories = $statment->fetchAll();
    $pageTitle = "Post";
    foreach ($catagories as $catagory) {
        $pageTitle = htmlspecialchars($catagory->title);
    }
    ?>
    <meta name="description" content="<?= $pageTitle ?> - My Blog">
    <title><?= $pageTitle ?> - My Blog</title>
    <link rel="stylesheet" href="<?= asset("asset/css/output.css") ?>">
<script>function toggleTheme(){var d=document.documentElement;d.classList.toggle('dark');localStorage.setItem('theme',d.classList.contains('dark')?'dark':'light')}document.documentElement.classList.toggle('dark',localStorage.getItem('theme')==='dark');</script>
</head>
<body class="bg-gray-50 text-gray-800 dark:bg-gray-950 dark:text-gray-100 antialiased transition-colors duration-300">

<?php require("layouts/top_nav.php") ?>

<main class="mt-16">
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <?php
        global $pdo;
        $q = "SELECT * FROM php_project.`posts` WHERE id=?";
        $statment = $pdo->prepare($q);
        $statment->execute([$_GET["id"]]);
        $catagories = $statment->fetchAll();

        if (count($catagories) >= 1) {
            foreach ($catagories as $catagory) {
        ?>
        <article>
            <header class="mb-8">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-gray-100 font-[family-name:var(--font-blog-heading)] leading-tight"><?= htmlspecialchars($catagory->title) ?></h1>
            </header>

            <div class="aspect-[16/7] overflow-hidden bg-gray-100 dark:bg-gray-800 rounded-2xl mb-10 shadow-md">
                <img src="<?= asset("asset/img/posts/" . $catagory->img) ?>" alt="<?= htmlspecialchars($catagory->title) ?>" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center text-gray-300\'><svg xmlns=\'http://www.w3.org/2000/svg\' width=\'64\' height=\'64\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><rect x=\'3\' y=\'3\' width=\'18\' height=\'18\' rx=\'2\' ry=\'2\'/><circle cx=\'8.5\' cy=\'8.5\' r=\'1.5\'/><polyline points=\'21 15 16 10 5 21\'/></svg></div>'">
            </div>

            <div class="prose prose-gray max-w-none">
                <p class="text-lg leading-relaxed text-gray-700 dark:text-gray-300 whitespace-pre-line"><?= htmlspecialchars($catagory->body) ?></p>
            </div>
        </article>
        <?php
            }
        } else {
        ?>
        <div class="text-center py-20">
            <svg class="mx-auto w-16 h-16 text-gray-300 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <p class="mt-4 text-xl text-gray-500 dark:text-gray-400 font-medium">Post not found</p>
            <a href="<?= url("posts.php") ?>" class="mt-4 inline-block text-amber-500 hover:text-amber-600 font-medium">&larr; Back to posts</a>
        </div>
        <?php } ?>
    </section>
</main>

<?php require("layouts/footer.php") ?>

</body>
</html>
