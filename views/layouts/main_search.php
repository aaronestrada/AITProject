<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Open Data Storage</title>
    <?php foreach ($this->view->getCSSScripts() as $cssFile) : ?>
        <link rel="stylesheet" href="<?php echo $cssFile; ?>" type="text/css">
    <?php endforeach; ?>
    <?php foreach ($this->view->getScripts(JS_POSITION_START) as $scriptFile) : ?>
        <script type="text/javascript" src="<?php echo $scriptFile; ?>"></script>
    <?php endforeach; ?>
</head>
<body>
<div>
    <?php if (!$this->roleAccess->isLoggedIn()) : ?>
        <a href="/user/login">Login</a>
        <a href="/user/register">Register</a>
    <?php else: ?>
        <a href="/site/cart">My shopping cart</a>
        <a href="/user/logout">Logout
            (<?php echo $this->roleAccess->getProperty('firstname'); ?> <?php echo $this->roleAccess->getProperty('lastname'); ?>)</a>
    <?php endif; ?>
</div>
<?php
    $searchQuery = $this->session->get('searchQuery');
    $searchText = isset($searchQuery['searchText']) ? $searchQuery['searchText']: '';
    $tags = isset($searchQuery['tags']) ? $searchQuery['tags']: '';
?>
<form action="/site/search" method="GET">
    Search
    <input type="text" name="searchtext" value="<?php echo $searchText; ?>">

    Tags
    <input type="text" name="tags" value="<?php echo $tags; ?>">
    <input type="submit" value="Search">
</form>
<div id="message"></div>
<?php echo $layoutContent; ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<?php foreach ($this->view->getScripts(JS_POSITION_END) as $scriptFile) : ?>
    <script type="text/javascript" src="<?php echo $scriptFile; ?>"></script>
<?php endforeach; ?>
</body>
</html>