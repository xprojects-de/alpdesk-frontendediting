<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Listener;

use Alpdesk\AlpdeskFrontendediting\Mapping\MappingArticle;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\DC_Table;
use Contao\PageModel;
use Contao\ContentModel;
use Contao\ModuleModel;
use Contao\Module;
use Contao\Form;
use Contao\System;
use Contao\FrontendTemplate;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Alpdesk\AlpdeskFrontendediting\Utils\Utils;
use Alpdesk\AlpdeskFrontendediting\Custom\Custom;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;
use Alpdesk\AlpdeskFrontendediting\Events\AlpdeskFrontendeditingEventService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment as TwigEnvironment;

class HooksListener
{
    private TokenChecker $tokenChecker;
    private AlpdeskFrontendeditingEventService $alpdeskfeeEventDispatcher;
    private TwigEnvironment $twig;
    private RequestStack $requestStack;
    private ScopeMatcher $scopeMatcher;

    private int $currentPageId = 0;
    private bool $pagemountAccess = false;
    private int $pageChmodEdit = 0;
    private int $accessFilesmanagement = 0;
    private bool $alpdeskfee_livemodus = false;
    private mixed $mappingconfig = null;

    private ?BackendUserPermissions $backendUserPermissions = null;

    public function __construct(
        TokenChecker                       $tokenChecker,
        AlpdeskFrontendeditingEventService $alpdeskfeeEventDispatcher,
        TwigEnvironment                    $twig,
        RequestStack                       $requestStack,
        ScopeMatcher                       $scopeMatcher
    )
    {
        $this->tokenChecker = $tokenChecker;
        $this->alpdeskfeeEventDispatcher = $alpdeskfeeEventDispatcher;
        $this->twig = $twig;
        $this->requestStack = $requestStack;
        $this->scopeMatcher = $scopeMatcher;
    }

    private function getCurrentSession(): SessionInterface
    {
        return $this->requestStack->getCurrentRequest()->getSession();
    }

    private function getBackendUser(): void
    {
        try {

            if ($this->tokenChecker->hasBackendUser() && $this->backendUserPermissions === null) {

                $this->backendUserPermissions = new BackendUserPermissions();
                $this->backendUserPermissions->init();

                System::loadLanguageFile('default');

                $liveModus = $this->getCurrentSession()->get('alpdeskfee_livemodus');
                if ($liveModus !== null && $liveModus === true) {
                    $this->alpdeskfee_livemodus = true;
                }

                if ($this->backendUserPermissions->isAdmin()) {

                    if ($this->backendUserPermissions->isAdminDisabled() === true) {
                        $this->alpdeskfee_livemodus = true;
                    }

                }

                $this->mappingconfig = Yaml::parse(\file_get_contents(__DIR__ . '/../Resources/config/config.yml'), Yaml::PARSE_CONSTANT);

            }

        } catch (\Exception $ex) {
            $this->backendUserPermissions = null;
        }

    }

    private function checkAccess(): bool
    {
        $this->getBackendUser();

        $isFrontend = $this->scopeMatcher->isFrontendRequest($this->requestStack->getCurrentRequest());

        if ($isFrontend === true && $this->backendUserPermissions !== null && $this->pagemountAccess == true && !$this->alpdeskfee_livemodus) {
            return true;
        }

        return false;
    }

    private function addLabelsToHeader(): void
    {
        $labels = \json_encode($GLOBALS['TL_LANG']['alpdeskfee_lables']);
        $GLOBALS['TL_HEAD'][] = "<script>const alpdeskfeePageid=" . $this->currentPageId . "; const alpdeskfeeCanPageEdit=" . $this->pageChmodEdit . "; const alpdeskfeeAccessFilemanagement=" . $this->accessFilesmanagement . "; const alpdeskfeeLabels='" . $labels . "';</script>";
    }

