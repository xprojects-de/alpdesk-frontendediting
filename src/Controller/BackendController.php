<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Controller;

use Alpdesk\AlpdeskFrontendediting\Utils\Utils;
use Contao\CoreBundle\Framework\ContaoFramework;
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
use Contao\ContentModel;

class BackendController extends AbstractController
{
    protected ContaoFramework $contaoFramework;

    private Security $security;
    private TokenChecker $tokenChecker;
    private ?BackendUser $backendUser = null;
    private CsrfTokenManagerInterface $csrfTokenManager;
    private string $csrfTokenName;
    private RequestStack $requestStack;
    private ScopeMatcher $scopeMatcher;

    public static int $STATUSCODE_OK = 200;
    public static int $STATUSCODE_COMMONERROR = 400;
    public static string $TARGETTYPE_PAGE = 'page';
    public static string $TARGETTYPE_ARTICLE = 'article';
    public static string $TARGETTYPE_CE = 'ce';
    public static string $TARGETTYPE_MOD = 'mod';
    public static string $TARGETTYPE_INFO = 'info';
    public static string $ACTION_PARENT_EDIT = 'parent_edit';
    public static string $ACTION_ELEMENT_EDIT = 'element_edit';
    public static string $ACTION_ELEMENT_VISIBILITY = 'element_visibility';
    public static string $ACTION_ELEMENT_DELETE = 'element_delete';
    public static string $ACTION_ELEMENT_SHOW = 'element_show';
    public static string $ACTION_ELEMENT_NEW = 'element_new';
    public static string $ACTION_ELEMENT_COPY = 'element_copy';
    public static string $ACTION_ELEMENT_CUT = 'element_cut';
    public static string $ACTION_ELEMENT_DRAG = 'element_drag';
    public static string $ACTION_ELEMENT_PASTEAFTER = 'element_pasteafter';
    public static string $ACTION_CLIPBOARD = 'clipboard';
    public static string $ACTION_NEWRECORDS = 'new_records';

    public function __construct(
        ContaoFramework           $contaoFramework,
        TokenChecker              $tokenChecker,
        Security                  $security,
        CsrfTokenManagerInterface $csrfTokenManager,
        string                    $csrfTokenName,
        RequestStack              $requestStack,
        ScopeMatcher              $scopeMatcher
    )
    {
        $this->contaoFramework = $contaoFramework;
        $this->tokenChecker = $tokenChecker;
        $this->security = $security;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->csrfTokenName = $csrfTokenName;
        $this->requestStack = $requestStack;
        $this->scopeMatcher = $scopeMatcher;

    }

    /**
     * @throws \Exception
     */
    private function checkAccess(): void
    {
        $this->contaoFramework->initialize();

        if (!$this->tokenChecker->hasBackendUser()) {
            throw new \Exception('No Access');
        }

        $user = $this->security->getUser();

        if (!$user instanceof BackendUser) {
            throw new \Exception('No Access');
        }

        if (!$this->scopeMatcher->isBackendRequest($this->requestStack->getCurrentRequest())) {
            throw new \Exception('No Access');
        }

        $this->backendUser = $user;

        Utils::mergeUserGroupPersmissions($this->backendUser);

    }

    /**
     * @param string|null $token
     * @return void
     * @throws \Exception
     */
    private function checkToken(?string $token): void
    {
        $tokenObject = new CsrfToken($this->csrfTokenName, $token);

        $valid = $this->csrfTokenManager->isTokenValid($tokenObject);
        if ($valid !== true) {
            throw new \Exception('Invalid Token');
        }
    }

