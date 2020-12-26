<?php

declare(strict_types=1);

namespace App\TimeManagers;

use Nette;
use Nette\Database\Table\Selection;
use DateInterval;
use PhpParser\Node\Scalar\String_;
use Nette\Utils\DateTime;


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
	 * @var Selection
	 */
	private $previousEvent;


	/**
	 * @brief Constructor
	 * @param  $tableSelection
	 * @param  $previousEvent
	 * @param DateTime $startTime
	 * @param DateTime $endTime
	 */
	public function __construct($tableSelection, $previousEvent,DateTime $startTime, DateTime  $endTime)
	{
		//TODO change times to DateTime
		$this->tableSelection = $tableSelection;
		$this->startTime = $startTime;
		$this->endTime = $endTime;
		$this->previousEvent = $previousEvent;
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
	public function countEvents($state = NULL): int
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
     * @return int time in seconds
     */
    public function allTime(): int
    {
        $time = 0;
	    $start = 0;
        if($this->previousEvent)
        {
            $state = $this->previousEvent->state;
            if($state != self::OFF)
            {
                $state = self::ON;
	            $start  = $this->startTime->getTimestamp();
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
            $time += $this->endTime->getTimestamp() - $start;
        }

        return $time;

    }
//
//
//    /**
//     * @brief Get stop time
//     * @return int time in seconds
//     */
//    public function stopTime(): int
//    {
//        $sState = self::STOP;
//        $time = 0;
//        $start = 0;
//
//        foreach($this->tableSelection as $event)
//        {
//            switch ($sState) {
//                case self::STOP:
//                    if($event->state == self::STOP)
//                    {
//                        $sState = self::REWORK;
//                        $start = $event->time->getTimestamp();
////                        echo " -> STOP -> ".$start;
//                    }
//                    else if($event->state == self::OFF)
//                    {
//                        $start = $stop = 0;
//                        $sState = self::OFF;
//                    }
//                    break;
//                case self::REWORK:
//                    if($event->state == self::REWORK)
//                    {
//                        $stop = $event->time->getTimestamp();
//                        $sState = self::STOP;
//                        $time += $stop-$start;
////                        echo " -> REWORK -> ".$stop."-> ALL: ". $time;
//                    }
//                    else if($event->state == self::OFF)
//                    {
//	                    $time += $this->endTime->getTimestamp()-$start;
//                    	$start = $stop = 0;
//                        $sState = self::OFF;
//                    }
//                    break;
//                case self::OFF:
//                    if($event->state == self::ON)
//                    {
//                        $sState = self::STOP;
//                    }
//                    break;
//            }
//        }
//
//        if($sState == self::REWORK)
//        {
//	        $time += $this->endTime->getTimestamp()-$start;
//        }
//        return $time;
//    }


	/**
	 * @brief Get stop time
	 * @return int time in seconds
	 */
	public function stopTime(): int
	{
		$time = 0;
		$start = 0;
		$sStateLast = "";
		$first = true;


//		if($this->previousEvent == self::STOP)
//		{
//			$start = $this->startTime->getTimestamp();
//		}

		foreach($this->tableSelection as $event)
		{
			$sState = $event;
			switch ($event->state) {
				case self::STOP:
					$start = $event->time->getTimestamp();
					break;
				case self::REWORK:
					if($first)
					{
						$start = $this->startTime->getTimestamp();
						$time += $event->time->getTimestamp()-$start;
					}
					else
					{
						$time += $event->time->getTimestamp()-$start;
					}
					break;
				case self::OFF:
					if($sStateLast == self::STOP)
					{
						$time += $event->time->getTimestamp()-$start;
					}
					break;
			}
			$first = false;
			$sStateLast = $event->state;
		}

		if($sState->state == self::STOP)
		{
			$time += $this->endTime->getTimestamp()-$start;
		}
		return $time;
//		dump($time);
//		return 300;
	}

	/**
	 * @brief Get last stop time
	 * @param DateTime $now
	 * @return int time in seconds
	 */
    public function lastStopTime(DateTime $now)
    {
	    if (($stop = $this->tableSelection[array_key_last($this->tableSelection)])->state != self::STOP) {
		    return null;
	    }

	    $stop = $stop->time->getTimestamp();
	    $start = $now->getTimestamp();

	    return $start - $stop;

    }


	/**
	 * @brief Get work time
	 * @param int $allTime
	 * @param int $stopTime
	 * @return int time in second
	 */
	public function workTime(int $allTime, int $stopTime)
	{
		return $allTime-$stopTime;
	}
    
}