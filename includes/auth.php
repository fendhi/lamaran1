<?php
// includes/auth.php

function admin_is_logged_in(): bool
{
    return !empty($_SESSION['admin_logged_in']);
}

function admin_require_login(array $config): void
{
    if (!admin_is_logged_in()) {
        redirect(url_for($config, '/admin/login.php'));
    }
}
