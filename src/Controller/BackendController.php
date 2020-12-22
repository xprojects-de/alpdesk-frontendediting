<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Controller;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

// CHeck Route: sudo /Applications/MAMP/bin/php/php7.4.9/bin/php vendor/bin/contao-console debug:router

class BackendController extends AbstractController {

  protected $framework;
  private $tokenChecker = null;
  public static $STATUSCODE_OK = 200;
  public static $STATUSCODE_COMMONERROR = 400;

  public function __construct(ContaoFramework $framework, TokenChecker $tokenChecker) {
    $this->framework = $framework;
    //$this->framework->initialize();
    $this->tokenChecker = $tokenChecker;
  }

  public function endpoint(Request $request): JsonResponse {

    try {
      //$data = $request->request->get('data');
      return (new JsonResponse(['currently not supported. For future use']));
    } catch (\Exception $ex) {
      return (new JsonResponse($exception->getMessage(), self::$STATUSCODE_COMMONERROR));
    }
  }

}
