<?php if (count($errorList) > 0) : ?>
    <div class="alert alert-danger"><?php foreach ($errorList as $errorItem) :
            switch ($errorItem):
                case 'credit_card_invalid': ?>
                    <div>Credit card is invalid.  Please verify.</div>
                    <?php break;
                case 'cart_modified': ?>
                    <div>Your shopping cart has been modified.  Please <a href="javascript:void(0)" onclick="location.reload();">refresh this page.</a></div>
                    <?php break;
                case 'connection_refused':
                case 'user_not_found': ?>
                    <div>There was a problem on trying to proceed with the purchase. Please try again.</div>
                    <?php break;
                case 'cart_empty': ?>
                    <div>Your shopping cart is empty.  Please select at least one document prior to proceed with checkout.</div>
                    <?php break;
            endswitch;
        endforeach; ?>
    </div>
<?php else : ?>
    <div class="alert alert-success">Thanks for your purchase! Proceed to download the files.</div>
<?php endif; ?>