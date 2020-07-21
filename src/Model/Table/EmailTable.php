<?php

namespace App\Model\Table;
use Cake\ORM\Table;

class Email extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table('email');
        $this->primaryKey('id');
    }
    public function getMails($email_id){
        $this->find('all')->where(['email.to_email >' => $email_id]);
    }
}