    private function createElementsTags(string $buffer, string $classes, array $attributes): string
    {
        $dataAttributes = \array_filter($attributes, function ($v) {
            return null !== $v;
        });

        return \preg_replace_callback('|<([a-zA-Z0-9]+)(\s[^>]*?)?(?<!/)>|', function ($matches) use ($classes, $dataAttributes) {
            $tag = $matches[1];
            $attributes = $matches[2];

            $attributes = preg_replace('/class="([^"]+)"/', 'class="$1 ' . $classes . '"', $attributes, 1, $count);
            if (0 === $count) {
                $attributes .= ' class="' . $classes . '"';
            }

            foreach ($dataAttributes as $key => $value) {
                $attributes .= ' ' . $key . "='" . $value . "'";
            }

            return '<' . $tag . $attributes . '>';
        }, $buffer, 1);

    }

    public function onGetPageLayout(PageModel $objPage): void
    {
        $this->checkAccess();

        if ($this->backendUserPermissions !== null && !$this->alpdeskfee_livemodus) {

            $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/alpdeskfrontendediting/js/alpdeskfrontendediting_fe.js|async';

            if ($this->backendUserPermissions->hasAccess('page', 'modules')) {
                // !!! Typecast is necessary
                $this->currentPageId = (int)$objPage->id;
            }

            $this->pagemountAccess = $this->backendUserPermissions->hasPageMountAccess($objPage);
            $this->pageChmodEdit = ($this->backendUserPermissions->isAllowed(Utils::CAN_EDIT_PAGE, $objPage->row()) === true ? 1 : 0);

            if ($this->backendUserPermissions->hasAccess('files', 'modules')) {
                $this->accessFilesmanagement = 1;
            }

            $this->addLabelsToHeader();

        }

    }

