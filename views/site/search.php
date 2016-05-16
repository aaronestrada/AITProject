<?php
$this->view->addScript('/js/site/document.js');
$this->view->addScript('/js/site/search.js');
?>
<div class="row-block">
    <div class="col-size-3-border-right">
        Open Data System will help you to find interesting information to use them for your company or for
        research purposes. Our database contains more than 10,000 data sets under hundreds of categories.
        Please register and have fun!
    </div>
    <div class="col-size-8 col-padleft-1">
        <h2>Search results</h2>
        <iframe id="downloadFile" class="hidden"></iframe>
        <div id="message" class="row-block"></div>
        <?php if (count($documentList) > 0) :
            foreach ($documentList as $documentItem) :
                $documentId = $documentItem->id;
                $objAuthor = $documentItem->getAuthor();
                ?>
                <article class="document">
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
                                <p><strong>Published on:</strong> <?php echo date('d/m/Y', strtotime($documentItem->published_at)); ?></p>
                                <p class="tags">
                                    <?php foreach ($documentItem->getTags() as $tagObject) : ?>
                                        <a href="/site/search?tags=<?php echo urlencode($tagObject->name); ?>"
                                           class="tag"><?php echo $tagObject->name; ?></a>
                                    <?php endforeach; ?>
                                </p>
                            </div>
                            <div class="col-size-6 col-padleft-1">
                                <p class="description"><?php echo $documentItem->description; ?></p>
                            </div>
                        </div>
                    </section>
                    <footer>
                        <input type="button" value="Details" class="btn-small btn-default moreInfoButton"
                               data-id="<?php echo $documentId; ?>">
                        <?php if ($userLoggedIn) : ?>
                            <?php if (in_array($documentId, $purchasedDocuments)) : ?>
                                <input type="button" value="Download" class="btn-small btn-default downloadButton"
                                       data-id="<?php echo $documentId; ?>">
                            <?php elseif (!in_array($documentId, $documentsInCart)) : ?>
                                <input type="button" value="Add to cart" class="btn-small btn-success cartButton"
                                       data-role="add_to_cart"
                                       data-id="<?php echo $documentId; ?>">
                            <?php else: ?>
                                <input type="button" value="Remove from cart" class="btn-small btn-danger cartButton"
                                       data-role="remove_from_cart"
                                       data-id="<?php echo $documentId; ?>">
                            <?php endif; ?>
                        <?php endif; ?>
                    </footer>
                </article>
            <?php endforeach;
        else : ?>
        <div class="alert alert-danger">No results found. Please try with another search criteria.</div>
    </div>
    <?php endif; ?>
</div>
</div>
