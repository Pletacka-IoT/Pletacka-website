<?php

declare(strict_types=1);

namespace App\TimeManagers;

use Nette;
use Nette\Database\Table\Selection;
use DateInterval;

/**
 * Class create pretty output in specific format
 */
class TimeBox
{
	use Nette\SmartObject;

    public const
        MINUTE = 60;

    public const
        FINISHED = 'FINISHED',	    // Machine is working
        STOP = "STOP",	    // Machine is not working
        REWORK = "REWORK", 	// State after end of STOP
        ON = 'ON',          // ON machine
        OFF = 'OFF';        // OFF machine

	private $tableSelection;

	/**
	 * Constructor
	 * @param Selection $tableSelection
	 */
	public function __construct($tableSelection)
	{
		$this->tableSelection = $tableSelection;
	}


	public function getEvents()
	{
		return $this->tableSelection;
	}

    /**
     * Count of table rows
     *
     * @param null $state
     * @return int count
     */
	public function countEvents($state = NULL)
	{

	    $count = 0;
		foreach($this->tableSelection as $event)
		{
			// dump($event->id);
            if($state == NULL)
            {
                $count++;
            }
            else
            {
                if($event->state == $state)
                    $count++;
            }
		}
		return $count;
	}


    /**
     * @return array
     * @throws \Exception
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function allTime()
    {
        $first = $this->tableSelection[array_key_first($this->tableSelection)]->time->getTimestamp();
        $last = $this->tableSelection[array_key_last($this->tableSelection)]->time->getTimestamp();
        $res =  $last-$first;
        return array(new DateInterval("PT" . $res . "S"), $res);
    }


    /**
     * Get stop time
     * @return array
     * @throws \Exception
     */
    public function stopTime()
    {
        $sState = 0;
        $time = 0;
        $start = 0;

        foreach($this->tableSelection as $event)
        {
            switch ($sState) {
                case 0:
                    if($event->state == self::STOP)
                    {
                        $sState = 1;
                        $start = $event->time->getTimestamp();
//                        echo " -> STOP -> ".$start;
                    }
                    break;
                case 1:
                    if($event->state == self::REWORK)
                    {
                        $stop = $event->time->getTimestamp();
                        $sState = 0;
                        $time += $stop-$start;
//                        echo " -> REWORK -> ".$stop."-> ALL: ". $time;
                    }
                    break;
            }
        }
        return array(new DateInterval("PT".$time."S"), $time);

    }

    /**
     * Get work time
     * @return array
     * @throws \Exception
     */
    public function workTime()
    {
        $time = $this->allTime()[1]-$this->stopTime()[1];
        return array(new DateInterval("PT".$time."S"), $time);
    }

    /**
     * Average stop time
     * @return array
     * @throws \Exception
     */
    public function avgStopTime()
    {
        $time = ceil($this->stopTime()[1]/$this->countEvents(self::STOP));
        return array(new DateInterval("PT".$time."S"), $time);
    }

    /**
     * Average work time
     * @return array
     * @throws \Exception
     */
    public function avgWorkTime()
    {
        $time = ceil($this->workTime()[1]/$this->countEvents(self::FINISHED));
        return array(new DateInterval("PT".$time."S"), $time);
    }
    
}