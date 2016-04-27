<?php $this->view->addScript('/js/site/search.js'); ?>
<form action="/site/search" method="GET">
    Search
    <input type="text" name="searchtext" value="<?php echo $searchText; ?>">

    Tags
    <input type="text" name="tags" value="<?php echo $tags; ?>">
    <input type="submit" value="Search">
</form>
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
            <input type="button" value="Download" class="downloadDocumentButton" data-id="<?php echo $documentId;?>">
        <?php elseif (!in_array($documentId, $documentsInCart)) : ?>
            <input type="button" value="Add to cart" class="addToCartButton" data-id="<?php echo $documentId;?>">
        <?php else: ?>
            <input type="button" value="Remove from cart" class="removeFromCartbutton" data-id="<?php echo $documentId;?>">
        <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>