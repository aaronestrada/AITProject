<?php
$this->view->addScript('/js/site/document.js');
$this->view->addScript('/js/site/cart.js');
$documentCount = count($documentList);
?>
<div class="row-block">
    <h2>My shopping cart</h2>
    <div id="message" class="row-block"></div>
</div>
<div class="row-block">
    <div class="col-size-12" id="itemsList">
        <?php if ($documentCount > 0) :
        $checkoutTotal = 0;
        foreach ($documentList as $documentItem) :
            $documentId = $documentItem->id;
            $objAuthor = $documentItem->getAuthor();
            $checkoutTotal += floatval($documentItem->price);
            ?>
            <article class="document" id="document_<?php echo $documentId; ?>">
                <header>
                    <h1><?php echo $documentItem->name; ?></h1>
                    <span>USD $<?php echo number_format($documentItem->price, 2, '.', ','); ?></span>
                </header>
                <section>
                    <div class="row-block">
                        <div class="col-size-5">
                            <?php if ($objAuthor->name != '') : ?>
                                <p><strong>Published by:</strong> <?php echo $objAuthor->name; ?></p>
                            <?php endif; ?>
                            <p><strong>Published
                                    on:</strong> <?php echo date('d/m/Y', strtotime($documentItem->published_at)); ?>
                            </p>
                        </div>
                        <div class="col-size-6 col-padleft-1">
                            <p class="description"><?php echo $documentItem->description; ?></p>
                        </div>
                        <div class="col-size-12">
                            <p class="tags">
                                <?php foreach ($documentItem->getTags() as $tagObject) : ?>
                                    <a href="/site/search?tags=<?php echo urlencode($tagObject->name); ?>"
                                       class="tag"><?php echo $tagObject->name; ?></a>
                                <?php endforeach; ?>
                            </p>
                        </div>
                    </div>
                </section>
                <footer>
                    <input type="button" value="Details" class="btn-small btn-default moreInfoButton"
                           data-id="<?php echo $documentId; ?>">
                    <input type="button" value="Remove from cart" class="btn-small btn-danger cartButton"
                           data-role="remove_from_cart"
                           data-id="<?php echo $documentId; ?>">
                </footer>
            </article>
        <?php endforeach; ?>
    </div>
    <div class="col-size-8 col-padleft-4 align-right align-center-mobile" id="checkoutBlock">
        <div class="row-block emphasize">
            <strong>Total:</strong> USD $<span
                id="checkoutTotal"><?php echo number_format($checkoutTotal, 2, '.', ','); ?></span>
        </div>
        <div class="row-block">
            <input type="button" id="checkoutButton" value="Checkout" class="btn btn-warning">
        </div>
    </div>
    <?php endif; ?>
    <div class="col-size-12<?php if ($documentCount > 0) : ?> hidden<?php endif; ?>" id="alertNoItems">
        <div class="alert alert-danger">No items added yet!</div>
    </div>
</div>