<?php

namespace App\Controller;

use App\Controller\AppController;
use Exception;
use Cake\View\View;
use Cake\View\Helper\SessionHelper;
// use Cake\Filesystem\Folder;

class MailsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Mail');
        $this->loadComponent('Flash');
    }

    public function index()
    {
        if ($this->request->is('post')) {
            $data = $this->request->data;
            $response = $this->read_mail($data);

            foreach($response as $res){
                $this->Mail->save($res);
                $session = $this->getRequest()->getSession();
                $session->write('emailid', $data['email_id']);
            }
            $this->Flash->success('Unread Messages saved.');
            $this->redirect(['action' => 'list']);
        }
    }

    protected function read_mail($data)
    {
        try {
            /* connect to gmail */
            $hostname = '{imap.gmail.com:993/ssl/novalidate-cert}INBOX';
            $username = $data['email_id'];
            $password = $data['pwd'];

            /* try to connect */
            $inbox = imap_open($hostname, $username, $password)  or die('Cannot connect to Gmail: ' . imap_last_error());


            $count = 1;
            $out = [];
            /* get emails */
            $emails = imap_search($inbox, 'UNSEEN');
            $total_unread_mails = count($emails);
            
            if ($emails) {
                $max_emails = 4;
                rsort($emails);
                
                foreach ($emails as $email) {
                    $overview = imap_fetch_overview($inbox, $email, 0);
                    $body = quoted_printable_decode(imap_fetchbody($inbox, $email, 2));
                    $structure = imap_fetchstructure($inbox, $email);


                    $out[$count]['to_email'] = $username;
                    $out[$count]['subj'] = $overview[0]->subject;
                    $out[$count]['email_id'] = $overview[0]->from;
                    $out[$count]['email_received_time'] = $overview[0]->date;
                    $out[$count]['body'] = $body;



                    /* if any attachments found... */
                    if(isset($structure->parts) && count($structure->parts)) 
                    {
                        for($j = 0; $j < count($structure->parts); $j++) 
                        {
                            $attachments[$j] = ['is_attachment' => false,
                                'filename' => '',
                                'name' => '',
                                'attachment' => ''];

                            if($structure->parts[$j]->ifdparameters) 
                            {
                                foreach($structure->parts[$j]->dparameters as $obj) 
                                {
                                    if(strtolower($obj->attribute) == 'filename') 
                                    {
                                        $attachments[$j]['is_attachment'] = true;
                                        $attachments[$j]['filename'] = $obj->value;
                                    }
                                }
                            }

                            if($structure->parts[$j]->ifparameters) 
                            {
                                foreach($structure->parts[$j]->parameters as $obj) 
                                {
                                    if(strtolower($obj->attribute) == 'name') 
                                    {
                                        $attachments[$j]['is_attachment'] = true;
                                        $attachments[$j]['name'] = $obj->value;
                                    }
                                }
                            }

                            if($attachments[$j]['is_attachment']) 
                            {
                                $attachments[$j]['attachment'] = imap_fetchbody($inbox, $email, $j+1);

                                /* 3 = BASE64 encoding */
                                if($structure->parts[$j]->encoding == 3) 
                                { 
                                    $attachments[$j]['attachment'] = base64_decode($attachments[$j]['attachment']);
                                }
                                /* 4 = QUOTED-PRINTABLE encoding */
                                elseif($structure->parts[$j]->encoding == 4) 
                                { 
                                    $attachments[$j]['attachment'] = quoted_printable_decode($attachments[$j]['attachment']);
                                }
                            }
                        }

                        foreach ($attachments as $attachment) {
                            if ($attachment['is_attachment'] == 1) {
                                $filename = $attachment['name'];
                                if (empty($filename)) {
                                    $filename = $attachment['filename'];
                                }

                                if (empty($filename)) {
                                    $filename = time() . ".dat";
                                }

                                $out[$count]['attachment'] = $filename;
                                $attachment_dir = ROOT."/attachments/";
                                if(is_dir($attachment_dir)){
                                    @mkdir($attachment_dir,0777,true);
                                }
                                $fp = fopen($attachment_dir . $email . "-" . $filename, "wb");
                                @chmod($attachment_dir . $email . "-" . $filename, 777); 
                                fwrite($fp, $attachment['attachment']);
                                fclose($fp);
                            }
                        }

                    }


                    if ($count++ >= $max_emails) {
                        break;
                    }
                }
                

                imap_close($inbox);
                return $out;
            }
        } catch (Exception $e) {
            echo 'Cannot connect ' . $e->getMessage();
        }
    }

    public function list(){
        $session = $this->getRequest()->getSession();
        $emailid = $session->read('emailid');
        $this->loadModel('Email');
        $mails = $this->Email->find('all')
                ->where(['to_email ' => $emailid])
                ->select(['id','to_email', 'email_id', 'subject']);
        $output = $mails->toArray();        
        $this->set('mails',$output);        
    }

    public function view($id = null)
    {
        $session = $this->getRequest()->getSession();
        $emailid = $session->read('emailid');
        $this->loadModel('Email');
        $mail = $this->Email->get($id);
        $this->set('mailbody',$mail);
    }
}
