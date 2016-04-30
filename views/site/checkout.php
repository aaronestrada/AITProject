<?php $this->view->addScript('/js/site/checkout.js');
$checkoutTotal = 0; ?>
<div id="message">CHECKOUT</div>
<?php foreach ($documentList as $documentItem) :
    $documentId = $documentItem->id;
    $checkoutTotal += floatval($documentItem->price);
    ?>
    <div id="document_<?php echo $documentId; ?>">
        <div>Name: <?php echo $documentItem->name; ?></div>
        <div>Description: <?php echo $documentItem->description; ?></div>
        <div>Tags: <?php foreach ($documentItem->getTags() as $tagObject) : ?>
                <span><?php echo $tagObject->name; ?></span>
            <?php endforeach; ?></div>
    </div>
<?php endforeach; ?>

<div>TOTAL USD $<?php echo number_format($checkoutTotal, 2, '.', ','); ?></div>
