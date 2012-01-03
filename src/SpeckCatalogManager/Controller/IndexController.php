<?php

namespace SpeckCatalogManager\Controller;

use Zend\Mvc\Controller\ActionController,
    SpeckCatalogManager\Service\FormService,
    SpeckCatalogManager\Entity,
    \Exception;

class IndexController extends ActionController
{
    protected $userService;
    protected $catalogService;
    protected $view = array();
    
    public function __construct()
    {
        if(!isset($_GET['constructor'])){
            $_GET['constructor'] = null;
        }
        if(!isset($_GET['entityId'])){
            $_GET['entityId'] = null;
        }
        $this->events()->attach('dispatch', array($this, 'preDispatch'), 100);
    }

    public function preDispatch($e)
    {
        $user = $this->userService->getAuthService()->getIdentity();
        if(!$user){
            return $this->redirect()->toRoute('edpuser');
        }
        $this->session = $this->sessionService->getSession($user);
        $this->formService = new FormService(); 
        $this->view['session'] = $this->session;
    }
    
    public function indexAction()
    {
        $this->prepPaginator('session', $this->session->getEntities());
        return $this->view;
    }
    
    private function getEntity($className, $constructor=null, $entityId=null, $notifyNew=false)
    {
        if($entityId){
            return $entity = $this->session->getEntityById($entityId);
        }else{
            if($notifyNew === false){
                $this->view['messages'][] = array(
                    'type' => 'success',
                    'status' => 'If you insist!', 
                    'message' => "You have just added a new {$className} ({$constructor}) to your session.",
                );
            }
            $class = '\SpeckCatalogManager\Entity\\'.$className;
            return new $class($constructor);
        } 
    }

    public function productAction()
    {
        $this->view['uomData'] = array('ea' => 'ea - each','bx' => 'bx - box','ca' => 'ca - case'); // hacky... for development
        $entityName = 'product';
        $entity = $this->getEntity(ucfirst($entityName), $_GET['constructor'], $_GET['entityId']);
        $this->view[$entityName] = $entity;
        $this->view['forms'] = $this->formService->getProductForms($entity);
        $this->prepPaginator('parentChoices', $entity->getParentChoices());
        return $this->view;
    }

    private function prepPaginator($name, $items=null, $perPage=10, $pageNum=1)
    {
        if(is_array($items)){ 
           $this->view["{$name}Paginator"] = \Zend\Paginator\Paginator::factory($items)
               ->setItemCountPerPage($perPage)->setCurrentPageNumber($pageNum); 
        }
    }

    public function setUserService($userService)
    {
        $this->userService = $userService;
    }

    public function setSessionService($catalogManagerService)
    {                                              
        $this->sessionService = $catalogManagerService;
    }

    protected function entitySearchAction()
    {
        $this->view['nolayout'] = true;
        return $this->view;
    }   

    protected function livePartialAction()
    {
        $this->view['nolayout'] = true;
        
        $className = $_GET['className'];
        $entityId = $_GET['entityId'];
        
        $this->view['partial'] = $className;
        $entity = $this->sessionService->getEntity($className, $entityId);
        $method = 'get'.ucfirst($className).'Forms';
        $this->view['forms'] = $this->formService->$method($entity);
        
        $this->view[$className] = $entity;
        return $this->view;
    }    
}                                   
