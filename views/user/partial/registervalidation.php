<div><?php foreach ($errorList as $errorItem) :
        switch ($errorItem):
            case 'error_email_empty': ?>
                <div>E-mail is empty</div>
                <?php break;
            case 'error_email_already_exists': ?>
                <div>E-mail is already taken</div>
                <?php break;
            case 'error_firstname_empty': ?>
                <div>First name is empty</div>
                <?php break;
            case 'error_lastname_empty': ?>
                <div>Last name is empty</div>
                <?php break;
            case 'error_password_empty': ?>
                <div>Password is empty</div>
                <?php break;
            case 'error_confirm-password_empty': ?>
                <div>Password confirmation is empty</div>
                <?php break;
            case 'error_passwords_do_not_match': ?>
                <div>Passwords do not match</div>
                <?php break;
        endswitch;
    endforeach; ?>
</div>