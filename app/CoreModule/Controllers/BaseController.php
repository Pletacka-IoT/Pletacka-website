<?php declare(strict_types = 1);

namespace App\CoreModule\Controllers;

use Apitte\Core\Annotation\Controller\GroupPath;
use Apitte\Core\UI\Controller\IController;

/**
 * Main API class - extend IController
 * @GroupPath("/api")
 */
abstract class BaseController implements IController
{

}
