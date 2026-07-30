<?php
    include("../../functions/pdo_conection.php");
    include("../../functions/helpers.php");
    include("../../functions/checkSession.php");

    if (isset($_POST["cat_name"]) && $_POST["cat_name"] !="" 
    &&  isset($_FILES["image"]) && $_FILES["image"]["name"] !=""
    ){

        $bas = "../../asset/img/cat/";
        $allowMimes = ["png" , "jpeg" , "jpg" , "gif"];
        $imageMime = pathinfo($_FILES["image"]["name"] , PATHINFO_EXTENSION);
        $image =  date("Y_m_d_H_i_s").".".$imageMime;

        $imageUpload = move_uploaded_file($_FILES["image"]["tmp_name"] , $bas . $image);

        if (!in_array($imageMime , $allowMimes)){
           redirect("admin/catagories/create.php?err=er");
        }

        echo $image;

        global $pdo;
        $q = "INSERT INTO php_project.`catagories` (`name`, `image` , `created_at`) VALUES (?,? , NOW())";
        $statment = $pdo->prepare($q);
        $statment->execute([$_POST["cat_name"],$image]);
        redirect("admin/");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="<?= asset("asset/css/output.css") ?>">
<script>function toggleTheme(){var d=document.documentElement;d.classList.toggle('dark');localStorage.setItem('theme',d.classList.contains('dark')?'dark':'light')}document.documentElement.classList.toggle('dark',localStorage.getItem('theme')==='dark');</script>
   <title>Create Category - Admin</title>
</head>
<body class="bg-gray-50 dark:bg-gray-950 transition-colors duration-300">

<?php include "../lay/top-nav.php" ?>
<?php include_once('../lay/sidebar.php') ?>

<div class="pt-14 md:pl-56 min-h-screen">
    <div class="p-4 sm:p-6 lg:p-8">

        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 font-[family-name:var(--font-blog-heading)] mb-6">Create Category</h1>

        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 max-w-lg">
            <form action="<?= url("admin/catagories/create.php") ?>" method="post" enctype="multipart/form-data" class="space-y-5">
                <div>
                    <label for="cat-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                    <input type="text" id="cat-name" name="cat_name" required placeholder="Category name" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition-all duration-200">
                </div>

                <div>
                    <label for="cat-image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Photo</label>
                    <input type="file" id="cat-image" name="image" accept="image/png,image/jpeg,image/jpg,image/gif" required class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-amber-50 dark:file:bg-amber-900/30 file:text-amber-600 dark:file:text-amber-400 hover:file:bg-amber-100 dark:hover:file:bg-amber-800/50 transition-colors duration-200">
                </div>

                <button type="submit" class="bg-amber-400 text-gray-900 font-semibold px-6 py-2.5 rounded-xl hover:bg-amber-500 focus:ring-2 focus:ring-amber-300 focus:outline-none transition-all duration-200 cursor-pointer">Create</button>
            </form>
        </div>

    </div>
</div>

<script src="<?= asset("asset/js/admin.js") ?>"></script>
</body>
</html>
