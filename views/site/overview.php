<?php
$this->view->addScript('/js/site/document.js');
$this->view->addScript('/js/site/search.js');
$this->view->addScript('/js/vendor/chart.min.js');
$this->view->addScript('/js/site/overview.js');
$documentId = $documentItem->id;
$objAuthor = $documentItem->getAuthor();
?>
<iframe id="downloadFile" class="hidden"></iframe>
<div id="message"></div>
<div class="row-block">
    <div class="col-size-12">
        <article class="document">
            <header>
                <h1><?php echo $documentItem->name; ?></h1>
                <span>USD $<?php echo number_format($documentItem->price, 2, '.', ','); ?></span>
            </header>
            <section>
                <div class="row-block">
                    <div class="col-size-12">
                        <?php if ($objAuthor->name != '') : ?>
                            <p><strong>Published by:</strong> <?php echo $objAuthor->name; ?></p>
                        <?php endif; ?>
                        <p><strong>Published
                                on:</strong> <?php echo date('d/m/Y', strtotime($documentItem->published_at)); ?></p>
                        <br>
                        <p class="description"><?php echo $documentItem->description; ?></p>
                        <br>
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
                <?php if ($userLoggedIn) : ?>
                    <?php if ($isDocumentPurchased) : ?>
                        <input type="button" value="Download" class="btn-small btn-default downloadButton"
                               data-id="<?php echo $documentId; ?>">
                    <?php elseif (!$isDocumentInCart) : ?>
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
    </div>
    <div id="chartArea" class="col-size-12" data-id="<?php echo $documentId; ?>" data-name="<?php echo $documentItem->name; ?>">
        <h3>Document chart</h3>
        <div id="chartControls" class="col-size-12">
            <div class="col-size-4 col-padleft-1 col-padright-1">
                <select id="availableCols" class="col-size-12" multiple="multiple">
                    <optgroup label="Available columns">
                </select>
                <input type="button" id="addColumnButton" class="btn btn-success col-size-12" value="Add">
            </div>
            <div class="col-size-4 col-padleft-1 col-padright-1">
                <select id="selectedCols" class="col-size-12" multiple="multiple">
                    <optgroup label="Columns in chart">
                </select>
                <input type="button" id="removeColumnButton" class="btn btn-warning col-size-12" value="Remove">
            </div>
        </div>
        <div class="col-size-12">
            <canvas id="canvas">Your browser does not support canvas</canvas>
        </div>
    </div>
</div>