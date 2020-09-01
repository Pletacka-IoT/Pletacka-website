<?php

declare(strict_types=1);

namespace App\TimeManagers;

use Nette;
use Nette\Database\Table\Selection;
use DateInterval;


/**
 * @brief The main time counting class
 *
 * Input database selection
 */
class TimeBox
{
	use Nette\SmartObject;

    public const
        FINISHED = 'FINISHED',  // Machine is working
        STOP = "STOP",	        // Machine is not working
        REWORK = "REWORK", 	    // State after end of STOP
        ON = 'ON',              // ON machine
        OFF = 'OFF';            // OFF machine



	private $tableSelection;
	private $startTime;
	private $endTime;

	/**
	 * @brief Constructor
	 * @param Selection $tableSelection
	 */
	public function __construct($tableSelection, $startTime, $endTime)
	{
		$this->tableSelection = $tableSelection;
		$this->startTime = $startTime;
		$this->endTime = $endTime;
	}

    /**
     * @brief Get events
     * @return Selection
     */
	public function getEvents()
	{
		return $this->tableSelection;
	}

    /**
     * @brief Count of table rows of specific Pletacka state or all states (null)
     * @param null $state Pletacka state or all states (null)
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
     * @brief Get all pletacka time
     * @return int time in seconds
     */
    public function allTime()
    {
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

        return $time;

    }


    /**
     * @brief Get stop time
     * @return int time in seconds
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
        return $time;
    }

    /**
     * @brief Get work time
     * @return array time in second
     */
    public function workTime()
    {
        $time = $this->allTime()-$this->stopTime();
        return $time;
    }

    /**
     * @brief Get average stop time
     * @return int time in seconds
     */
    public function avgStopTime()
    {
        $count = $this->countEvents(self::STOP);

        if($count>0)
        {
            return ceil($this->stopTime()/$count);
        }
        else
            return 0;

    }

    /**
     * @brief Get average work time
     * @return int time in seconds
     */
    public function avgWorkTime()
    {
        $count = $this->countEvents(self::FINISHED);

        if($count>0)
        {
            return ceil($this->workTime()/$count);
        }
        else
            return 0;
    }
    
}