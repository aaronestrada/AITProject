<div class="row-block">
    <div class="col-size-6">
        <form action="/user/login" method="POST" class="form-regular">
            <h2>Login</h2>
            <div id="message" class="row-block">
                <?php if ($attemptLogin === true): ?>
                    <div class="alert alert-danger">Invalid credentials</div>
                <?php endif; ?>
                <?php if ($userFormValidationAlert != null) : echo $userFormValidationAlert; endif; ?>
            </div>
            <label>E-mail <span class="required">*</span></label>
            <input type="text" name="email" maxlength="150" required>
            <label>Password <span class="required">*</span></label>
            <input type="password" name="password"  maxlength="32" required>
            <div class="row-block">
                <div class="col-size-12">
                    <input type="submit" class="btn btn-success" value="Login">
                </div>
            </div>
        </form>
    </div>
</div>