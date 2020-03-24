<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Model\DatabaseManager;
use Nette\Application\UI\Form;


final class SensorsPresenter extends Nette\Application\UI\Presenter
{
    private $databaseManager;

	public function __construct(DatabaseManager $databaseManager)
	{
		$this->databaseManager = $databaseManager;
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

    public function createComponentEditSensorForm(): Form
    {        
        $form = new Form; // means Nette\Application\UI\Form
    
        $form->addText('oldname', 'Starý název:') 
            ->setRequired();
        
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
        $addNew = $this->databaseManager->editSensor($values->oldname, $values->number, $values->name, $values->description);
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
            $this->redirect('Sensors:sensor',$values->name);
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
    
    public function renderSensor($name, $next)
    {
        $this->template->settings = $this->databaseManager->getTitleSettings();
        
        $this->template->name = $name;
        $this->template->a = "";
        
        
        $res = $this->databaseManager->addNewSensor(25,"MojeMasina");

        $this->template->b = $res[0];
        $this->template->c = $res[1];
    }

}