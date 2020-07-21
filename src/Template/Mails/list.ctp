<div class="container">
    <table>
        <thead>
            <tr>    
                <th>#</th>
                <th>To</th>
                <th>From</th>
                <th>Subject</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mails as  $value) {?>
            <tr>
                <td><?=$value['id'];?></td>
                <td><?=$value['to_email'];?></td>
                <td><?=$value['email_id'];?></td>
                <td><?php echo $this->Html->link($value['subject'],['controller' => 'Mails', 'action' => 'view', $value['id']]); ?></td>
            </tr>
            <?php }?>
        </tbody>
    </table>
</div>