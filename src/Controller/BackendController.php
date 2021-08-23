<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Controller;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Core\Security;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Contao\BackendUser;
use Contao\System;
use Contao\ContentModel;

// Check Route: sudo /Applications/MAMP/bin/php/php7.4.9/bin/php vendor/bin/contao-console debug:router

class BackendController extends AbstractController
{
    private $security;
    private $tokenChecker;
    private $backendUser = null;
    private $csrfTokenManager;
    private $csrfTokenName;
    private $requestStack;
    private $scopeMatcher;

    public static $STATUSCODE_OK = 200;
    public static $STATUSCODE_COMMONERROR = 400;
    public static $TARGETTYPE_PAGE = 'page';
    public static $TARGETTYPE_ARTICLE = 'article';
    public static $TARGETTYPE_CE = 'ce';
    public static $TARGETTYPE_MOD = 'mod';
    public static $TARGETTYPE_INFO = 'info';
    public static $ACTION_PARENT_EDIT = 'parent_edit';
    public static $ACTION_ELEMENT_EDIT = 'element_edit';
    public static $ACTION_ELEMENT_VISIBILITY = 'element_visibility';
    public static $ACTION_ELEMENT_DELETE = 'element_delete';
    public static $ACTION_ELEMENT_SHOW = 'element_show';
    public static $ACTION_ELEMENT_NEW = 'element_new';
    public static $ACTION_ELEMENT_COPY = 'element_copy';
    public static $ACTION_ELEMENT_CUT = 'element_cut';
    public static $ACTION_ELEMENT_DRAG = 'element_drag';
    public static $ACTION_ELEMENT_PASTEAFTER = 'element_pasteafter';
    public static $ACTION_CLIPBOARD = 'clipboard';
    public static $ACTION_NEWRECORDS = 'new_records';

    public function __construct(TokenChecker $tokenChecker, Security $security, CsrfTokenManagerInterface $csrfTokenManager, string $csrfTokenName, RequestStack $requestStack, ScopeMatcher $scopeMatcher)
    {
        $this->tokenChecker = $tokenChecker;
        $this->security = $security;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->csrfTokenName = $csrfTokenName;
        $this->requestStack = $requestStack;
        $this->scopeMatcher = $scopeMatcher;

        $this->getBackendUser();
    }

    private function getBackendUser()
    {
        if ($this->tokenChecker->hasBackendUser()) {

            $user = $this->security->getUser();

            if ($user instanceof BackendUser) {
                $this->backendUser = $user;
            }
        }
    }

    /**
     * @throws \Exception
     */
    private function checkAccess()
    {
        $isBackend = $this->scopeMatcher->isBackendRequest($this->requestStack->getCurrentRequest());

        if ($isBackend === false || $this->backendUser === null) {
            throw new \Exception('No Access');
        }
    }

    /**
     * @throws \Exception
     */
    private function checkToken($token)
    {
        $token = new CsrfToken($this->csrfTokenName, $token);

        $valid = $this->csrfTokenManager->isTokenValid($token);
        if ($valid !== true) {
            throw new \Exception('Invalid Token');
        }
    }

    public function endpoint(Request $request): JsonResponse
    {
        try {

            $this->checkAccess();

            $data = (array)$request->request->get('data');
            $rt = (string)$request->request->get('rt');

            if (\count($data) == 0) {
                $content = $request->getContent();
                $json = \json_decode($content, true);
                $data = \json_decode($json['data'], true);
                $rt = (string)$json['rt'];
            }

            $this->checkToken($rt);

            $response = new JsonResponse('COMMOM ERROR', self::$STATUSCODE_COMMONERROR);

            switch ($data['targetType']) {

                case self::$TARGETTYPE_CE:
                {
                    $response = $this->procceedType_CE($data);
                    break;
                }

                case self::$TARGETTYPE_ARTICLE:
                {
                    $response = $this->procceedType_Article($data);
                    break;
                }

                case self::$TARGETTYPE_INFO:
                {
                    $response = $this->procceedType_Info($data);
                    break;
                }

                default:
                    break;
            }

            return $response;

        } catch (\Exception $ex) {
            return (new JsonResponse($ex->getMessage(), self::$STATUSCODE_COMMONERROR));
        }

    }

