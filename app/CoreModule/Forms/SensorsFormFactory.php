<?php

declare(strict_types=1);

namespace App\CoreModule\Forms;

use Nette;
use App\Forms\FormFactory;
use Nette\Application\UI\Form;


final class SensorsFormFactory
{
    use Nette\SmartObject;
    
    const
        FORM_MSG_REQUIRED = 'Tohle pole je povinné.',
        FORM_MSG_RULE = 'Tohle pole má neplatný formát.'; 

    protected $formFactory;

    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * Create sensor Form
     */    
    public function createCreate(callable $onSuccess): Form
    {
        $form = $this->formFactory->create();
        
        $form->addText('number', 'Číslo:')
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->addRule(Form::INTEGER, self::FORM_MSG_RULE);
        
        $form->addText('description', 'Popis:');           
            
        $form->addSubmit('send', 'Přidej'); 
               
        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess) {
          
            $onSuccess($form, $values);
        };
    
        return $form;        
    }

    /**
     * Edit sensor Form
     */
    public function createEdit(callable $onSuccess): Form
    {
        $form = $this->formFactory->create();
        
        $form->addText('number', 'Číslo:')
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->addRule(Form::INTEGER, self::FORM_MSG_RULE);

        $form->addText('description', 'Popis:');       
        $form->addSubmit('send', 'Uprav');
               
        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess) {
          
            $onSuccess($form, $values);
        };
    
        return $form;        
    }     

    /**
     * Edit sensor Form - with old name parameter
     */    
    public function createEditOld(callable $onSuccess): Form
    {
        $form = $this->formFactory->create();
        
        $form->addText('oldnumber', 'Staré číslo:')
            ->setRequired(self::FORM_MSG_REQUIRED);
        
        $form->addText('number', 'Číslo:')
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->addRule(Form::INTEGER, self::FORM_MSG_RULE);

        $form->addText('name', 'Název:')
            ->setRequired(self::FORM_MSG_REQUIRED);        


        $form->addText('description', 'Popis:');           
            
        $form->addSubmit('send', 'Uprav');
               
        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess) {
          
            $onSuccess($form, $values);
        };
    
        return $form;        
    }  
    

    /**
     * Delete sensor Form
     */    
    public function createDelete(callable $onSuccess): Form
    {
        $form = $this->formFactory->create();
        
        $form->addText('name', 'Název:')
            ->setRequired(self::FORM_MSG_REQUIRED);                  
            
        $form->addSubmit('send', 'Smaž');
               
        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess) {
            $onSuccess($form, $values);
        };
    
        return $form;        
    }  


    
}
