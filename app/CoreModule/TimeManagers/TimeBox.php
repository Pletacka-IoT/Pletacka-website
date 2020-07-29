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
        MINUTE = 60,
        FIRST = 1;

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
        $sState = self::FIRST;
        $time = 0;
        $state = self::ON;

        $start = $this->tableSelection[array_key_first($this->tableSelection)]->time->getTimestamp();
//      $stop = $this->tableSelection[array_key_last($this->tableSelection)]->time->getTimestamp();


        foreach($this->tableSelection as $event)
        {
            echo "";
            switch($event->state)
            {
                // it is OFF
                case self::OFF:
                    $stop = $event->time->getTimestamp();
                    $time += $stop - $start;
                    $state = self::OFF;
                    break;

                // It is ON
                case self::ON:
                    $start = $event->time->getTimestamp();
                    $state = self::ON;
                    break;


//                case default:
//                    $time +=

            }

        }

        if($state != self::OFF)
        {
            $stop = $this->tableSelection[array_key_last($this->tableSelection)]->time->getTimestamp();
            $time += $stop - $start;
        }

        return array(new DateInterval("PT" . $time . "S"), $time);
    }



    /**
     * Get stop time
     * @return array
     * @throws \Exception
     */
    public function stopTime()
    {
        $sState = self::STOP;
        $time = 0;
        $start = 0;

        foreach($this->tableSelection as $event)
        {
            switch ($sState) {
                case self::STOP:
                    if($event->state == self::STOP)
                    {
                        $sState = self::REWORK;
                        $start = $event->time->getTimestamp();
//                        echo " -> STOP -> ".$start;
                    }
                    else if($event->state == self::OFF)
                    {
                        $start = $stop = 0;
                        $sState = self::OFF;
                    }
                    break;
                case self::REWORK:
                    if($event->state == self::REWORK)
                    {
                        $stop = $event->time->getTimestamp();
                        $sState = self::STOP;
                        $time += $stop-$start;
//                        echo " -> REWORK -> ".$stop."-> ALL: ". $time;
                    }
                    else if($event->state == self::OFF)
                    {
                        $start = $stop = 0;
                        $sState = self::OFF;
                    }
                    break;
                case self::OFF:
                    if($event->state == self::ON)
                    {
                        $sState = self::STOP;
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
        if($this->countEvents(self::STOP)>0)
        {
            $time = ceil($this->stopTime()[1]/$this->countEvents(self::STOP));
            return array(new DateInterval("PT".$time."S"), $time);
        }
        else
            return array(new DateInterval("PT0S"), 0);

    }

    /**
     * Average work time
     * @return array
     * @throws \Exception
     */
    public function avgWorkTime()
    {
        if($this->countEvents(self::FINISHED)>0)
        {
            $time = ceil($this->workTime()[1]/$this->countEvents(self::FINISHED));
            return array(new DateInterval("PT".$time."S"), $time);
        }
        else
            return array(new DateInterval("PT0S"), 0);
    }
    
}