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

    /**
     * @brief ThisSensorFormFactory constructor.
     * @param FormFactory $formFactory
     */
    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @brief Create sensor Form
     */    
    public function show(callable $onSuccess): Form
    {
        $form = $this->formFactory->create();

        $form->addText('from', 'Od')
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->setDefaultValue("2020-05-05T05:00")
            ->setHtmlType('datetime-local');


        $form->addText('to', 'Do')
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->setDefaultValue("2020-05-05T23:43")
            ->setHtmlType('datetime-local');

//        $week = $form->addSubmit('week', "Week");
//        $week->onClick[] = function (Form $form, \stdClass $values) use ($onWeek) {
//
//            $onWeek($form, $values);
//        };



            
        $form->addSubmit('send', 'Zobrazit');
               
        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess) {
          
            $onSuccess($form, $values);
        };
    
        return $form;        
    }




    
}
