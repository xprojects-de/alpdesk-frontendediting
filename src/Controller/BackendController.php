<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Controller;

use Symfony\Component\Security\Core\Security;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Contao\BackendUser;
use Contao\System;

// CHeck Route: sudo /Applications/MAMP/bin/php/php7.4.9/bin/php vendor/bin/contao-console debug:router

class BackendController extends AbstractController {

  private $security = null;
  private $tokenChecker = null;
  private $backendUser = null;
  public static $STATUSCODE_OK = 200;
  public static $STATUSCODE_COMMONERROR = 400;
  public static $TARGETTYPE_PAGE = 'page';
  public static $TARGETTYPE_ARTICLE = 'article';
  public static $TARGETTYPE_CE = 'ce';
  public static $TARGETTYPE_MOD = 'mod';
  public static $ACTION_PARENT_EDIT = 'parent_edit';
  public static $ACTION_ELEMENT_EDIT = 'element_edit';
  public static $ACTION_ELEMENT_VISIBILITY = 'element_visibility';
  public static $ACTION_ELEMENT_DELETE = 'element_delete';
  public static $ACTION_ELEMENT_SHOW = 'element_show';
  public static $ACTION_ELEMENT_NEW = 'element_new';
  public static $ACTION_ELEMENT_COPY = 'element_copy';

  public function __construct(TokenChecker $tokenChecker, Security $security) {
    $this->tokenChecker = $tokenChecker;
    $this->security = $security;
    $this->getBackendUser();
  }

  private function getBackendUser() {
    if ($this->tokenChecker->hasBackendUser()) {
      $user = $this->security->getUser();
      if ($user instanceof BackendUser) {
        $this->backendUser = $user;
      }
    }
  }

  private function checkAccess() {
    if (TL_MODE !== 'BE' || $this->backendUser === null) {
      throw new \Exception('No Access');
    }
  }

  public function endpoint(Request $request): JsonResponse {

    try {

      $this->checkAccess();

      $data = (array) $request->request->get('data');

      $response = new JsonResponse('COMMOM ERROR', self::$STATUSCODE_COMMONERROR);

      switch ($data['targetType']) {
        case self::$TARGETTYPE_CE: {
            $response = $this->procceedType_CE($data);
            break;
          }
        default:
          break;
      }

      return $response;
    } catch (\Exception $ex) {
      return (new JsonResponse($exception->getMessage(), self::$STATUSCODE_COMMONERROR));
    }
  }

  private function procceedType_CE($data): JsonResponse {

    // currently we do not need any Access-Check because only the clipboard is modified and no record of Database is affected
    // In future be careful!

    if ($data['action'] == self::$ACTION_ELEMENT_COPY) {

      $clipboard = System::getContainer()->get('session')->get('CLIPBOARD');

      if (!\is_array($clipboard) || $clipboard === null) {
        $clipboard = [];
      }

      $clipboard['tl_content'] = [
          'childs' => null,
          'id' => \intval($data['id']),
          'mode' => 'copy'
      ];

      System::getContainer()->get('session')->set('CLIPBOARD', $clipboard);

      $data['clipboard'] = $clipboard;

      return (new JsonResponse($data));
    }

    throw new \Exception('invalid action');
  }

}
