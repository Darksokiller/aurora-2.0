<?php
namespace User\Controller;

use \RuntimeException;
use Application\Controller\AbstractController;
use User\Model\UserTable;
use User\Model\User;
use User\Form\LoginForm;
use User\Form\EditUserForm;
use User\Filter\FormFilters;
use Laminas\Db\RowGateway\RowGatewayInterface;
use Laminas\Db\TableGateway\TableGateway as Table;
//use Application\Model\RowGateway\RowGateway as Prototype;
use Laminas\Authentication\Result;
//use Laminas\Validator\Db\NoRecordExists;

class UserController extends AbstractController
{
    /**
     * 
     * @var $table UserTable
     */
    public $table;
    
    public function __construct(UserTable $table)
    {
        $this->table = $table;
    }
    public function _init() {}
    public function indexAction()
    {
        try {
            $userName = $this->params('userName');
            $hasMessage = false;
            if(!empty($userName)) {
                $this->fm = $this->plugin('flashMessenger');
                $this->fm->addSuccessMessage('User ' . $userName . ' was successfully deleted!!');
                $hasMessage = true;
            }
            $this->view->setVariable('hasMessage', $hasMessage);
            $this->view->setVariable('users', $this->table->fetchAll());
            $this->view->setVariable('roleFilter', $this->sm->get('User\Filter\RoleFilter'));
            return $this->view;
        } catch (RuntimeException $e) {
            
        }
    }
    public function editAction()
    {
        try {
            $logout = false;
            // get the user by userName that is to be edited
            $userName = $this->params()->fromRoute('userName');
            // this is the proper fetch for a user, all other calls are to be removed
            $user = $this->table->fetchByColumn('userName', $userName);
            //if($this->acl->isAllowed())
            
            // if they can not edit the user there is no point in preceeding
            if( ! $this->acl->isAllowed($this->user, $user, $this->action) ) {
                $this->flashMessenger()->addWarningMessage('You do not have the required permissions to edit users');
                $this->redirect()->toUrl('/forbidden');
            }
            else {
                $options = [];
                $options['acl'] = $this->acl;
                $options['settings'] = $this->appSettings;
                $options['rolesTable'] = $this->sm->get('User\Model\RolesTable');
                $options['user'] = $this->user;
                $form = new EditUserForm(null, $options);
                $form->get('submit')->setAttribute('value', 'Edit');
                $request = $this->getRequest();
                // if this is not a post lets return early
                $viewData['userName'] = $user->userName;
                if (! $request->isPost()) {
                    // bind the queried user data to the form
                    $form->bind($user);
                    // we should only need this when its not post, when form is initially built
                    $viewData['form'] = $form;
                    $this->view->setVariables($viewData);
                    return $this->view;
                }
                $filters = new FormFilters();
                // Set the input filters in the form object
                $form->setInputFilter($filters->getEditUserFilter($this->table, $user->id));
                // get the posted data
                $post = $request->getPost();
                
                // Set the posted data in the form so that it can be validated
                $form->setData($request->getPost());
                // Validate the posted data via the filters set in the form object
                // TODO: Fix this, this form object has no filters or validators defined in the form class
                if (! $form->isValid()) {
                    $this->view->form = $form;
                    return $this->view;
                }
                // testing
                $validatedData = $form->getData();
                
                // ok so just because we have a valid password doesnt mean we want to use it here
                if($post['password'] === '') {
                    /** 
                     * In this condition using the validatedData['password'] means its a passwordhash'ed empty string
                     * the user will no longer be able to login we cant use it
                     */
                    unset($validatedData['password']);
                }
                $result = $this->table->save($validatedData, true);
                if($result) {
                    // Redirect to User list
                    return $this->redirect()->toRoute('user', ['action' => 'index']);
                }
                else {
                    throw new \RuntimeException('The user could not be updated at this time');
                }
            }
        } catch (RuntimeException $e) {
            
        }
    }
    
    public function deleteAction()
    {
        try {
            $userName = $this->params()->fromRoute('userName');
            $user = $this->table->fetchByColumn('userName', $userName);
            $deletedUser = $user->toArray();
            if($this->acl->isAllowed($this->user, $user, $this->action)) {
                $result = $user->delete();
                if($result > 0) {
                    $this->logger->info('User ' . $this->user->userName . ' deleted user: ' . $deletedUser['userName'], 
                                        [
                                            'userId' => $this->user->id, 
                                            'userName' => $this->user->userName,
                                            'role' => $this->user->role,
                                        ]);
                    $this->redirect()->toRoute('user', ['action' => 'index', 'userName' => $deletedUser['userName']]);
                }
                else {
                    throw new RuntimeException('The requested action could not be completed');
                }
            }
            else {
                $this->flashMessenger()->addErrorMessage('Forbidden action');
                $this->redirect()->toRoute('forbidden');
            }
        } catch (RuntimeException $e) {
            
        }
    }
    public function forgotPasswordAction()
    {
        
    }
    public function logoutAction()
    {
        switch ($this->authService->hasIdentity())
        {
            case true :
                $this->authService->clearIdentity();
                return $this->redirect()->toUrl('/');
                break;
            case false:
                
                break;
            default:
                
                break;
        }
    }
    public function loginAction()
    {
        //var_dump($this->sm);
        $form = new LoginForm();
        $form->get('submit')->setValue('Login');
        $request = $this->getRequest();
        if (! $request->isPost()) {
            return ['form' => $form];
        }
        // get the post data
        $post = $request->getPost();
        $filters = new FormFilters();
        // set the input filters on the form object
        $form->setInputFilter($filters->getLoginFilter());
        // set the posted data in the form objects context
        $form->setData($request->getPost());
        // check with the form object to verify data is valid
        if (! $form->isValid()) {
            return ['form' => $form];
        }
        $validData = $form->getData();
        $user = $this->table->fetchByColumn('userName', $validData['userName']);
        /** DO NOT change the following line as the password property must be set to the posted password!!!!
         * if changed login fails
         */
        $user->password = $validData['password'];
        $loginResult = $this->table->login($user);
        if($loginResult instanceof RowGatewayInterface) {
            $this->flashMessenger()->addInfoMessage('Welcome back!!');
            $this->redirect()->toRoute('profile', ['userName' => $loginResult->userName]);
        }
        else {
            $messages = $loginResult->getMessages();
            switch($loginResult->getCode()) {
                case Result::FAILURE_IDENTITY_NOT_FOUND :
                    $element = $form->get('userName');
                    $messages[] = 'If you are certain you have registered you may need to verify your account before you can login';
                    $element->setMessages($messages);
                    break;
                case Result::FAILURE_CREDENTIAL_INVALID :
                    $element = $form->get('password');
                    $element->setMessages($messages);
                    break;
            }
            $this->view->setVariable('form', $form);
            return $this->view;
        }
    }
    public function loginFailureAction() {}
}