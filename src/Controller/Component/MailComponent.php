<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;

class MailComponent extends Component{
    
    protected $_defaultConfig = [];

    public function save($data){
        
        $emailTable = TableRegistry::getTableLocator()->get('email');
        $email = $emailTable->newEntity();

        $email->subject = $data['subj'];
        $email->body = $data['body'];
        $email->email_id = $data['email_id'];
        $email->to_email = $data['to_email'];
        $email->email_received_time = $data['email_received_time'];
        $email->attachment = $data['attachment'];

        // $emailTable->save($email);
        if ($emailTable->save($email)) {
            $id = $email->id;
        }

    }

    public function getMails($email){
        $emailTable = TableRegistry::getTableLocator()->get('email');
        $emailTable->find('all')->where(['email.to_email >' => $email]);
    }
}