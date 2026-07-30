<?php
    include("../../functions/pdo_conection.php");
    include("../../functions/helpers.php");
    include("../../functions/checkSession.php");

    if ( (isset($_POST["title"]) && $_POST["title"] !="") 
     &&  (isset($_POST["cat_id"]) && $_POST["cat_id"] !="")
     &&  (isset($_POST["body"]) && $_POST["body"] !="")
     &&  (isset($_FILES["image"]) && $_FILES["image"]["name"] !="")
    ){
        global $pdo;
        $q = "SELECT * FROM catagories WHERE id=?";
        $statment = $pdo->prepare($q);
        $statment->execute([$_POST["cat_id"]]);
        $re = $statment->fetchAll();
        if(!count($re) >+ 1){
            redirect("admin/posts/create.php");
        }

        $bas = "../../asset/img/posts/";
        $allowMimes = ["png" , "jpeg" , "jpg" , "gif"];
        $imageMime = pathinfo($_FILES["image"]["name"] , PATHINFO_EXTENSION);
        $image =  date("Y_m_d_H_i_s").".".$imageMime;

        $imageUpload = move_uploaded_file($_FILES["image"]["tmp_name"] , $bas . $image);

        if (!in_array($imageMime , $allowMimes)){
           redirect("admin/posts/create.php?err=er");
        }

        global $pdo;
        $q = "INSERT INTO php_project.`posts` SET title=? , `body`=? , cat_id=? ,`status` =10, img=?, created_at= NOW() ";
        $statment = $pdo->prepare($q);
        $statment->execute([$_POST["title"] , $_POST["body"] , $_POST["cat_id"] , $image]);
        redirect("admin/posts");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="<?= asset("asset/css/output.css") ?>">
<script>function toggleTheme(){var d=document.documentElement;d.classList.toggle('dark');localStorage.setItem('theme',d.classList.contains('dark')?'dark':'light')}document.documentElement.classList.toggle('dark',localStorage.getItem('theme')==='dark');</script>
   <title>Create Post - Admin</title>
</head>
<body class="bg-gray-50 dark:bg-gray-950 transition-colors duration-300">

<?php include "../lay/top-nav.php" ?>
<?php include_once('../lay/sidebar.php') ?>

<div class="pt-14 md:pl-56 min-h-screen">
    <div class="p-4 sm:p-6 lg:p-8">

        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 font-[family-name:var(--font-blog-heading)] mb-6">Create Post</h1>

        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 max-w-2xl">
            <form action="<?= url("admin/posts/create.php") ?>" method="post" enctype="multipart/form-data" class="space-y-5">

                <div>
                    <label for="post-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                    <input type="text" id="post-title" name="title" required placeholder="Post title" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition-all duration-200">
                </div>

                <div>
                    <label for="post-image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Image</label>
                    <input type="file" id="post-image" name="image" accept="image/png,image/jpeg,image/jpg,image/gif" required class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-amber-50 dark:file:bg-amber-900/30 file:text-amber-600 dark:file:text-amber-400 hover:file:bg-amber-100 dark:hover:file:bg-amber-800/50 transition-colors duration-200">
                    <?php if (isset($_GET["err"])) { ?>
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 font-medium">Invalid file type. Please upload an image (png, jpeg, jpg, gif).</p>
                    <?php } ?>
                </div>

                <div>
                    <label for="post-category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                    <select id="post-category" name="cat_id" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition-all duration-200">
                        <option value="">Select a category</option>
                        <?php
                        global $pdo;
                        $q = "SELECT * FROM catagories";
                        $statment = $pdo->prepare($q);
                        $statment->execute();
                        $catagories = $statment->fetchAll();
                        foreach ($catagories as $catagory) {
                        ?>
                        <option value="<?= $catagory->id ?>"><?= htmlspecialchars($catagory->name) ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div>
                    <label for="post-body" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Body</label>
                    <textarea id="post-body" name="body" rows="10" required placeholder="Post content..." class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-amber-400 focus:border-transparent outline-none transition-all duration-200"></textarea>
                </div>

                <button type="submit" class="bg-amber-400 text-gray-900 font-semibold px-6 py-2.5 rounded-xl hover:bg-amber-500 focus:ring-2 focus:ring-amber-300 focus:outline-none transition-all duration-200 cursor-pointer">Create</button>

            </form>
        </div>

    </div>
</div>

<script src="<?= asset("asset/js/admin.js") ?>"></script>
</body>
</html>