    /**
     * @throws \Exception
     */
    private function procceedType_CE($data): JsonResponse
    {
        // currently we do not need any Access-Check because only the clipboard is modified and no record of Database is affected
        // In future be careful!

        if ($data['action'] == self::$ACTION_ELEMENT_COPY || $data['action'] == self::$ACTION_ELEMENT_CUT) {

            $objSession = System::getContainer()->get('session');

            $clipboard = $objSession->get('CLIPBOARD');

            if (!\is_array($clipboard) || $clipboard === null) {
                $clipboard = [];
            }

            if ($data['action'] == self::$ACTION_ELEMENT_COPY) {

                $clipboard['tl_content'] = [
                    'childs' => null,
                    'id' => \intval($data['id']),
                    'mode' => 'copy',
                    'alpdeskptable' => $data['do']
                ];

            } else if ($data['action'] == self::$ACTION_ELEMENT_CUT) {

                $clipboard['tl_content'] = [
                    'childs' => null,
                    'id' => \intval($data['id']),
                    'mode' => 'cut',
                    'alpdeskptable' => $data['do']
                ];

            }

            $objSession->set('CLIPBOARD', $clipboard);
            $data['clipboard'] = $clipboard;

            // If copy/cut is pressed from Frontend blacklist all other new Records
            // whitlisting not possibel because we do not know the next ID
            $objSessionBag = $objSession->getBag('contao_backend');
            $new_records = $objSessionBag->get('new_records');

            if (!\is_array($new_records) || $new_records === null) {
                $new_records = [];
            }

            $objSessionBag->set('alpdeskfee_blacklist', $new_records);
            $data['new_records'] = $new_records;
            $data['alpdeskfee_blacklist'] = $objSessionBag->get('alpdeskfee_blacklist');

            return (new JsonResponse($data));

        } else if ($data['action'] == self::$ACTION_ELEMENT_NEW) {

            System::getContainer()->get('session')->set('CURRENT_ID', \intval($data['pid']));
            $currentid = System::getContainer()->get('session')->get('CURRENT_ID');
            $data['CURRENT_ID'] = $currentid;

            if (\array_key_exists('element_type', $data) && $data['element_type'] !== null && $data['element_type'] !== '') {
                System::getContainer()->get('session')->set('alpdeskfee_tl_content_element_type', (string)$data['element_type']);
            } else {
                System::getContainer()->get('session')->set('alpdeskfee_tl_content_element_type', null);
            }

            return (new JsonResponse($data));
        }

        throw new \Exception('invalid action');
    }

    /**
     * @throws \Exception
     */
    private function procceedType_Article($data): JsonResponse
    {
        // currently we do not need any Access-Check because only the clipboard is modified and no record of Database is affected
        // In future be careful!

        if ($data['action'] == self::$ACTION_ELEMENT_NEW) {

            System::getContainer()->get('session')->set('CURRENT_ID', \intval($data['id']));
            $currentid = System::getContainer()->get('session')->get('CURRENT_ID');
            $data['CURRENT_ID'] = $currentid;

            return (new JsonResponse($data));
        }

        throw new \Exception('invalid action');
    }

    /**
     * @throws \Exception
     */
    private function procceedType_Info($data): JsonResponse
    {
        // currently we do not need any Access-Check because only the clipboard is modified and no record of Database is affected
        // In future be careful!

        if ($data['action'] == self::$ACTION_CLIPBOARD) {

            $clipboard = System::getContainer()->get('session')->get('CLIPBOARD');
            if (!\is_array($clipboard) || $clipboard === null) {
                $clipboard = [];
            }

            return (new JsonResponse($clipboard));

        } else if ($data['action'] == self::$ACTION_NEWRECORDS) {

            $objSession = System::getContainer()->get('session');
            $objSessionBag = $objSession->getBag('contao_backend');

            $new_records = $objSessionBag->get('new_records');
            if (!\is_array($new_records) || $new_records === null) {
                $new_records = [];
            }

            $new_records_blacklist = $objSessionBag->get('alpdeskfee_blacklist');
            if (!\is_array($new_records_blacklist) || $new_records_blacklist === null) {
                $new_records_blacklist = [];
            }

            $tlContentBlacklist = [];
            if (\count($new_records_blacklist) > 0) {
                foreach ($new_records_blacklist as $key => $value) {
                    if ($key == 'tl_content') {
                        if (\is_array($value)) {
                            foreach ($value as $uId) {
                                \array_push($tlContentBlacklist, \intval($uId));
                            }
                        } else {
                            \array_push($tlContentBlacklist, \intval($value));
                        }
                    }
                }
            }

            $updatedrecords = [];
            if ($data['updateContentRecords'] == true && \count($new_records) > 0) {
                foreach ($new_records as $key => $value) {
                    if ($key == 'tl_content') {
                        if (\is_array($value)) {
                            foreach ($value as $uId) {
                                if (!\in_array(\intval($uId), $tlContentBlacklist)) {
                                    $contentModel = ContentModel::findById(\intval($uId));
                                    if ($contentModel !== null) {
                                        if ($contentModel->tstamp == 0) {
                                            $contentModel->tstamp = time();
                                            $contentModel->save();
                                            \array_push($updatedrecords, \intval($uId));
                                        }
                                    }
                                }
                            }
                        } else {
                            if (!\in_array(\intval($value), $tlContentBlacklist)) {
                                $contentModel = ContentModel::findById(\intval($value));
                                if ($contentModel !== null) {
                                    if ($contentModel->tstamp == 0) {
                                        $contentModel->tstamp = time();
                                        $contentModel->save();
                                        \array_push($updatedrecords, \intval($value));
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $result = [
                'new_records' => $new_records,
                'new_records_blacklist' => $new_records_blacklist,
                'updatedrecords' => $updatedrecords
            ];

            return (new JsonResponse($result));
        }

        throw new \Exception('invalid action');
    }

}
