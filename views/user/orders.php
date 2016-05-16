<?php
$this->view->addScript('/js/site/document.js');
$this->view->addScript('/js/user/orders.js');
?>
<div class="row-block">
    <iframe id="downloadFile" class="hidden"></iframe>
    <h2>My orders</h2>
    <?php if (count($purchaseList) == 0) : ?>
        <div class="row-block">
            <div class="col-size-12">
                <div class="alert alert-danger">No orders yet!</div>
            </div>
        </div>
    <?php else : ?>
        <?php if ($checkoutAlertSuccess != null) : ?>
            <div class="row-block">
                <div class="col-size-12"><?php echo $checkoutAlertSuccess; ?></div>
            </div>
        <?php endif; ?>
        <?php foreach ($purchaseList as $purchaseItem) :
            $totalPurchase = 0; ?>
            <article class="document order">
                <header>
                    <h3>Order #<?php echo $purchaseItem->id; ?>
                        [<?php echo date('d/m/Y h:i', strtotime($purchaseItem->created_at)); ?>]</h3>
                </header>
                <section>
                    <div class="row-block">
                        <table style="width: 100%">
                            <tbody>
                            <?php
                            if (isset($documentObjectList[$purchaseItem->id])) :
                                $documentList = $documentObjectList[$purchaseItem->id];
                                foreach ($documentList as $documentItem) :
                                    $objDocument = $documentItem['documentItem'];
                                    $documentPurchasePrice = $documentItem['purchasePrice'];
                                    $totalPurchase += $documentPurchasePrice;
                                    ?>
                                    <tr>
                                        <td class=""><?php echo $objDocument->name; ?></td>
                                        <td class="align-right">
                                            <span>USD $</span><?php echo number_format($documentPurchasePrice, 2, '.', ','); ?>
                                        </td>
                                        <td class="align-right">
                                            <input type="button"
                                                   class="btn-small btn-default moreInfoButton"
                                                   value="Details"
                                                   data-id="<?php echo $objDocument->id; ?>">
                                            <input type="button"
                                                   class="btn-small btn-default downloadButton"
                                                   value="Download"
                                                   data-id="<?php echo $objDocument->id; ?>">
                                        </td>
                                    </tr>
                                    <?php
                                endforeach;
                            endif; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td width="50%"><strong>ORDER TOTAL</strong></td>
                                <td class="align-right"><strong>USD
                                        $<?php echo number_format($totalPurchase, 2, '.', ',');; ?></strong></td>
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>
            </article>
        <?php endforeach;
    endif; ?>
</div>
