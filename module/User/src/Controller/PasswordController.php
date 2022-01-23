<?php
namespace User\Controller;

use Application\Controller\AbstractController;
use Application\Utilities\Mailer;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Form\Form;
use \RuntimeException;
use \DateTime;
use User\Form\ResetPassword;
use User\Model\User;
use User\Filter\RegistrationHash;

class PasswordController extends AbstractController
{

    /**
     *
     * @var User\Model\UserTable $userTable
     */
    protected $userTable;

    public function __construct(TableGatewayInterface $table)
    {
        $this->userTable = $table;
        // Debug::dump($this->userTable, __CLASS__.'::'.__LINE__);
    }

    public function resetAction()
    {
        /*
         * TODO: create timestamp representing now
         * TODO: store now stamp in the db
         * TODO: add 86,400 seconds to that stamp and save it as expire time
         * TODO: write reset email method in Utilities\Mailer to send reset password email with link and token
         * TODO: use a /:step param to divide workflow
         * TODO: create form for resetting password
         * testing commit
         */
        try {
            //$mailer = $this->sm->get('Application\Utilities\Mailer');
            $step = $this->params('step', 'zero');
            $this->logger->log(6, "$step");
            $dateTime = new DateTime('NOW');
            $options = ['db' => $this->userTable, 'enableCaptcha' => $this->appSettings->enableCaptcha];
            $form = new ResetPassword(null, $options);
            $this->view->setVariable('showForm', true);
            switch ($step)
            {
                case 'submit-email':
                    $startTime = $dateTime->format($this->appSettings->timeFormat);
                    $formData = ['resetTimeStamp' => $startTime, 'step' => 'two'];
                    $form->remove('password');
                    $form->remove('conf_password');
                    $form->setAttribute('action', '/user/password/reset/send-email');
                    $form->setData($formData);
                    break;
                case 'send-email':
                    if($this->request->isPost())
                    {
                        $this->view->setVariable('showForm', false);
                        $form->setValidationGroup('email');
                        $post = $this->request->getPost();
                        $form->setData($post);
                        if($form->isValid()) {
                            $data = $form->getData();
                        }
                        $user = $this->userTable->fetchByColumn('email', $post['email']);

                        if ($user instanceof User)
                        {
                            $filter = new RegistrationHash();
                            $hash = $filter->filter(['email' => $post['email'], 'timestamp' => $post['resetTimeStamp']]);
                            $user->resetTimeStamp = $post['resetTimeStamp'];
                            $user->resetHash = $hash;
                            if($user->save())
                            {
                                // send reset email 
                                /**
                                 * @var Application\Utilities\Mailer $mailer
                                 */
                                $mailer = $this->sm->get('Application\Utilities\Mailer');
                                try {
                                    //code...
                                    $mailer->sendMessage($post['email'], Mailer::RESET_PASSWORD, $hash);
                                } catch (\Throwable $th) {
                                    $this->logger->log(2, $th->getMessage());
                                }
                                // redirect
                                $this->logger->log(6, 'Password change request', $user->getLogData());
                            }
                            else {
                                throw new RuntimeException('Information not saved');
                            }
                        }
                    }
                    break;
                case 'reset-password':
                    
                break;
            }
            $this->view->setVariable('form', $form);
            return $this->view;
            //$this->debug::dump();
            if ($step !== 'two') {
                throw new RuntimeException('Step is not two');
            }
        } catch (RuntimeException $e) {
            $this->logger->log(2, $e->getMessage());
        }
    }
    public function progressAction()
    {
        $step = $this->params('step');
        
        return $this->view;
    }
}
