<button><?= $this->Html->link(__('Back'), $this->request->referer()) ?></button>
<div>
<span>
    <?=$mailbody['body']?>
</span>
<?php 
if(isset($mailbody['attachment'])){
    $this->Html->link('Link text', ROOT."/attachments/".$mailbody['attachment'],array('download'=>$mailbody['attachment']));
}
?>
</div>