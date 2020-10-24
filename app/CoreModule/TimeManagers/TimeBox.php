<?php

declare(strict_types=1);

namespace App\TimeManagers;

use Nette;
use Nette\Database\Table\Selection;
use DateInterval;
use PhpParser\Node\Scalar\String_;
use DateTime;


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
     * @param           $startTime
     * @param           $endTime
     */
	public function __construct($tableSelection, String $startTime, String $endTime)
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
     * @param string $previousEvent
     * @return int time in seconds
     */
    public function allTime($previousEvent)
    {
        $time = 0;
        if($previousEvent)
        {
            $state = $previousEvent->state;
            if($state != self::OFF)
            {
                $state = self::ON;
	            $x = new DateTime($this->startTime);
	            $start = $x->getTimestamp();
            }
            else
            {
	            $state = self::OFF;
	            $start = $this->tableSelection[array_key_first($this->tableSelection)]->time->getTimestamp();
            }
        }
        else
        {
            $state = self::OFF;
            $start = $this->tableSelection[array_key_first($this->tableSelection)]->time->getTimestamp();
        }

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

                case self::FINISHED:
                case self::REWORK:
                case self::STOP:
                	if($state == self::OFF)
	                {
		                $start = $event->time->getTimestamp();
		                $state = self::ON;
	                }
	                break;
            }
        }

        if($state != self::OFF)
        {
	        $y = new DateTime($this->endTime);
	        $stop = $y->getTimestamp();

//        	$stop = $this->tableSelection[array_key_last($this->tableSelection)]->time->getTimestamp();
            $time += $stop - $start;
        }

        return $time;

    }


    /**
     * @brief Get stop time
     * @param $previousEvent
     * @return int time in seconds
     */
    public function stopTime($previousEvent)
    {
        $sState = self::STOP;
        $time = 0;
        $start = 0;

        // Fix time after last $event
//	    ISSUE

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


        if($sState == self::REWORK)
        {
	        $x = new DateTime($this->endTime);
        	$stop = $x->getTimestamp();
	        $time += $stop-$start;
        }
        return $time;
    }

	/**
	 * @brief Get last stop time
	 * @param Nette\Utils\DateTime $now
	 * @return int time in seconds
	 */
    public function lastStopTime(Nette\Utils\DateTime $now)
    {
		if(($stop = $this->tableSelection[array_key_last($this->tableSelection)])->state != self::STOP)
		{
			return null;
		}

	    $stop = $stop->time->getTimestamp();
		$start = $now->getTimestamp();

		return $start-$stop;



//    	$table = array_reverse($this->tableSelection);
//        $first = true;
//
//        foreach($table as $event)
//        {
//            if($first)
//            {
//				$stop = $event->time->getTimestamp();
//            }
//            else if($event->state == self::)
//            {
//
//            }


//        }
//
//        return ;
//
//        return $time;
    }

    /**
     * @brief Get work time
     * @param $previousEvent
     * @return int time in second
     */
    public function workTime($previousEvent)
    {
        $time = $this->allTime($previousEvent)-$this->stopTime($previousEvent);
        return $time;
    }

    /**
     * @brief Get average stop time
     * @param $previousEvent
     * @return int time in seconds
     */
    public function avgStopTime($previousEvent)
    {
        $count = $this->countEvents(self::STOP);

        if($count>0)
        {
            return ceil($this->stopTime($previousEvent)/$count);
        }
        else
            return 0;

    }

    /**
     * @brief Get average work time
     * @param $previousEvent
     * @return int time in seconds
     */
    public function avgWorkTime($previousEvent)
    {
        $count = $this->countEvents(self::FINISHED);

        if($count>0)
        {
            return ceil($this->workTime($previousEvent)/$count);
        }
        else
            return 0;
    }
    
}