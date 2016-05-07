<?php
$this->view->addScript('/js/site/search.js');
$documentId = $documentItem->id;
?>
<div id="message"></div>
<div>
    <div>Name: <?php echo $documentItem->name; ?></div>
    <div>Description: <?php echo $documentItem->description; ?></div>
    <div>Tags: <?php foreach ($documentItem->getTags() as $tagObject) : ?>
            <span><?php echo $tagObject->name; ?></span>
        <?php endforeach; ?></div>

    <?php if ($userLoggedIn) : ?>
        <?php if ($isDocumentPurchased) : ?>
            <a href="/document/download/id/<?php echo $documentId; ?>">Download</a>
        <?php elseif (!$isDocumentInCart) : ?>
            <input type="button" value="Add to cart" class="cartButton" data-role="add_to_cart"
                   data-id="<?php echo $documentId; ?>">
        <?php else: ?>
            <input type="button" value="Remove from cart" class="cartButton"
                   data-role="remove_from_cart" data-id="<?php echo $documentId; ?>">
        <?php endif; ?>
    <?php endif; ?>
</div>