<?php
$searchQuery = $this->session->get('searchQuery');
$searchText = isset($searchQuery['searchText']) ? $searchQuery['searchText'] : '';
$tags = isset($searchQuery['tags']) ? $searchQuery['tags'] : '';
?>
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
<body class="search-fields">
<header class="main-header">
    <nav>
        <a class="logo" href="#">&nbsp;</a>
        <ul class="menu hidden-menu" id="menu">
            <li><a href="/">Home</a></li>
            <?php if (!$this->roleAccess->isLoggedIn()) : ?>
                <li><a href="/user/login">Login</a></li>
                <li><a href="/user/register">Register</a></li>
            <?php else: ?>
                <li><a href="/site/cart">My shopping cart</a></li>
                <li><a href="/user/orders">My orders</a></li>
                <li><a href="/user/edit">My account</a></li>
                <li><a href="/user/logout">Logout
                        (<?php echo $this->roleAccess->getProperty('firstname'); ?> <?php echo $this->roleAccess->getProperty('lastname'); ?>
                        )</a></li>
            <?php endif; ?>
        </ul>
        <a href="#" class="pull" id="menuIcon" data-menu-id="menu">&#9776;</a>
        <div class="search-row">
            <form action="/site/search" method="GET">
                <div class="col-size-5 field-inline">
                    <span>Search</span>
                    <input type="text" placeholder="Criteria to search" name="searchtext"
                           value="<?php echo $searchText; ?>">
                </div>
                <div class="col-size-5 field-inline">
                    <span>Tags</span>
                    <input type="text" placeholder="Example: economy" name="tags" value="<?php echo $tags; ?>">
                </div>
                <div class="col-size-2">
                    <input type="submit" class="btn btn-success" value="Search">
                </div>
            </form>
        </div>
    </nav>
</header>
<div class="content"><?php echo $layoutContent; ?></div>
<footer class="main-footer">Aaron Estrada, Gustavs Venters - Advanced Internet Technologies, 2016 &copy;</footer>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script type="text/javascript" src="/js/framework/framework.js"></script>
<?php foreach ($this->view->getScripts(JS_POSITION_END) as $scriptFile) : ?>
    <script type="text/javascript" src="<?php echo $scriptFile; ?>"></script>
<?php endforeach; ?>
</body>
</html>