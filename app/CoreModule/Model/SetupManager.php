<?php

namespace App\CoreModule\Model;


use Exception;
use Nette;
use Nette\Database\Context;
use App\Utils\Pretty;
// use App\CoreModule\Exceptions;
use App\Exceptions;
use App\Exceptions\MyException;

/**
 * @brief Manage sensor with name
 */
class SetupManager
{
	use Nette\SmartObject;

    private $database;
    
    public function __construct( Context $database )
    {
        $this->database = $database;
    }


    public function importantTablesAreExist()
    {
    	$count = 0;

	    try {
            $ret = $this->database->table("users")->fetch(); //number is ID in table settings
        } catch (Nette\InvalidArgumentException $e) {
			$this->createTableUsers();
			$count++;
        }

	    try {
		    $ret = $this->database->table("workShift")->fetch(); //number is ID in table settings
	    } catch (Nette\InvalidArgumentException $e) {
		    $this->createTableWorkShift();
		    $count++;
	    }

	    try {
		    $ret = $this->database->table("settings")->fetch(); //number is ID in table settings
	    } catch (Nette\InvalidArgumentException $e) {
		    $this->createTableSettings();
		    $count++;
	    }

	    try {
		    $ret = $this->database->table("sensors")->fetch(); //number is ID in table settings
	    } catch (Nette\InvalidArgumentException $e) {
		    $this->createTableSensors();
		    $count++;
	    }

		return $count;

    }

    public function createTableUsers()
    {
	    $this->database->query("
            DROP TABLE IF EXISTS `users`;
			CREATE TABLE `users` (
	            `id` int(11) NOT NULL AUTO_INCREMENT,
	            `username` varchar(255) COLLATE utf8_czech_ci NOT NULL,
	            `password` varchar(255) COLLATE utf8_czech_ci NOT NULL,
	            `role` varchar(255) COLLATE utf8_czech_ci NOT NULL,
				  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
		");
    }

	public function createTableWorkShift()
	{
		$this->database->query("
			DROP TABLE IF EXISTS `workShift`;
			CREATE TABLE `workShift` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `year` year(4) NOT NULL,
			  `week` int(11) NOT NULL,
			  `wsA` enum('Cahovi','Vaňkovi') COLLATE utf8_czech_ci NOT NULL,
			  `wsB` enum('Cahovi','Vaňkovi') COLLATE utf8_czech_ci NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;				
		");
	}

	public function createTableSettings()
	{
		$this->database->query("
			DROP TABLE IF EXISTS `settings`;
			CREATE TABLE `settings` (
			  `ID` int(10) NOT NULL AUTO_INCREMENT,
			  `web_name` varchar(60) COLLATE utf8_czech_ci NOT NULL,
			  `web_description` text COLLATE utf8_czech_ci NOT NULL,
			  `title_footer` varchar(60) COLLATE utf8_czech_ci NOT NULL,
			  `work_shift_A` varchar(60) COLLATE utf8_czech_ci NOT NULL,
			  `work_shift_B` varchar(60) COLLATE utf8_czech_ci NOT NULL,
			  `title_pair_count` varchar(60) COLLATE utf8_czech_ci NOT NULL,
			  `title_error_count` varchar(60) COLLATE utf8_czech_ci NOT NULL,
			  `title_succes_rate` varchar(60) COLLATE utf8_czech_ci NOT NULL,
			  `title_stop_time` varchar(60) COLLATE utf8_czech_ci NOT NULL,
			  PRIMARY KEY (`ID`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;			
		");
	}

	public function createTableSensors()
	{
		$this->database->query("
			DROP TABLE IF EXISTS `sensors`;
			CREATE TABLE `sensors` (
			  `number` int(11) NOT NULL,
			  `description` text COLLATE utf8_czech_ci NOT NULL,
			  `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `status` enum('on','off') COLLATE utf8_czech_ci NOT NULL DEFAULT 'off',
			  PRIMARY KEY (`number`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;			
		");
	}



}
