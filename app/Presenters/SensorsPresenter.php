<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Model\DatabaseManager;
use Nette\Application\UI\Form;
use Nette\Http\Request;
use Nette\Http\UrlScript;


final class SensorsPresenter extends Nette\Application\UI\Presenter
{
    private $databaseManager;
    private $request;

	public function __construct(DatabaseManager $databaseManager, Nette\Http\Request $request)
	{
        $this->databaseManager = $databaseManager;
        $this->request = $request;
    }

    ///////////////////
    //Pridani senzoru
    ///////////////////
    public function createComponentAddSensorForm(): Form
    {        
        $form = new Form; // means Nette\Application\UI\Form
    
        $form->addText('number', 'Cislo:') 
            ->setRequired();

        $form->addText('name', 'Nazev:') 
            ->setRequired();
        
        $form->addText('description', 'Popis:');           
            
        $form->addSubmit('send', 'Pridej');

        $form->onSuccess[] = [$this, 'AddSensorFormSucceeded'];
    
        return $form;
    }

    public function AddSensorFormSucceeded(Form $form, \stdClass $values): void
    {
        $addNew = $this->databaseManager->addNewSensor($values->number, $values->name, $values->description);
        if($addNew[0])
        {
            $this->flashMessage($addNew[2], 'success');
            $this->redirect('Sensors:sensor',$values->name);
        }
        else
        {
            
            $this->flashMessage($addNew[2], 'error');
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
            ->setRequired();
        
        $form->addText('number', 'Cislo:') 
            ->setRequired();

        $form->addText('name', 'Nazev:') 
            ->setRequired();        


        $form->addText('description', 'Popis:');           
            
        $form->addSubmit('send', 'Uprav');

        $form->onSuccess[] = [$this, 'EditSensorxFormSucceeded'];
    
        return $form;
    }    
    
    public function EditSensorxFormSucceeded(Form $form, \stdClass $values): void
    {
        $addNew = $this->databaseManager->editSensorx($values->oldname, $values->number, $values->name, $values->description);
        if($addNew[0])
        {
            $this->flashMessage($addNew[2], 'success');
            $this->redirect('Sensors:sensor',$values->name);
        }
        else
        {
            
            $this->flashMessage($addNew[2], 'error');
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
            ->setRequired();                  
            
        $form->addSubmit('send', 'Smaž');

        $form->onSuccess[] = [$this, 'DeleteSensorFormSucceeded'];
    
        return $form;
    }    
    
    public function DeleteSensorFormSucceeded(Form $form, \stdClass $values): void
    {
        $addNew = $this->databaseManager->deleteSensor($values->name);
        if($addNew[0])
        {
            $this->flashMessage($addNew[2], 'success');
            $this->redirect('this');
        }
        else
        {
            
            $this->flashMessage($addNew[2], 'error');
            $this->redirect('this');
        }
    }    


    public function renderDefault() : void
    {
        $this->template->settings = $this->databaseManager->getTitleSettings();

    } 


    public function renderSensor($name)
    {
        $this->template->settings = $this->databaseManager->getTitleSettings();
        
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
            ->setRequired();

        $form->addText('name', 'Nazev:') 
            ->setRequired();        


        $form->addText('description', 'Popis:');           
            
        $form->addSubmit('send', 'Uprav');

        $form->onSuccess[] = [$this, 'EditSensorFormSucceeded'];
    
        return $form;
    }    
    
    public function EditSensorFormSucceeded(Form $form, \stdClass $values): void
    {
        $url = $this->request->getHeaders()["referer"];
        $exUrl = explode('/', $url);
        $exUrl = explode('?', $exUrl[6]);
        $oldSensor = $exUrl[0];
        
        
        $addNew = $this->databaseManager->editSensor($oldSensor,$values->number, $values->name, $values->description);
        if($addNew[0])
        {
            $this->flashMessage($addNew[2], 'success');
            $this->redirect('Sensors:sensor',$values->name);
        }
        else
        {
            
            $this->flashMessage($addNew[2], 'error');
            $this->redirect('this');
        }
    }     

    public function renderEdit($name)
    {
        $this->template->settings = $this->databaseManager->getTitleSettings();

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
    //  Test Page
    ////////////////////////////////////////////////

    public function renderTest($name, $next)
    {
        $this->template->settings = $this->databaseManager->getTitleSettings();
        
    }

}