<?php

namespace App\Model\Table;
use Cake\ORM\Table;

class Email extends Table
{


    /*
    * function to initialize email model
    * @param array
    */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table('email');
        $this->primaryKey('id');
    }
   
}
