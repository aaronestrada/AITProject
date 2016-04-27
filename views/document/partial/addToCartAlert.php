<?php if (count($errorList) > 0) : ?>
    <?php foreach ($errorList as $errorItem) :
        switch ($errorItem):
            case 'document_already_purchased': ?>
                <div>Document has been already purchased.</div>
                <?php break;
            case 'document_already_in_cart': ?>
                <div>Document has been already added to your shopping cart.</div>
                <?php break;
            case 'connection_refused': ?>
                <div>There was a problem on trying to add the document to your shopping cart. Please try again.</div>
                <?php break;
            case 'document_not_found': ?>
                <div>The document you are trying to add to your shopping cart is currently unavailable.</div>
                <?php break;
        endswitch;
    endforeach; ?>
<?php else : ?>
    <div>Document successfully added to your shopping cart!</div>
<?php endif; ?>