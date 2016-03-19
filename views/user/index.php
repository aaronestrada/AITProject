<table>
    <tr>
        <th>ID</th>
        <th>Filename</th>
        <th>Funder</th>
        <th>Status</th>
    </tr><?php foreach($documentList as $documentItem) : ?>
    <tr>
        <td><?php echo $documentItem['id']; ?></td>
        <td><?php echo $documentItem['filename']; ?></td>
        <td><?php echo $documentItem['funder']; ?></td>
        <td><?php echo $documentItem['status'] == 1 ? 'Active' : 'Inactive'; ?></td>
    </tr><?php endforeach; ?>
</table>
<form action="/user/index/num/1" method="POST">
    <input type="hidden" id="num" name="num" value="2">
    <input type="submit" value="Submit">
</form>