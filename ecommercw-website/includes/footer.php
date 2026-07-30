</main>

<?php
$v = filemtime(__DIR__ . '/../assets/js/theme.js');
?>

<footer id="contact" class="bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 mt-16 transition-colors duration-300">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
    <div>
      <h3 class="font-bold text-lg text-primary-600 dark:text-primary-400 mb-3">ShopEase</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400">Your one-stop shop for quality products at great prices, delivered fast and reliably.</p>
    </div>
    <div>
      <h4 class="font-semibold mb-3 text-sm uppercase tracking-wide text-gray-500 dark:text-gray-400">About</h4>
      <p class="text-sm text-gray-500 dark:text-gray-400">We are committed to providing an excellent shopping experience with curated categories, secure checkout, and responsive support.</p>
    </div>
    <div>
      <h4 class="font-semibold mb-3 text-sm uppercase tracking-wide text-gray-500 dark:text-gray-400">Quick Links</h4>
      <ul class="space-y-2 text-sm text-gray-500 dark:text-gray-400">
        <li><a href="<?php echo isset($isAdmin) ? '../index.php' : 'index.php'; ?>" class="hover:text-primary-600 dark:hover:text-primary-400">Home</a></li>
        <li><a href="<?php echo isset($isAdmin) ? '../login.php' : 'login.php'; ?>" class="hover:text-primary-600 dark:hover:text-primary-400">Login</a></li>
        <li><a href="<?php echo isset($isAdmin) ? '../register.php' : 'register.php'; ?>" class="hover:text-primary-600 dark:hover:text-primary-400">Register</a></li>
      </ul>
    </div>
    <div>
      <h4 class="font-semibold mb-3 text-sm uppercase tracking-wide text-gray-500 dark:text-gray-400">Contact</h4>
      <ul class="space-y-2 text-sm text-gray-500 dark:text-gray-400">
        <li>Email: support@shopease.test</li>
        <li>Phone: +1 (555) 123-4567</li>
        <li>Address: 123 Market Street, Suite 4</li>
      </ul>
    </div>
  </div>
  <div class="border-t border-gray-100 dark:border-gray-700 py-4 text-center text-xs text-gray-400 dark:text-gray-500">
    &copy; <?php echo date('Y'); ?> ShopEase. All rights reserved.
  </div>
</footer>

<script src="<?php echo isset($isAdmin) ? '../assets/js/theme.js' : 'assets/js/theme.js'; ?>?v=<?php echo $v; ?>"></script>
<script src="<?php echo isset($isAdmin) ? '../assets/js/api.js' : 'assets/js/api.js'; ?>?v=<?php echo $v; ?>"></script>
<?php if (isLoggedIn()): ?>
<script src="<?php echo isset($isAdmin) ? '../assets/js/cart.js' : 'assets/js/cart.js'; ?>?v=<?php echo $v; ?>"></script>
<script src="<?php echo isset($isAdmin) ? '../assets/js/notifications.js' : 'assets/js/notifications.js'; ?>?v=<?php echo $v; ?>"></script>
<?php endif; ?>
<?php if (!empty($extraScripts)) : foreach ($extraScripts as $script): ?>
<script src="<?php echo e($script); ?>?v=<?php echo $v; ?>"></script>
<?php endforeach; endif; ?>
</body>
</html>
