<?php
//List of months to construct select field
$monthList = [
    1 => 'January',
    2 => 'February',
    3 => 'March',
    4 => 'April',
    5 => 'May',
    6 => 'June',
    7 => 'July',
    8 => 'August',
    9 => 'September',
    10 => 'October',
    11 => 'November',
    12 => 'December'
];

$formAction = 'registeruser';
$formId = 'registerForm';
$submitValue = 'Register';

$formValue = [
    'email' => '',
    'firstname' => '',
    'lastname' => '',
    'birthdate_day' => '',
    'birthdate_month' => '',
    'birthdate_year' => ''
];

if ($isEdition) {
    $formAction = 'edituser';
    $formId = 'editForm';
    $submitValue = 'Edit information';

    $formValue['email'] = $objUser->email;
    $formValue['firstname'] = $objUser->firstname;
    $formValue['lastname'] = $objUser->lastname;

    if ($objUser->birthdate != '') {
        $birthdate = DateTime::createFromFormat('Y-m-d', $objUser->birthdate);
        if($birthdate != null) {
            $formValue['birthdate_day'] = $birthdate->format('d');
            $formValue['birthdate_month'] = $birthdate->format('m');
            $formValue['birthdate_year'] = $birthdate->format('Y');
        }
    }
    $this->view->addScript('/js/user/edit.js');
} else
    $this->view->addScript('/js/user/register.js');

?>
<form action="/user/<?php echo $formAction; ?>" method="POST" class="form-regular" id="<?php echo $formId; ?>"
      onsubmit="return false;">
    <h2><?php if ($isEdition): ?>My account information<?php else: ?>Register<?php endif; ?></h2>
    <div class="row-block">
        <div class="col-size-12">
            <div id="message" class="row-block">
                <?php if ($userFormValidationAlert != null) : echo $userFormValidationAlert; endif; ?>
            </div>
        </div>
    </div>
    <div class="row-block">
        <div class="col-size-6">
            <div class="col-size-11">
                <label>Name <span class="required">*</span></label>
                <input type="text" name="firstname" maxlength="100" value="<?php echo $formValue['firstname']; ?>" required>
                <label>Last name <span class="required">*</span></label>
                <input type="text" name="lastname" maxlength="100" value="<?php echo $formValue['lastname']; ?>" required>
                <label>Birth date <span class="required">*</span></label>
            </div>
            <div class="row-block">
                <div class="col-size-3">
                    <select name="birthdate_day" required>
                        <option value="">Day</option>
                        <?php for ($day = 1; $day <= 31; $day++) : ?>
                            <option
                                value="<?php echo $day; ?>"<?php if ($isEdition) : if ($day == $formValue['birthdate_day']) : ?> selected<?php endif;
                            endif; ?>><?php echo $day; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-size-3 col-padleft-1">
                    <select name="birthdate_month" required>
                        <option value="">Month</option>
                        <?php foreach ($monthList as $monthNumber => $monthName) : ?>
                            <option
                                value="<?php echo $monthNumber; ?>"<?php if ($isEdition) : if ($monthNumber == $formValue['birthdate_month']) : ?> selected<?php endif;
                            endif; ?>><?php echo $monthName; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-size-3 col-padleft-1">
                    <select name="birthdate_year" required>
                        <option value="">Year</option>
                        <?php for ($year = date('Y') - 18; $year >= 1930; $year--) : ?>
                            <option
                                value="<?php echo $year; ?>"<?php if ($isEdition) : if ($year == $formValue['birthdate_year']) : ?> selected<?php endif;
                            endif; ?>><?php echo $year; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <?php if ($isEdition) : ?>
                <div class="col-size-11">
                    <label>E-mail <span class="required">*</span></label>
                    <input type="email" name="email" maxlength="150" value="<?php echo $formValue['email']; ?>" required>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-size-6">
            <div class="col-size-11">
                <?php if (!$isEdition) : ?>
                    <label>E-mail <span class="required">*</span></label>
                    <input type="email" name="email" maxlength="150" value="<?php echo $formValue['email']; ?>" required>
                <?php else : ?>
                    <label>Current password <span class="required">*</span>
                    </label>
                    <input type="password" name="old_password" maxlength="32">
                <?php endif; ?>
                <label>New password <span class="required">*<?php if ($isEdition) : ?>*<?php endif; ?></span></label>
                <input type="password" name="password" maxlength="32"<?php if (!$isEdition) : ?> required<?php endif; ?>>
                <label>Confirm password <span class="required">*<?php if ($isEdition) : ?>*<?php endif; ?></span></label>
                <input type="password" name="confirm-password"
                       maxlength="32"<?php if (!$isEdition) : ?> required<?php endif; ?>>
                <?php if ($isEdition) : ?>
                    <cite><span class="required">**</span> To change your current password, please complete the password fields.</cite>
                <?php endif; ?>
                <input type="submit" class="btn btn-success right" value="<?php echo $submitValue; ?>">
            </div>
        </div>
    </div>

</form>