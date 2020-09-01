<?php

declare(strict_types=1);

namespace App\CoreModule\Control;

use Nette;
use App\Forms\FormFactory;
use Nette\Application\UI\Form;
use Nette\Application\UI\Control;

/**
 * @brief
 */
class PletackaChartControl extends  Control{

    private $poolId;

    public function __construct(int $poolId)
    {
        $this->poolId = $poolId;
    }

    public function render(int $sNumber, string $from, string $to, string $type, $stateType)
    {
        echo "Cau".$this->poolId;
        return "Cau".$this->poolId;
    }

}