    /**
     * @param FrontendTemplate $template
     * @param array $data
     * @param Module $module
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function onCompileArticle(FrontendTemplate $template, array $data, Module $module): void
    {
        if ($this->checkAccess()) {

            if ($this->backendUserPermissions->hasAccess('article', 'modules')) {

                $aRow = Utils::mergeArticlePermissions(null, $module->getModel()->row());

                $canEdit = false;
                if ($aRow !== null) {
                    $canEdit = $this->backendUserPermissions->isAllowed(Utils::CAN_EDIT_ARTICLES, $aRow);
                }

                $canDelete = false;
                if ($aRow !== null) {
                    $canDelete = $this->backendUserPermissions->isAllowed(Utils::CAN_DELETE_ARTICLES, $aRow);
                }

                $canPublish = $this->backendUserPermissions->hasAccess('tl_article::published', 'alexf');

                $tdata = [
                    'type' => 'article',
                    'do' => 'article',
                    'id' => $data['id'],
                    'invisible' => ((int)$data['published'] === 0),
                    'canEdit' => $canEdit,
                    'canDelete' => $canDelete,
                    'canPublish' => $canPublish,
                    'pageid' => $this->currentPageId,
                    'desc' => $GLOBALS['TL_LANG']['alpdeskfee_lables']['article']
                ];

                $articleContainerTwig = $this->twig->render('@AlpdeskFrontendediting/alpdeskfrontendediting_article.html.twig', [
                    'data' => \json_encode($tdata)
                ]);

                $elements = $template->elements;
                \array_unshift($elements, $articleContainerTwig);
                $template->elements = $elements;
            }

        }

    }

    public function onGetContentElement(ContentModel $element, string $buffer): string
    {
        if ($this->checkAccess()) {

            $modDoType = Custom::processElement($element, $this->alpdeskfeeEventDispatcher, $this->mappingconfig, $this->backendUserPermissions->getBackendUser());

            // Check if access to element
            $hasElementAccess = true;
            if (!$this->backendUserPermissions->hasAccess($element->type, 'elements') || !$this->backendUserPermissions->hasAccess($element->type, 'alpdesk_fee_elements')) {
                $hasElementAccess = false;
            }

            // Check if user has access to BackendModule
            $hasBackendModuleAccess = true;
            // e.g. Article
            $modulesCheck = str_replace('tl_', '', $element->ptable);

            // e.g. events where ptable= calendar_events and Backendmodule is calendar! Mapped in Custom::class
            if ($modDoType->getCustomBackendModule() !== '') {
                $modulesCheck = $modDoType->getCustomBackendModule();
            }

            if (!$this->backendUserPermissions->hasAccess($modulesCheck, 'modules')) {
                $hasBackendModuleAccess = false;
            }

            // Check when Artikel if the element can be edited
            // Maybe the element can be inserted by inserttags in other Module without Article
            // @TODO im not sure if it´s possible that a user can edit article::text but not tl_news::text!?
            $canEdit = true;
            if ($element->ptable == 'tl_article') {

                $aRow = Utils::mergeArticlePermissions((int)$element->pid, null);
                if ($aRow !== null) {
                    $canEdit = $this->backendUserPermissions->isAllowed(Utils::CAN_EDIT_ARTICLES, $aRow);
                }

            }

            $canPublish = $this->backendUserPermissions->hasAccess('tl_content::invisible', 'alexf');

            $label = $GLOBALS['TL_LANG']['alpdeskfee_lables']['ce'];
            if ($modDoType->getValid() === true) {
                $label = $modDoType->getLabel();
            } else {

                $labelList = $GLOBALS['TL_LANG']['CTE'];
                if (\array_key_exists($element->type, $labelList)) {

                    if (\is_array($labelList[$element->type]) && \count($labelList[$element->type]) >= 1) {
                        $label = $labelList[$element->type][0];
                    } else if ($labelList[$element->type] !== null && $labelList[$element->type] !== '') {
                        $label = $labelList[$element->type];
                    }

                }

            }

            // e.g. Article
            $do = str_replace('tl_', '', $element->ptable);
            // In same cases e.g. Calendar the do type is different from the ptable. So map this manually
            if ($modDoType->getCustomBackendModule() !== '') {
                $do = $modDoType->getCustomBackendModule();
            }

            $access = true;
            if (!$hasElementAccess || !$hasBackendModuleAccess || !$modDoType->getHasParentAccess()) {
                $access = false;
            }

            $parentaccess = true;
            if (!$hasBackendModuleAccess || !$modDoType->getHasParentAccess()) {
                $parentaccess = false;
            }

            $data = [
                'type' => 'ce',
                'do' => $do,
                'access' => $access,
                'parentaccess' => $parentaccess,
                'id' => $element->id,
                'pid' => $element->pid,
                'invisible' => ((int)$element->invisible === 1),
                'canEdit' => $canEdit,
                'canPublish' => $canPublish,
                'pageid' => $this->currentPageId,
                'act' => ($modDoType->getValid() == true ? $modDoType->getPath() : ''),
                'icon' => ($modDoType->getValid() == true ? $modDoType->getIcon() : ''),
                'iconclass' => ($modDoType->getValid() == true ? $modDoType->getIconclass() : ''),
                'desc' => $label
            ];
            $buffer = $this->createElementsTags($buffer, 'alpdeskfee-ce', [
                'data-alpdeskfee' => \json_encode($data)
            ]);
        }

        return $buffer;

    }

    private function renderModuleOutput(CustomViewItem $modDoType, string $buffer): string
    {
        if ($modDoType->getValid() === true && ($modDoType->getType() == CustomViewItem::$TYPE_MODULE || $modDoType->getType() == CustomViewItem::$TYPE_FORM)) {

            $data = [
                'type' => 'mod',
                'do' => $modDoType->getPath(),
                'act' => $modDoType->getSublevelpath(),
                'icon' => $modDoType->getIcon(),
                'iconclass' => $modDoType->getIconclass(),
                'pageid' => $this->currentPageId,
                'subviewitems' => $modDoType->getDecodesSubviewItems(),
                'desc' => $modDoType->getLabel()
            ];

            $buffer = $this->createElementsTags($buffer, 'alpdeskfee-ce', [
                'data-alpdeskfee' => \json_encode($data)
            ]);

        }

        return $buffer;

    }

    // @ToDo $module must not be of Type Module!!! Currently when e.g. Form there is a FORM-Object as 3 parameter
    // In future check also other Typs!
    public function onGetFrontendModule(ModuleModel $model, string $buffer, mixed $module): string
    {
        if ($this->checkAccess()) {

            if ($module instanceof Module) {

                $modDoType = Custom::processModule($module, $this->alpdeskfeeEventDispatcher, $this->mappingconfig, $this->backendUserPermissions->getBackendUser());
                return $this->renderModuleOutput($modDoType, $buffer);

            } else if ($module instanceof Form) {

                $modDoType = Custom::processForm($module, $this->alpdeskfeeEventDispatcher, $this->mappingconfig, $this->backendUserPermissions->getBackendUser());
                return $this->renderModuleOutput($modDoType, $buffer);

            }
        }

        return $buffer;

    }

    public function onParseArticles(FrontendTemplate $template, array $newsEntry, Module $module): void
    {
        if ($this->checkAccess()) {
            (new MappingArticle($template, $newsEntry, $module, (string)$this->currentPageId, $this->backendUserPermissions->getBackendUser()))->prepare();
        }

    }

    /**
     * @param string $pattern
     * @param string $strString
     * @return bool
     */
    public static function isPatternInString(string $pattern, string $strString): bool
    {
        if (\preg_match("/{$pattern}/i", $strString)) {
            return true;
        }

        return false;

    }

