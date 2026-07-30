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
    $q = "SELECT `name` FROM php_project.`catagories` WHERE id=?";
    $statment = $pdo->prepare($q);
    $statment->execute([$_GET["id"]]);
    $catagories = $statment->fetchAll();
    $catName = "Category";
    foreach ($catagories as $catagory) {
        $catName = htmlspecialchars($catagory->name);
    }
    ?>
    <meta name="description" content="Browse <?= $catName ?> posts - My Blog">
    <title><?= $catName ?> - My Blog</title>
    <link rel="stylesheet" href="<?= asset("asset/css/output.css") ?>">
<script>function toggleTheme(){var d=document.documentElement;d.classList.toggle('dark');localStorage.setItem('theme',d.classList.contains('dark')?'dark':'light')}document.documentElement.classList.toggle('dark',localStorage.getItem('theme')==='dark');</script>
</head>
<body class="bg-gray-50 text-gray-800 dark:bg-gray-950 dark:text-gray-100 antialiased transition-colors duration-300">

<?php require("layouts/top_nav.php") ?>

<main class="mt-16">
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <span class="text-sm font-semibold text-amber-500 uppercase tracking-widest">Category</span>
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-gray-100 mt-2 font-[family-name:var(--font-blog-heading)]"><?= $catName ?></h1>
        </div>

        <?php
        global $pdo;
        $q = "SELECT * FROM php_project.`posts` WHERE cat_id=?";
        $statment = $pdo->prepare($q);
        $statment->execute([$_GET["id"]]);
        $catagories = $statment->fetchAll();

        if (count($catagories) >= 1) {
        ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($catagories as $catagory) { ?>
            <a href="<?= url("detail.php?id=" . $catagory->id) ?>" class="group block bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                <div class="aspect-[3/2] overflow-hidden bg-gray-100 dark:bg-gray-800">
                    <img src="<?= asset("asset/img/posts/" . $catagory->img) ?>" alt="<?= htmlspecialchars($catagory->title) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center text-gray-400\'><svg xmlns=\'http://www.w3.org/2000/svg\' width=\'48\' height=\'48\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><path d=\'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z\'/><polyline points=\'14 2 14 8 20 8\'/><line x1=\'16\' y1=\'13\' x2=\'8\' y2=\'13\'/><line x1=\'16\' y1=\'17\' x2=\'8\' y2=\'17\'/></svg></div>'">
                </div>
                <div class="p-5">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 font-[family-name:var(--font-blog-heading)] group-hover:text-amber-500 dark:group-hover:text-amber-400 transition-colors duration-200"><?= htmlspecialchars($catagory->title) ?></h2>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 line-clamp-3"><?= htmlspecialchars($catagory->body) ?></p>
                </div>
            </a>
            <?php } ?>
        </div>
        <?php } else { ?>
        <div class="text-center py-20">
            <svg class="mx-auto w-16 h-16 text-gray-300 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            <p class="mt-4 text-xl text-gray-500 dark:text-gray-400 font-medium">No posts in this category</p>
            <p class="mt-2 text-gray-400 dark:text-gray-500">Posts will appear here once published.</p>
            <a href="<?= url("index.php") ?>" class="mt-6 inline-block text-amber-500 hover:text-amber-600 dark:hover:text-amber-400 font-medium">&larr; Back to categories</a>
        </div>
        <?php } ?>
    </section>
</main>

<?php require("layouts/footer.php") ?>

</body>
</html>
