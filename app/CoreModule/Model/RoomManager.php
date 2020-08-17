<?php

namespace App\CoreModule\Model;

use Nette;
use Nette\Database\Context;
use App\CoreModule\Model\MultiSensorsManager;
use App\CoreModule\Model\ThisSensorManager;
use DateInterval;
use DateTimeZone;
use Nette\Utils\DateTime;
//use DateTime;
use DateTimeImmutable;
use Nette\Database\UniqueConstraintViolationException;
use App\Utils\Pretty;
use App\TimeManagers\TimeBox;


/**
 * @brief Manage rooms
 */
class RoomManager
{
    use Nette\SmartObject;




    private $database;
    private $defaultMsgLanguage;
    private $defaultAPILanguage;


    public function __construct( Context $database, MultiSensorsManager $multiSensorsManager, ThisSensorManager $thisSensorManager)
    {
        $this->database = $database;
    }

    /*
     * Sensor positions in Big Pletarna
     */
    public $roomPletarnaBig = array(
        array( 1,   2,   3,   4),
        array( 8,   7,   6,   5),
        array( 9,  -1,  -1,  18),
        array(10,  11,  12,  13),
        array(17,  16,  15,  14),
    );

    /*
     * Sensor positions in Small Pletarna
     */
    public $roomPletarnaSmall = array(
        array(20,  23),
        array(19,  22),
        array(-1,  21),
    );


}


