<?php
    include("../functions/pdo_conection.php");
    include("../functions/helpers.php");
    include("../functions/checkSession.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="<?= asset("asset/css/output.css") ?>">
<script>function toggleTheme(){var d=document.documentElement;d.classList.toggle('dark');localStorage.setItem('theme',d.classList.contains('dark')?'dark':'light')}document.documentElement.classList.toggle('dark',localStorage.getItem('theme')==='dark');</script>
   <title>Categories - Admin</title>
</head>
<body class="bg-gray-50 dark:bg-gray-950 transition-colors duration-300">

<?php include "lay/top-nav.php" ?>
<?php include_once('lay/sidebar.php') ?>

<div class="pt-14 md:pl-56 min-h-screen">
    <div class="p-4 sm:p-6 lg:p-8">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 font-[family-name:var(--font-blog-heading)]">Categories</h1>
            <a href="catagories/create.php" class="inline-flex items-center gap-1 bg-amber-400 text-gray-900 px-4 py-2 rounded-full text-sm font-semibold hover:bg-amber-500 transition-colors duration-200">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Category
            </a>
        </div>

        <?php
        global $pdo;
        $q = "SELECT * FROM php_project.`catagories`";
        $statment = $pdo->prepare($q);
        $statment->execute();
        $catagories = $statment->fetchAll();
        ?>

        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-gray-400">#</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-gray-400">Image</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-gray-400">Name</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-600 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($catagories as $catagory) { ?>
                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150">
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400"><?= $catagory->id ?></td>
                            <td class="px-4 py-3">
                                <img src="<?= asset("asset/img/cat/".$catagory->image) ?>" alt="<?= htmlspecialchars($catagory->name) ?>" class="w-12 h-12 rounded-lg object-cover border border-gray-200 dark:border-gray-600">
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200"><?= htmlspecialchars($catagory->name) ?></td>
                            <td class="px-4 py-3 text-right">
                                <a href="<?= url("admin/catagories/edit.php?cat_id=").$catagory->id ?>" class="inline-block bg-blue-500 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-blue-600 transition-colors duration-200">Edit</a>
                                <a href="<?= url("admin/catagories/delete.php?cat_id=").$catagory->id ?>" class="inline-block bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-red-600 transition-colors duration-200 ml-2">Delete</a>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if (count($catagories) === 0) { ?>
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">No categories yet.</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script src="<?= asset("asset/js/admin.js") ?>"></script>
</body>
</html>
