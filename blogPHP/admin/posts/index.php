<?php
    include("../../functions/pdo_conection.php");
    include("../../functions/helpers.php");
    include("../../functions/checkSession.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="<?= asset("asset/css/output.css") ?>">
<script>function toggleTheme(){var d=document.documentElement;d.classList.toggle('dark');localStorage.setItem('theme',d.classList.contains('dark')?'dark':'light')}document.documentElement.classList.toggle('dark',localStorage.getItem('theme')==='dark');</script>
   <title>Posts - Admin</title>
</head>
<body class="bg-gray-50 dark:bg-gray-950 transition-colors duration-300">

<?php include "../lay/top-nav.php" ?>
<?php include_once('../lay/sidebar.php') ?>

<div class="pt-14 md:pl-56 min-h-screen">
    <div class="p-4 sm:p-6 lg:p-8">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 font-[family-name:var(--font-blog-heading)]">Posts</h1>
            <a href="<?= url("admin/posts/create.php") ?>" class="inline-flex items-center gap-1 bg-amber-400 text-gray-900 px-4 py-2 rounded-full text-sm font-semibold hover:bg-amber-500 transition-colors duration-200">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Post
            </a>
        </div>

        <?php
        global $pdo;
        $q = "SELECT *, posts.id, `catagories`.`name` FROM `posts` LEFT JOIN `catagories` ON `posts`.cat_id = `catagories`.id";
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
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-gray-400">Title</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-gray-400">Category</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-gray-400">Status</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-600 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($catagories as $post) { ?>
                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150">
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400"><?= $post->id ?></td>
                            <td class="px-4 py-3">
                                <img src="<?= asset("asset/img/posts/".$post->img) ?>" alt="<?= htmlspecialchars($post->title) ?>" class="w-14 h-10 rounded-lg object-cover border border-gray-200 dark:border-gray-600">
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200 max-w-[200px] truncate"><?= htmlspecialchars($post->title) ?></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($post->name) ?></td>
                            <td class="px-4 py-3">
                                <?php if ($post->status == 10) { ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Enabled</span>
                                <?php } else { ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">Disabled</span>
                                <?php } ?>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="<?= url("admin/posts/change-status.php?") . "i=" . $post->id . "&s=" . $post->status ?>" class="inline-block bg-yellow-400 text-gray-900 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-yellow-500 transition-colors duration-200">Toggle</a>
                                <a href="<?= url("admin/posts/edit.php?i=").$post->id ?>" class="inline-block bg-blue-500 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-blue-600 transition-colors duration-200 ml-1">Edit</a>
                                <a href="<?= url("admin/posts/delete.php?i=").$post->id ?>" class="inline-block bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-red-600 transition-colors duration-200 ml-1">Delete</a>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if (count($catagories) === 0) { ?>
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">No posts yet.</td>
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
