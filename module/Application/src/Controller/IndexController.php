<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Controller\AbstractController;
use Application\Form\ContactForm;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractController
{

    public function indexAction()
    {
        return $this->view;
    }
    public function contactAction()
    {
        $form = new ContactForm('contact-us', $this->appSettings->toArray());
        $form->setInputFilter($form->addInputFilter());
        if($this->request->isPost())
        {
            $validationGroup = ['fullName', 'email', 'message'];
            if($this->appSettings->enableCaptcha) 
            {
               $validationGroup[] = 'captcha';
            }
            $form->setValidationGroup($validationGroup);
            $form->setData($this->request->getPost()->toArray());
            if($form->isValid())
            {
                $data = $form->getData();
                $mailer = $this->sm->get('Application\Utilities\Mailer');
                $mailer->contactUsMessage($data['email'], $data['fullName'], $data['message']);
                $this->flashMessenger()->addSuccessMessage('Thank you for contacting us, your message was sent');
                return $this->redirect()->toRoute('home');
            }
        } else {
            
        }
        $this->view->setVariable('form', $form);
        return $this->view;
    }
    public function forbiddenAction()
    {
        $this->response->setStatusCode(403);
        return $this->view;
    }
}
