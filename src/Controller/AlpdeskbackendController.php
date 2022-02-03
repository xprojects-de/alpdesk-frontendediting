<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Controller;

use Contao\CoreBundle\Framework\ContaoFramework;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;
use Contao\Environment;
use Contao\Input;
use Contao\Controller;
use Contao\BackendUser;
use Contao\UserModel;
use Contao\PageModel;
use Contao\System;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Alpdesk\AlpdeskFrontendediting\Utils\Utils;

class AlpdeskbackendController extends AbstractController
{
    protected $contaoFramework;

    private $twig;
    private $csrfTokenManager;
    private $csrfTokenName;
    protected $router;
    private $security;

    private $session;

    public function __construct(ContaoFramework $contaoFramework, TwigEnvironment $twig, CsrfTokenManagerInterface $csrfTokenManager, string $csrfTokenName, RouterInterface $router, Security $security, SessionInterface $session)
    {
        $this->contaoFramework = $contaoFramework;
        $this->twig = $twig;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->csrfTokenName = $csrfTokenName;
        $this->router = $router;
        $this->security = $security;
        $this->session = $session;
    }

    private function toggleFullesize()
    {
        $userModel = UserModel::findById(BackendUser::getInstance()->id);
        if ($userModel !== null) {

            $userModel->fullscreen = ($userModel->fullscreen == 1 ? 0 : 1);
            $userModel->save();

        }

        Controller::reload();
    }

    private function toggleLiveModus()
    {
        $liveModus = $this->session->get('alpdeskfee_livemodus');

        if ($liveModus === true) {
            $this->session->set('alpdeskfee_livemodus', false);
        } else {
            $this->session->set('alpdeskfee_livemodus', true);
        }

        Controller::reload();
    }

    private function setPageAlias($id)
    {
        if ($id !== null && $id !== '') {

            $pageModel = PageModel::findById($id);

            if ($pageModel !== null) {
                $this->session->set('alpdeskfee_pageselect', $pageModel->id);
            } else {
                $this->session->set('alpdeskfee_pageselect', '');
            }

        }

        Controller::redirect($this->router->generate('alpdesk_frontendediting_backend'));
    }

    private function generatePreviewUrl()
    {
        $url = '/preview.php';

        $id = $this->session->get('alpdeskfee_pageselect');
        if ($id !== null && $id !== '') {

            $pageId = (int)$id;
            if ($pageId > 0) {

                $pageModel = PageModel::findById((int)$id);

                if ($pageModel !== null) {
                    $url .= '/' . Controller::replaceInsertTags('{{link_url::' . $pageModel->id . '}}');
                }

            }

        }

        return $url;
    }

    /**
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\SyntaxError
     */
    public function endpoint(): Response
    {
        $this->contaoFramework->initialize();

        $backendUser = $this->security->getUser();

        if (!$backendUser instanceof BackendUser) {
            return new Response($this->twig->render('@AlpdeskFrontendediting/alpdeskfee_be_error.html.twig', ['msg' => 'Permission denied']));
        }

        Utils::mergeUserGroupPersmissions();
        if (!$backendUser->isAdmin && $backendUser->alpdesk_fee_enabled != 1) {
            return new Response($this->twig->render('@AlpdeskFrontendediting/alpdeskfee_be_error.html.twig', ['msg' => 'Permission denied']));
        }

        if (Input::post('toggleFullsize')) {
            $this->toggleFullesize();
        } else if (Input::post('toggleLivemodus')) {
            $this->toggleLiveModus();
        } else if (Input::get('pageselect')) {
            $this->setPageAlias(Input::get('pageselect'));
        }

        System::loadLanguageFile('default');

        $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/alpdeskfrontendediting/js/alpdeskfrontendediting_be.js';
        $GLOBALS['TL_CSS'][] = 'bundles/alpdeskfrontendediting/css/alpdeskfrontendediting_be.css';
        $GLOBALS['TL_CSS'][] = 'bundles/alpdeskfrontendediting/css/angular/alpdeskfee-styles.css';

        $elements = [];
        $elementsData = Utils::getAlpdeskFeeElements(BackendUser::getInstance());
        if (\count($elementsData) > 0) {
            $elements = $elementsData;
        }
        $elements = \json_encode($elements);

        $outputTwig = $this->twig->render('@AlpdeskFrontendediting/alpdeskfee_be.html.twig', [
            'token' => $this->csrfTokenManager->getToken($this->csrfTokenName)->getValue(),
            'base' => Environment::get('base'),
            'livemodus' => $this->session->get('alpdeskfee_livemodus'),
            'url' => $this->generatePreviewUrl(),
            'cachingTime' => time(),
            'label_fullscreen' => $GLOBALS['TL_LANG']['alpdeskfee_backend_lables']['fullscreen'],
            'label_livemodus' => $GLOBALS['TL_LANG']['alpdeskfee_backend_lables']['live_mode'],
            'label_pageselect' => $GLOBALS['TL_LANG']['alpdeskfee_backend_lables']['page_select'],
            'elements' => $elements
        ]);

        return new Response($outputTwig);
    }

}
