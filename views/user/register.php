<form action="/user/register" method="POST">
    E-mail:
    <input type="email" name="email" maxlength="150" required>

    Password:
    <input type="password" name="password" maxlength="32" required>

    Confirm password:
    <input type="password" name="confirm-password" maxlength="32" required>

    Name:
    <input type="text" name="firstname" maxlength="100" required>

    Last name:
    <input type="text" name="lastname" maxlength="100" required>

    Birth date:
    <input type="date" name="birthdate" maxlength="10" required>

    <input type="submit" value="Register">
</form>

<?php print_r($errorList); ?>