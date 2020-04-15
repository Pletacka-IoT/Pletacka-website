<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;

use Nette;
use App\Model\DatabaseManager;

use Nette\Application\UI\Form;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use App\Presenters\BasePresenter;
use Nette\Utils\Strings;
use Nette\Http\Session;

final class SensorsPresenter extends BasePresenter
{
	const
		FORM_MSG_REQUIRED = 'Tohle pole je povinné.',
		FORM_MSG_RULE = 'Tohle pole má neplatný formát.';    
    
    private $databaseManager;
    private $request;
    private $session;
    private $urlParameter;

    

	public function __construct(DatabaseManager $databaseManager, Request $request, Session $session)
	{
        $this->databaseManager = $databaseManager;
        $this->request = $request;
        $this->session = $session;
    }

    ///////////////////
    //Pridani senzoru
    ///////////////////
    public function createComponentAddSensorForm(): Form
    {        
        $form = new Form; // means Nette\Application\UI\Form
    
        $form->addText('number', 'Cislo:') 
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->addRule(Form::INTEGER, self::FORM_MSG_RULE);

        $form->addText('name', 'Nazev:') 
            ->setRequired(self::FORM_MSG_REQUIRED);
        
        $form->addText('description', 'Popis:');           
            
        $form->addSubmit('send', 'Pridej');

        $form->onSuccess[] = [$this, 'AddSensorFormSucceeded'];
    
        return $form;
    }

    public function AddSensorFormSucceeded(Form $form, \stdClass $values): void
    {
        $returnMessage = $this->databaseManager->addNewSensor($values->number, $values->name, $values->description);
        if($returnMessage[0])
        {
            $this->flashMessage($returnMessage[2], 'success');
            $this->redirect('Sensors:sensor',$values->name);
        }
        else
        {
            
            $this->flashMessage($returnMessage[2], 'error');
            $this->redirect('this');
        }
    } 
    ///////////////////
    //Editace senzoru
    ///////////////////

    public function createComponentEditSensorxForm(): Form
    {        
        $form = new Form; // means Nette\Application\UI\Form
    
        $form->addText('oldname', 'Starý název1:') 
            ->setRequired(self::FORM_MSG_REQUIRED);
        
        $form->addText('number', 'Cislo:') 
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->addRule(Form::INTEGER, self::FORM_MSG_RULE);

        $form->addText('name', 'Nazev:') 
            ->setRequired(self::FORM_MSG_REQUIRED);        


        $form->addText('description', 'Popis:');           
            
        $form->addSubmit('send', 'Uprav');

        $form->onSuccess[] = [$this, 'EditSensorxFormSucceeded'];
    
        return $form;
    }    
    
    public function EditSensorxFormSucceeded(Form $form, \stdClass $values): void
    {
        $returnMessage = $this->databaseManager->editSensor($values->oldname, $values->number, $values->name, $values->description);
        if($returnMessage[0])
        {
            $this->flashMessage($returnMessage[2], 'success');
            $this->redirect('Sensors:sensor',$values->name);
        }
        else
        {
            
            $this->flashMessage($returnMessage[2], 'error');
            $this->redirect('this');
        }
    }    

    ///////////////////
    //Smazani senzoru
    ///////////////////

    public function createComponentDeleteSensorForm(): Form
    {        
        $form = new Form; // means Nette\Application\UI\Form

        $form->addText('name', 'Nazev:') 
            ->setRequired(self::FORM_MSG_REQUIRED);                  
            
        $form->addSubmit('send', 'Smaž');

        $form->onSuccess[] = [$this, 'DeleteSensorFormSucceeded'];
    
        return $form;
    }    
    
    public function DeleteSensorFormSucceeded(Form $form, \stdClass $values): void
    {
        $returnMessage = $this->databaseManager->deleteSensor($values->name);
        if($returnMessage[0])
        {
            $this->flashMessage($returnMessage[2], 'success');
            $this->redirect('this');
        }
        else
        {
            
            $this->flashMessage($returnMessage[2], 'error');
            $this->redirect('this');
        }
    }    


    public function renderDefault() : void
    {
        
        $this->template->sensors = $this->databaseManager->getSensors();

    } 


    public function renderSensor($name)
    {
        
        
        if(!$this->databaseManager->sensorIsExist($name))
        {
            $message = array(false, "This sensor does not exist","Tento senzor neexistuje");
            $this->flashMessage($message[2], 'error');
            $this->redirect('Sensors:default');
        }

        $this->template->sensor = $this->databaseManager->getSensorInfo($name);
        $this->template->name = $this->databaseManager->getSensorInfo($name)["name"];
        

    }

    ////////////////////////////////////////////////
    //  Edit page
    ////////////////////////////////////////////////


    public function createComponentEditSensorForm(): Form
    {        
        $form = new Form; // means Nette\Application\UI\Form
        
        $form->addText('number', 'Cislo:') 
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->addRule(Form::INTEGER, self::FORM_MSG_RULE);

        $form->addText('name', 'Nazev:') 
            ->setRequired(self::FORM_MSG_REQUIRED);        


        $form->addText('description', 'Popis:');       
        $form->addSubmit('send', 'Uprav');


        
        $form->onSuccess[] = [$this, 'EditSensorFormSucceeded'];
    
        return $form;
    }    
    
    public function EditSensorFormSucceeded(Form $form, \stdClass $values): void
    {
        $url = $this->request->getHeaders()["referer"];
        $exUrl = explode('/', $url);
        $exUrl = explode('?', $exUrl[7]);
        $oldSensor = $exUrl[0];
        
        
        $returnMessage = $this->databaseManager->editSensor($oldSensor,$values->number, $values->name, $values->description);
        if($returnMessage[0])
        {
            $this->flashMessage($returnMessage[2], 'success');
            $this->redirect('Sensors:sensor',$values->name);
        }
        else
        {
            
            $this->flashMessage($values->old."*".$returnMessage[2], 'error');
            $this->redirect('this');
        }
    }     

    public function renderEdit($name)
    {

        if(!$this->databaseManager->sensorIsExist($name))
        {
            $message = array(false, "This sensor does not exist","Tento senzor neexistuje");
            $this->flashMessage($message[2], 'error');
            $this->redirect('Sensors:default');
        }
        $sensor = $this->databaseManager->getSensorInfo($name);
        $this->template->sensor = $sensor;
        $this->template->name = $sensor["name"];
        $this['editSensorForm']->setDefaults($sensor);

    }

    ////////////////////////////////////////////////
    //  Delete sensor Page
    ////////////////////////////////////////////////

    public function actionDelete($name)
    {
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }        

        
        

        if(!$this->databaseManager->sensorIsExist($name))
        {
            $message = array(false, "This sensor does not exist","Tento senzor neexistuje");
            $this->flashMessage($message[2], 'error');
            $this->redirect('Sensors:default');
        }

        $returnMessage = $this->databaseManager->deleteSensor($name);
        if($returnMessage[0])
        {
            $this->flashMessage($returnMessage[2], 'success');
            $this->redirect('Sensors:default');
        }
        else
        {
            
            $this->flashMessage($returnMessage[2], 'error');
            $this->redirect('Sensors:default');
        }



        
    }

    ////////////////////////////////////////////////
    //  Test Page
    ////////////////////////////////////////////////

    public function renderTest($name, $next)
    {
        
        
        
    }

}