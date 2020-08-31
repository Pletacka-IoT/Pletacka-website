<?php

declare(strict_types=1);

namespace App\CoreModule\Forms;

use Nette;
use App\Forms\FormFactory;
use Nette\Application\UI\Form;

/**
 * @brief Factory for work shift
 */
final class WorkShiftFormFactory
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
    public function createWSselect(callable $onSuccess): Form
    {
        $form = $this->formFactory->create();
        
        $form->addText('year', 'Rok:')
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->addRule(Form::INTEGER, self::FORM_MSG_RULE)
            ->setDefaultValue(date("Y"));

        $form->addSelect('ws', 'Lichá směna:', ["c" => "Cahovi", "v" => "Vaňkovi"]) //"Cahovi" => "c", "Vaňkovi" => "v"
            ->setDefaultValue('c');
            
        $form->addSubmit('send', 'Nastav');
               
        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess) {
          
            $onSuccess($form, $values);
        };
    
        return $form;        
    }


}
