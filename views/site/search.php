<?php
$this->view->addScript('/js/site/document.js');
$this->view->addScript('/js/site/search.js');

$totalTags = null;
if (is_array($tagList)) {
    $totalTagCount = count($tagList);
    $totalTags = $totalTagCount <= 5 ? $totalTagCount : 5;
}
?>
<div class="row-block">
    <div class="col-size-2">
        <article class="tags">
            <header>Tags</header>
            <section>
                <div class="row-block">
                    <?php
                    for ($tagIndex = 0; $tagIndex < $totalTags; $tagIndex++) :
                        $tagItem = $tagList[$tagIndex];
                        $documentCount = $tagItem->getDocumentCount();
                        if ($documentCount > 0) : ?>
                            <div class="col-size-12"><a
                                    href="/site/search?tags=<?php echo urlencode($tagItem->name); ?>"><?php echo $tagItem->name; ?>
                                    (<?php echo $documentCount; ?>)</a></div>
                        <?php endif;
                    endfor; ?>
                </div>
                <div class="row-block" id="tagList" style="display: none;">
                    <?php
                    for ($tagIndex = $totalTags; $tagIndex < $totalTagCount; $tagIndex++) :
                        $tagItem = $tagList[$tagIndex];
                        $documentCount = $tagItem->getDocumentCount();
                        if ($documentCount > 0) : ?>
                            <div class="col-size-12"><a
                                    href="/site/search?tags=<?php echo urlencode($tagItem->name); ?>"><?php echo $tagItem->name; ?>
                                    (<?php echo $documentCount; ?>)</a></div>
                        <?php endif;
                    endfor; ?>
                </div>
                <summary><a href="#" class="toggle" id="lnkShowMoreTags" data-toggle="show" data-more="More &#9660;"
                            data-less="Less &#9650;">More &#9660;</a></summary>
            </section>
        </article>
    </div>
    <div class="col-size-9 col-padleft-1">
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
