<?php
$this->view->addScript('/js/site/checkout.js');
$checkoutTotal = 0;
$currentYear = date('Y');
?>
<div id="message" class="row-block"></div>
<form action="/site/checkoutprocess" method="POST" id="checkoutprocessForm" onsubmit="return false;"
      class="form-regular">
    <div class="row-block">
        <h2>Checkout</h2>
        <div class="col-size-6">
            <h3>Documents</h3>
            <?php foreach ($documentList as $documentItem) :
                $documentId = $documentItem->id;
                $checkoutTotal += floatval($documentItem->price);
                ?>
                <article class="document">
                    <header class="no-margin">
                        <h3><?php echo $documentItem->name; ?></h3>
                        <span>USD $<?php echo number_format($documentItem->price, 2, '.', ','); ?></span>
                    </header>
                    <section>
                        <p><?php echo $documentItem->description; ?></p>
                    </section>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="col-size-5 col-padleft-1">
            <div class="row-block">
                <h3>User information</h3>
                <p><strong>Name: </strong><?php echo $objUser->firstname . ' ' . $objUser->lastname; ?></p>
                <p><strong>E-mail: </strong><?php echo $objUser->email; ?></p>
            </div>
            <div class="row-block">
                <h3>Purchase information</h3>
                <div class="row-block">
                    <div class="col-size-6">
                        <label>Credit card <span class="required">*</span></label>
                        <input type="text" maxlength="16" name="card_number" required>
                    </div>
                    <div class="col-size-6">
                        <label>CVV <span class="required">*</span></label>
                        <input type="text" maxlength="4" name="card_cvv" required>
                    </div>
                </div>
                <label>Expiration date <span class="required">*</span></label>
                <div class="row-block">
                    <div class="col-size-6">
                        <select name="exp_month" required>
                            <option value="">Month</option>
                            <option value="1">01</option>
                            <option value="2">02</option>
                            <option value="3">03</option>
                            <option value="4">04</option>
                            <option value="5">05</option>
                            <option value="6">06</option>
                            <option value="7">07</option>
                            <option value="8">08</option>
                            <option value="9">09</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                    </div>
                    <div class="col-size-6">
                        <select name="exp_year" required>
                            <option value="">Year</option>
                            <?php for ($yearItem = $currentYear; $yearItem <= $currentYear + 7; $yearItem++) : ?>
                                <option value="<?php echo $yearItem; ?>"><?php echo $yearItem; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row-block">
                <br>
                <div class="col-size-8 col-padleft-4 align-right align-center-mobile">
                    <div class="row-block emphasize">
                        <strong>Total:</strong> USD
                        $<span><?php echo number_format($checkoutTotal, 2, '.', ','); ?></span>
                    </div>
                    <div class="row-block">
                        <input type="submit" id="checkoutButton" value="Purchase" class="btn btn-success">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
