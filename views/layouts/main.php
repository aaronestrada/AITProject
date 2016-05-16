<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Open Data System</title>
    <link href='https://fonts.googleapis.com/css?family=Titillium+Web' rel='stylesheet' type='text/css'>
    <link href="/css/framework.main.css" rel="stylesheet" type="text/css">
    <?php foreach ($this->view->getCSSScripts() as $cssFile) : ?>
        <link rel="stylesheet" href="<?php echo $cssFile; ?>" type="text/css">
    <?php endforeach; ?>
    <?php foreach ($this->view->getScripts(JS_POSITION_START) as $scriptFile) : ?>
        <script type="text/javascript" src="<?php echo $scriptFile; ?>"></script>
    <?php endforeach; ?>
</head>
<body>
<header class="main-header">
    <nav>
        <a class="logo" href="/"><img src="/img/logo.png" alt="logo"></a>
        <ul class="menu hidden-menu" id="menu"><?php if (!isset($hideHomeLink)) : ?>
                <li><a href="/">Home</a></li>
            <?php endif; ?>
            <?php if (!$this->roleAccess->isLoggedIn()) : ?>
                <li><a href="/user/login">Login</a></li>
                <li><a href="/user/register">Register</a></li>
            <?php else: ?>
                <li><a href="/site/cart">My shopping cart</a></li>
                <li><a href="/user/orders">My orders</a></li>
                <li><a href="/user/edit">My account</a></li>
                <li><a href="/user/logout">Logout (<?php echo $this->roleAccess->getProperty('firstname'); ?> <?php echo $this->roleAccess->getProperty('lastname'); ?>)</a></li>
            <?php endif; ?>
        </ul>
        <a href="#" class="pull" id="menuIcon" data-menu-id="menu">&#9776;</a>
    </nav>
</header>
<div class="content">
    <?php echo $layoutContent; ?>
</div>
<footer class="main-footer">Aaron Estrada, Gustavs Venters - Advanced Internet Technologies, 2016 &copy;</footer>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script type="text/javascript" src="/js/framework/framework.js"></script>
<?php foreach ($this->view->getScripts(JS_POSITION_END) as $scriptFile) : ?>
    <script type="text/javascript" src="<?php echo $scriptFile; ?>"></script>
<?php endforeach; ?>
</body>
</html>