    public function checkPermissions(Request $request): JsonResponse
    {
        try {

            $this->checkAccess();

            if (!$this->backendUser instanceof BackendUser) {
                throw new \Exception('invalid user');
            }

            $content = $request->getContent();

            if (!\is_string($content)) {
                throw new \Exception('invalid inputData');
            }

            $data = \json_decode($content, true);

            if (
                !\is_array($data) ||
                !\array_key_exists('data', $data)
            ) {
                throw new \Exception('invalid type');
            }

            switch ((string)$data['type']) {

                case 'global':
                {
                    $response = [
                        'isAdmin' => $this->backendUser->isAdmin,
                        'isAdminDisabled' => ($this->backendUser->alpdesk_fee_admin_disabled !== null && (int)$this->backendUser->alpdesk_fee_admin_disabled === 1)
                    ];

                    break;
                }

                default:
                    throw new \Exception('invalid type');
            }

            return new JsonResponse($response);

        } catch (\Exception $ex) {
            return (new JsonResponse($ex->getMessage(), self::$STATUSCODE_COMMONERROR));
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
     * @param array $data
     * @return JsonResponse
     * @throws \Exception
     */
    private function procceedType_CE(array $data): JsonResponse
    {
        // currently we do not need any Access-Check because only the clipboard is modified and no record of Database is affected
        // In future be careful!

        $objSession = $this->requestStack->getCurrentRequest()->getSession();

        if ($data['action'] == self::$ACTION_ELEMENT_COPY || $data['action'] == self::$ACTION_ELEMENT_CUT) {

            $clipboard = $objSession->get('CLIPBOARD');

            if (!\is_array($clipboard)) {
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

            if (!\is_array($new_records)) {
                $new_records = [];
            }

            $objSessionBag->set('alpdeskfee_blacklist', $new_records);
            $data['new_records'] = $new_records;
            $data['alpdeskfee_blacklist'] = $objSessionBag->get('alpdeskfee_blacklist');

            return (new JsonResponse($data));

        } else if ($data['action'] == self::$ACTION_ELEMENT_NEW) {

            $objSession->set('CURRENT_ID', \intval($data['pid']));
            $currentid = $objSession->get('CURRENT_ID');
            $data['CURRENT_ID'] = $currentid;

            if (\array_key_exists('element_type', $data) && $data['element_type'] !== null && $data['element_type'] !== '') {
                $objSession->set('alpdeskfee_tl_content_element_type', (string)$data['element_type']);
            } else {
                $objSession->set('alpdeskfee_tl_content_element_type', null);
            }

            return (new JsonResponse($data));
        }

        throw new \Exception('invalid action');
    }

    /**
     * @param array $data
     * @return JsonResponse
     * @throws \Exception
     */
    private function procceedType_Article(array $data): JsonResponse
    {
        // currently we do not need any Access-Check because only the clipboard is modified and no record of Database is affected
        // In future be careful!

        $objSession = $this->requestStack->getCurrentRequest()->getSession();

        if ($data['action'] == self::$ACTION_ELEMENT_NEW) {

            $objSession->set('CURRENT_ID', \intval($data['id']));
            $currentid = $objSession->get('CURRENT_ID');
            $data['CURRENT_ID'] = $currentid;

            return (new JsonResponse($data));
        }

        throw new \Exception('invalid action');
    }

    /**
     * @throws \Exception
     */
    private function procceedType_Info(array $data): JsonResponse
    {
        // currently we do not need any Access-Check because only the clipboard is modified and no record of Database is affected
        // In future be careful!

        $objSession = $this->requestStack->getCurrentRequest()->getSession();

        if ($data['action'] == self::$ACTION_CLIPBOARD) {

            $clipboard = $objSession->get('CLIPBOARD');
            if (!\is_array($clipboard)) {
                $clipboard = [];
            }

            return (new JsonResponse($clipboard));

        } else if ($data['action'] == self::$ACTION_NEWRECORDS) {

            $objSessionBag = $objSession->getBag('contao_backend');

            $new_records = $objSessionBag->get('new_records');
            if (!\is_array($new_records)) {
                $new_records = [];
            }

            $new_records_blacklist = $objSessionBag->get('alpdeskfee_blacklist');
            if (!\is_array($new_records_blacklist)) {
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