    public function onParseFrontendTemplate(string $buffer, string $template): string
    {
        if ($this->checkAccess()) {

            $mappedNewsListTemplates = MappingArticle::getMappedNewsListTemplates();
            if (\count($mappedNewsListTemplates) > 0) {

                if (\array_key_exists($template, $mappedNewsListTemplates)) {

                    $mappedItems = $mappedNewsListTemplates[$template];

                    foreach ($mappedItems as $mappedItem) {

                        if (self::isPatternInString($mappedItem['mappingString'], $buffer) === true) {

                            $buffer = $this->createElementsTags($buffer, 'alpdeskfee-ce', [
                                'data-alpdeskfee' => \json_encode($mappedItem['data'])
                            ]);

                            // Because to modify header also
                            // @TODO if no space is set in some layout maybe the parent NewsModule is not accessible
                            // $buffer = '<div style="margin-top:10px">' . $buffer . '</div>';

                        }

                    }


                }

            }

        }

        return $buffer;

    }

    /**
     * @param array $attributes
     * @param null $context
     * @return array
     */
    public function onGetAttributesFromDca(array $attributes, $context = null): array
    {
        $this->getBackendUser();

        if (
            $this->scopeMatcher->isBackendRequest($this->requestStack->getCurrentRequest()) === true &&
            $this->backendUserPermissions !== null &&
            !$this->alpdeskfee_livemodus &&
            $context instanceof DC_Table
        ) {

            $objSession = $this->getCurrentSession();

            $alpdeskfeeElementType = $objSession->get('alpdeskfee_tl_content_element_type');
            if ($alpdeskfeeElementType !== null && $alpdeskfeeElementType !== '' && $attributes["name"] === 'type' && $attributes['strTable'] === "tl_content") {

                $objSession->set('alpdeskfee_tl_content_element_type', null);

                if ($this->backendUserPermissions->hasAccess((string)$alpdeskfeeElementType, 'elements') && $context->id !== null && $context->id !== 0) {

                    $contentModel = ContentModel::findById($context->id);
                    if ($contentModel !== null) {

                        $contentModel->type = (string)$alpdeskfeeElementType;
                        $contentModel->save();

                        $attributes['value'] = (string)$alpdeskfeeElementType;

                    }

                }

            }

        }

        return $attributes;
    }

}
