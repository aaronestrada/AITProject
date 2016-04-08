<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Created at</th>
        <th>Status</th>
    </tr><?php foreach($tagList as $tagItem) : ?>
    <tr>
        <td><?php echo $tagItem->id; ?></td>
        <td><?php echo $tagItem->name; ?></td>
        <td><?php echo $tagItem->created_at; ?></td>
        <td><?php echo $tagItem->status == 1 ? 'Active' : 'Inactive'; ?></td>
    </tr><?php endforeach; ?>
</table>