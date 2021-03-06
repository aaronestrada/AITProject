<?php if (count($errorList) > 0): ?>
    <div class="alert alert-danger"><?php foreach ($errorList as $errorItem) :
            switch ($errorItem):
                case 'error_email_empty': ?>
                    <div>E-mail is empty</div>
                    <?php break;
                case 'error_email_already_exists': ?>
                    <div>E-mail is already taken</div>
                    <?php break;
                case 'error_email_invalid': ?>
                    <div>E-mail is invalid</div>
                    <?php break;
                case 'error_firstname_empty': ?>
                    <div>First name is empty</div>
                    <?php break;
                case 'error_lastname_empty': ?>
                    <div>Last name is empty</div>
                    <?php break;
                case 'error_old_password_empty': ?>
                    <div>Current password is empty</div>
                    <?php break;
                case 'error_old_password_do_not_match': ?>
                    <div>Current password is incorrect</div>
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
                case 'error_birthdate_invalid': ?>
                    <div>Birth date is invalid</div>
                    <?php break;
            endswitch;
        endforeach; ?>
    </div>
<?php else :
    if ($isEdition) : ?>
        <div class="alert alert-success">Your information has been updated!</div>
    <?php else : ?>
        <div class="alert alert-success">Thanks for registering! Please enter your credentials to login!</div>
        <?php
    endif;
endif;
?>