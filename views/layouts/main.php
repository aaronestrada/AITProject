<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Open Data Storage</title>
</head>
<body>
<div>
    <?php if (!$this->roleAccess->isLoggedIn()) : ?>
        <a href="/user/login">Login</a>
        <a href="/user/register">Register</a>
    <?php else: ?>
        <a href="/user/login">My shopping cart</a>
        <a href="/user/logout">Logout</a>
    <?php endif; ?>
</div>
<?php echo $layoutContent; ?>
</body>
</html>