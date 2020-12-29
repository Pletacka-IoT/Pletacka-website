<?php declare(strict_types = 1);

namespace App\CoreModule\Controllers;

use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\UI\Controller\IController;

/**
 * Main API class - extend IController
 * @Path("/api")
 */
abstract class BaseController implements IController
{

}
