<?php

declare(strict_types=1);

namespace App\CoreModule\Forms;

use Nette;
use App\Forms\FormFactory;
use Nette\Application\UI\Form;


final class ThisSensorFormFactory
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
    public function show(callable $onSuccess): Form
    {
        $form = $this->formFactory->create();

        $form->addText('from', 'Od')
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->setDefaultValue("2020-07-08T22:43")
            ->setHtmlType('datetime-local');


        $form->addText('to', 'Do')
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->setDefaultValue("2020-10-08T22:43")
            ->setHtmlType('datetime-local');



            
        $form->addSubmit('send', 'Zobrazit');
               
        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess) {
          
            $onSuccess($form, $values);
        };
    
        return $form;        
    }




    
}
