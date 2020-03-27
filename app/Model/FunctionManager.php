<?php

namespace App\Model;

use Nette;
use App\Model\DatabaseManager;
use App\Model\ThisSensorManager;
use  Nette\Security\User;


class FunctionManager
{
	use Nette\SmartObject;

    private $database;
    private $databaseManager;
    private $request;
    private $thisSensorManager;
    
    

	public function __construct(Nette\Database\Context $database, DatabaseManager $databaseManager, Nette\Http\Request $request, ThisSensorManager $thisSensorManager)
	{
        $this->databaseManager = $databaseManager;
        $this->request = $request;
        $this->thisSensorManager = $thisSensorManager;
        $this->database = $database;
    }  
    
    /**
     * Check if the user is logged in
     */
    public function checkLogin()
    {
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
    }


}