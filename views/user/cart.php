<?php $this->view->addScript('/js/cart.js'); ?>
<?php foreach($documentList as $documentItem) : ?>
    <div>
        <div>Name: <?php echo $documentItem->name; ?></div>
        <div>Description: <?php echo $documentItem->description; ?></div>
        <div>Tags: <?php foreach($documentItem->getTags() as $tagObject) : ?>
                <span><?php echo $tagObject->name; ?></span>
            <?php endforeach; ?></div>
    </div>
<?php endforeach; ?>