<div id="message">
    <?php if ($attemptLogin === true): ?>
        <div>Invalid credentials</div>
    <?php endif; ?>
    <?php if ($userFormValidationAlert != null) : echo $userFormValidationAlert; endif; ?>
</div>
<form action="/user/login" method="POST">
    Username
    <input type="text" name="email" required>
    Password
    <input type="password" name="password" required>
    <input type="submit" value="Login">
</form>