<?php
   echo $this->Form->create(NULL,array('url'=>'/mail'));
   echo $this->Form->input('email', ['type' => 'email','name'=> 'email_id','label'=>'Email']);
   echo $this->Form->input('password', ['type' => 'password','name'=> 'pwd','label'=>'Password']);
   echo $this->Form->button('Submit');
   echo $this->Form->end();
?>