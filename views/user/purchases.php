<?php foreach ($purchaseList as $purchaseItem) :
    $totalPurchase = 0; ?>
    <article>
        <h3>Transaction #<?php echo $purchaseItem->id; ?> (<?php echo $purchaseItem->created_at; ?>)</h3>
        <?php
        if (isset($documentObjectList[$purchaseItem->id])) :
            $documentList = $documentObjectList[$purchaseItem->id];
            foreach ($documentList as $documentItem) :
                $objDocument = $documentItem['documentItem'];
                $documentPurchasePrice = $documentItem['purchasePrice'];
                $totalPurchase += $documentPurchasePrice;
                ?>
                <div>
                    <p><?php echo $objDocument->name; ?> <a href="/document/download/id/<?php echo $objDocument->id; ?>">(Download)</a></p>
                    <p>Price: USD $<?php echo number_format($documentPurchasePrice, 2, '.', ','); ?></p>
                </div>
                <?php
            endforeach;
        endif; ?>
        <p>TOTAL: USD $<?php echo number_format($totalPurchase, 2, '.', ',');; ?></p>
    </article>
<?php endforeach; ?>
