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
<div>
    <h1>Lorem ipsum</h1>
    <p>"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore
        magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
        consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla
        pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est
        laborum.
    </p>
</div>
<form action="/site/search" method="GET">
    Search
    <input type="text" name="searchtext">

    Tags
    <input type="text" name="tags">
    <input type="submit" value="Search">
</form>
</body>
</html>