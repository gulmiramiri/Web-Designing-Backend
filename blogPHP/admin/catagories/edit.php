<?php
    include("../../functions/pdo_conection.php");
    include("../../functions/helpers.php");
    include("../../functions/checkSession.php");

    if (isset($_POST["cat_name"]) && $_POST["cat_name"] !="" ){
        if(isset($_GET["cat_id"])){
            global $pdo;
            $q = "UPDATE php_project.`catagories` SET `name` = ? , `updated_at` = now() WHERE id = ?";
            $statment = $pdo->prepare($q);
            $statment->execute([$_POST["cat_name"] , $_GET["cat_id"]]);
            redirect("admin/");
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="<?= asset("asset/css/output.css") ?>">
<script>function toggleTheme(){var d=document.documentElement;d.classList.toggle('dark');localStorage.setItem('theme',d.classList.contains('dark')?'dark':'light')}document.documentElement.classList.toggle('dark',localStorage.getItem('theme')==='dark');</script>
   <title>Edit Category - Admin</title>
</head>
<body class="bg-gray-50 dark:bg-gray-950 transition-colors duration-300">

<?php include "../lay/top-nav.php" ?>
<?php include_once('../lay/sidebar.php') ?>

<div class="pt-14 md:pl-56 min-h-screen">
    <div class="p-4 sm:p-6 lg:p-8">

        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 font-[family-name:var(--font-blog-heading)] mb-6">Edit Category</h1>

        <?php
        global $pdo;
        if (isset($_GET["cat_id"])) {
            $cat_id = $_GET["cat_id"];
            $q = "SELECT * FROM `catagories` WHERE id = ?";
            $statment = $pdo->prepare($q);
            $statment->execute([$cat_id]);
            $catagories = $statment->fetchAll();

            foreach ($catagories as $catagory) {
        ?>

        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 max-w-lg">
            <form action="#" method="post" class="space-y-5">
                <div>
                    <label for="edit-cat-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                    <input type="text" id="edit-cat-name" name="cat_name" required value="<?= htmlspecialchars($catagory->name) ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition-all duration-200">
                </div>
                <button type="submit" class="bg-amber-400 text-gray-900 font-semibold px-6 py-2.5 rounded-xl hover:bg-amber-500 focus:ring-2 focus:ring-amber-300 focus:outline-none transition-all duration-200 cursor-pointer">Update</button>
            </form>
        </div>

        <?php
            }
        }
        ?>

    </div>
</div>

<script src="<?= asset("asset/js/admin.js") ?>"></script>
</body>
</html>
