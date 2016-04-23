<form action="/site/search" method="GET">
    Search
    <input type="text" name="searchtext" value="<?php echo $searchText; ?>">

    Tags
    <input type="text" name="tags" value="<?php echo $tags; ?>">
    <input type="submit" value="Search">
</form>
<?php foreach($documentList as $documentItem) : ?>
<div>
    <div>Name: <?php echo $documentItem->name; ?></div>
    <div>Description: <?php echo $documentItem->description; ?></div>
    <div>Tags: <?php foreach($documentItem->getTags() as $tagObject) : ?>
            <span><?php echo $tagObject->name; ?></span>
    <?php endforeach; ?></div>
</div>
<?php endforeach; ?>