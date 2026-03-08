<?php
require __DIR__ . '/../includes/bootstrap.php';

unset($_SESSION['admin_logged_in']);
redirect(url_for($config, '/admin/login.php'));
