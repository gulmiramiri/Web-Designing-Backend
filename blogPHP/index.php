<?php
    include("functions/pdo_conection.php");
    include("functions/helpers.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="My Blog - Explore articles and stories across various categories.">
    <title>My Blog - Home</title>
    <link rel="stylesheet" href="<?= asset("asset/css/output.css") ?>">
<script>function toggleTheme(){var d=document.documentElement;d.classList.toggle('dark');localStorage.setItem('theme',d.classList.contains('dark')?'dark':'light')}document.documentElement.classList.toggle('dark',localStorage.getItem('theme')==='dark');</script>
</head>
<body class="bg-gray-50 text-gray-800 dark:bg-gray-950 dark:text-gray-100 antialiased transition-colors duration-300">

<?php require("layouts/top_nav.php") ?>

<main>
    <!-- Hero Section -->
    <section class="relative mt-16 overflow-hidden bg-gradient-to-br from-amber-400 via-amber-300 to-yellow-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 lg:py-32">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <div class="space-y-6">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-gray-100 font-[family-name:var(--font-blog-heading)] leading-tight">
                        Stories that matter
                    </h1>
                    <p class="text-lg sm:text-xl text-gray-700 dark:text-gray-200 max-w-lg">
                        Explore in-depth articles, breaking news, and thoughtful perspectives across every category that matters to you.
                    </p>
                    <a href="<?= url("posts.php") ?>" class="inline-block bg-gray-900 dark:bg-gray-800 text-white px-8 py-3 rounded-full font-medium hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors duration-200 shadow-lg">
                        Browse All Posts
                    </a>
                </div>
                <div class="hidden lg:block">
                    <img src="<?= asset("asset/img/5.jpg") ?>" alt="Blog featured image" class="rounded-2xl shadow-2xl object-cover w-full h-80 lg:h-96" onerror="this.style.display='none'">
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <span class="text-sm font-semibold text-amber-500 uppercase tracking-widest">Browse</span>
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-2 font-[family-name:var(--font-blog-heading)]">All Categories</h2>
        </div>

        <?php
        global $pdo;
        $q = "SELECT * FROM php_project.`catagories`";
        $statment = $pdo->prepare($q);
        $statment->execute();
        $catagories = $statment->fetchAll();

        if (count($catagories) >= 1) {
        ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($catagories as $catagory) { ?>
            <a href="<?= url("catagory.php?id=" . $catagory->id) ?>" class="group block bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300" aria-label="Browse <?= htmlspecialchars($catagory->name) ?> category">
                <div class="aspect-[16/9] overflow-hidden bg-gray-100 dark:bg-gray-800">
                    <img src="<?= asset("asset/img/cat/" . $catagory->image) ?>" alt="<?= htmlspecialchars($catagory->name) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center text-gray-400\'><svg xmlns=\'http://www.w3.org/2000/svg\' width=\'48\' height=\'48\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><rect x=\'3\' y=\'3\' width=\'7\' height=\'7\'/><rect x=\'14\' y=\'3\' width=\'7\' height=\'7\'/><rect x=\'14\' y=\'14\' width=\'7\' height=\'7\'/><rect x=\'3\' y=\'14\' width=\'7\' height=\'7\'/></svg></div>'">
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 font-[family-name:var(--font-blog-heading)] group-hover:text-amber-500 dark:group-hover:text-amber-400 transition-colors duration-200"><?= htmlspecialchars($catagory->name) ?></h3>
                </div>
            </a>
            <?php } ?>
        </div>
        <?php } else { ?>
        <div class="text-center py-20">
            <svg class="mx-auto w-16 h-16 text-gray-300 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            <p class="mt-4 text-xl text-gray-500 dark:text-gray-400 font-medium">No categories yet</p>
            <p class="mt-2 text-gray-400 dark:text-gray-500">Categories will appear here once added.</p>
        </div>
        <?php } ?>
    </section>
</main>

<?php require("layouts/footer.php") ?>

</body>
</html>
