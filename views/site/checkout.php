<?php
$this->view->addScript('/js/site/checkout.js');
$checkoutTotal = 0;
$currentYear = date('Y');

?>
<div id="message"></div>
<h1>Checkout</h1>
<?php foreach ($documentList as $documentItem) :
$documentId = $documentItem->id;
$checkoutTotal += floatval($documentItem->price);
?>
<div>
    <h3>Document details</h3>
    <div id="document_<?php echo $documentId; ?>">
        <div>Name: <?php echo $documentItem->name; ?></div>
        <div>Description: <?php echo $documentItem->description; ?></div>
        <div>Tags: <?php foreach ($documentItem->getTags() as $tagObject) : ?>
                <span><?php echo $tagObject->name; ?></span>
            <?php endforeach; ?></div>
    </div>
    <?php endforeach; ?>
</div>
<hr>
<div>
    <h3>User information</h3>
    <p>Name: <?php echo $objUser->firstname . ' ' . $objUser->lastname; ?></p>
    <p>E-mail: <?php echo $objUser->email; ?></p>
</div>
<form action="/site/checkoutprocess" method="POST" id="checkoutprocessForm" onsubmit="return false;">
    <div>
        <h3>Purchase information</h3>
        Credit card number
        <input type="text" maxlength="16" name="card_number" required>
        CVV
        <input type="text" maxlength="4" name="card_cvv" required>
        Expiration date
        <select name="exp_month" required>
            <option>Month</option>
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
        <select name="exp_year" required>
            <option>Year</option>
            <?php for ($yearItem = $currentYear; $yearItem <= $currentYear + 7; $yearItem++) : ?>
                <option value="<?php echo $yearItem; ?>"><?php echo $yearItem; ?></option>
            <?php endfor; ?>
        </select>
        <div>TOTAL USD $<?php echo number_format($checkoutTotal, 2, '.', ','); ?></div>
        <input type="submit" value="Purchase">
    </div>
</form>
<hr>
