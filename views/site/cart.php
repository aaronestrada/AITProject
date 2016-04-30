<?php $this->view->addScript('/js/site/cart.js'); ?>
<div id="message"></div>
<?php if (count($documentList) > 0) :
    foreach ($documentList as $documentItem) : $documentId = $documentItem->id; ?>
        <div id="document_<?php echo $documentId; ?>">
            <div>Name: <?php echo $documentItem->name; ?></div>
            <div>Description: <?php echo $documentItem->description; ?></div>
            <div>Tags: <?php foreach ($documentItem->getTags() as $tagObject) : ?>
                    <span><?php echo $tagObject->name; ?></span>
                <?php endforeach; ?></div>
            <input type="button" value="Remove from cart" class="cartButton" data-id="<?php echo $documentId; ?>">
        </div>
    <?php endforeach;
else :?>
    <div>No items added yet!</div>
<?php endif; ?>
