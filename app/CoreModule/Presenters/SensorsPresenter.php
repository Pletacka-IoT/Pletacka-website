<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;

use App\CoreModule\Forms\SensorsFormFactory;
use Nette;
use App\CoreModule\Model\SensorManager;

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
    
    private $sensorManager;
    private $request;
    private $session;
    private $urlParameter;
    private $sensorsFormFactory;

    

	public function __construct(SensorManager $sensorManager, Request $request, Session $session, SensorsFormFactory $sensorsFormFactory)
	{
        $this->sensorManager = $sensorManager;
        $this->request = $request;
        $this->session = $session;
        $this->sensorsFormFactory = $sensorsFormFactory;
    }

    ///////////////////
    //Pridani senzoru
    ///////////////////
    public function createComponentAddSensorForm(): Form
    {
		return $this->sensorsFormFactory->createCreate(function (Form $form, \stdClass $values) {
            $returnMessage = $this->sensorManager->addNewSensor($values->number, $values->name, $values->description);
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
		});        
    }

    ///////////////////
    //Editace senzoru
    /////////////////// 
    public function createComponentEditSensorxForm(): Form
    {
		return $this->sensorsFormFactory->createEditOld(function (Form $form, \stdClass $values) {
            $returnMessage = $this->sensorManager->editSensor($values->oldname, $values->number, $values->name, $values->description);
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
		});        
    }

    ///////////////////
    //Smazani senzoru
    ///////////////////  
    public function createComponentDeleteSensorForm(): Form
    {
		return $this->sensorsFormFactory->createDelete(function (Form $form, \stdClass $values) {
            $returnMessage = $this->sensorManager->deleteSensor($values->name);
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
		});        
    }    

    ////////////////////////////////////////////////
    //  Default page
    ////////////////////////////////////////////////

    public function renderDefault() : void
    {
        
        $this->template->sensors = $this->sensorManager->getSensors();

    } 


    public function renderSensor($name)
    {
        
        
        if(!$this->sensorManager->sensorIsExist($name))
        {
            $message = array(false, "This sensor does not exist","Tento senzor neexistuje");
            $this->flashMessage($message[2], 'error');
            $this->redirect('Sensors:default');
        }

        $this->template->sensor = $this->sensorManager->getSensorInfo($name);
        $this->template->name = $this->sensorManager->getSensorInfo($name)["name"];
        

    }

    ////////////////////////////////////////////////
    //  Edit page
    ////////////////////////////////////////////////
    
    public function createComponentEditSensorForm(): Form
    {
		return $this->sensorsFormFactory->createEdit(function (Form $form, \stdClass $values) {
            $url = $this->request->getHeaders()["referer"];
            $exUrl = explode('/', $url);
            $exUrl = explode('?', $exUrl[7]);
            $oldSensor = $exUrl[0];
            
            
            $returnMessage = $this->sensorManager->editSensor($oldSensor,$values->number, $values->name, $values->description);
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
		});        
    }    
    

    public function renderEdit($name)
    {

        if(!$this->sensorManager->sensorIsExist($name))
        {
            $message = array(false, "This sensor does not exist","Tento senzor neexistuje");
            $this->flashMessage($message[2], 'error');
            $this->redirect('Sensors:default');
        }
        $sensor = $this->sensorManager->getSensorInfo($name);
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

        
        

        if(!$this->sensorManager->sensorIsExist($name))
        {
            $message = array(false, "This sensor does not exist","Tento senzor neexistuje");
            $this->flashMessage($message[2], 'error');
            $this->redirect('Sensors:default');
        }

        $returnMessage = $this->sensorManager->deleteSensor($name);
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
        // $output = array();
        // $sensors = $this->sensorManager->getSensors();
        // foreach($sensors as $sensor)
        // {
        //     dump($sensor);
        //     echo($sensor->name);
        //     $output[] = array('number'=>$sensor->number, 'name'=>$sensor->name, 'description'=>$sensor->description);
        // }
        // $xout = array();
        // $xout = array('sensors'=>$output);
        // $xout['status'] = 'kuba';
        // dump($xout);
        // echo json_encode($xout);
        // dump( $xout['sensors']);

        // $sensor = $this->sensorManager->getSensorsNumber(3);
        // dump(array('number'=>$sensor->number, 'name'=>$sensor->name, 'description'=>$sensor->description));

        // if($this->sensorManager->sensorIsExist(5, 'number'))

        
    }

}