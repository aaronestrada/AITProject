<?php $this->view->addScript('/js/site/search.js'); ?>
<div id="message"></div>
<?php foreach ($documentList as $documentItem) : $documentId = $documentItem->id; ?>
    <div>
        <div>Name: <?php echo $documentItem->name; ?></div>
        <div>Description: <?php echo $documentItem->description; ?></div>
        <div>Tags: <?php foreach ($documentItem->getTags() as $tagObject) : ?>
                <span><?php echo $tagObject->name; ?></span>
            <?php endforeach; ?>
        </div>
        <?php if ($userLoggedIn) : ?>
            <?php if (in_array($documentId, $purchasedDocuments)) : ?>
                <a href="/document/download/id/<?php echo $documentId; ?>">Download</a>
            <?php elseif (!in_array($documentId, $documentsInCart)) : ?>
                <input type="button" value="Add to cart" class="cartButton" data-role="add_to_cart"
                       data-id="<?php echo $documentId; ?>">
            <?php else: ?>
                <input type="button" value="Remove from cart" class="cartButton" data-role="remove_from_cart"
                       data-id="<?php echo $documentId; ?>">
            <?php endif; ?>

        <?php endif; ?>
        <a href="/site/overview/id/<?php echo $documentId; ?>">More information</a>
    </div>
<?php endforeach; ?>