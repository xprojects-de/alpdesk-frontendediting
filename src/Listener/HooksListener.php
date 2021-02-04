<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Listener;

use Contao\LayoutModel;
use Contao\PageModel;
use Contao\ArticleModel;
use Contao\PageRegular;
use Contao\ContentModel;
use Contao\ModuleModel;
use Contao\Module;
use Contao\BackendUser;
use Contao\System;
use Contao\FrontendTemplate;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Alpdesk\AlpdeskFrontendediting\Utils\Utils;
use Alpdesk\AlpdeskFrontendediting\Custom\Custom;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;
use Alpdesk\AlpdeskFrontendediting\Events\AlpdeskFrontendeditingEventService;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment as TwigEnvironment;

class HooksListener {

  private $tokenChecker = null;
  private $alpdeskfeeEventDispatcher = null;
  private $twig = null;
  private $backendUser = null;
  private $currentPageId = 0;
  private $pagemountAccess = false;
  private $pageChmodEdit = 0;
  private $alpdeskfee_livemodus = false;
  private $mappingconfig = null;

  public function __construct(TokenChecker $tokenChecker, AlpdeskFrontendeditingEventService $alpdeskfeeEventDispatcher, TwigEnvironment $twig) {
    $this->tokenChecker = $tokenChecker;
    $this->alpdeskfeeEventDispatcher = $alpdeskfeeEventDispatcher;
    $this->twig = $twig;
    $this->getBackendUser();
  }

  private function getBackendUser() {
    if ($this->tokenChecker->hasBackendUser()) {
      Utils::mergeUserGroupPersmissions();
      $this->backendUser = BackendUser::getInstance();
      System::loadLanguageFile('default');
      $liveModus = System::getContainer()->get('session')->get('alpdeskfee_livemodus');
      if ($liveModus !== null && $liveModus === true) {
        $this->alpdeskfee_livemodus = true;
      }
      $this->mappingconfig = Yaml::parse(\file_get_contents(__DIR__ . '/../Resources/config/config.yml'), Yaml::PARSE_CONSTANT);
    }
  }

  private function addLabelsToHeader() {
    $labels = \json_encode($GLOBALS['TL_LANG']['alpdeskfee_lables']);
    $GLOBALS['TL_HEAD'][] = "<script>const alpdeskfeePageid=" . $this->currentPageId . "; const alpdeskfeeCanPageEdit=" . $this->pageChmodEdit . "; const alpdeskfeeLabels='" . $labels . "';</script>";
  }

  public function onGetPageLayout(PageModel $objPage, LayoutModel $objLayout, PageRegular $objPageRegular): void {

    if ($this->backendUser !== null && !$this->alpdeskfee_livemodus) {

      $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/alpdeskfrontendediting/js/alpdeskfrontendediting_fe.js|async';

      if ($this->backendUser->hasAccess('page', 'modules')) {
        $this->currentPageId = $objPage->id;
      }
      $this->pagemountAccess = Utils::hasPagemountAccess($objPage);
      $this->pageChmodEdit = ($this->backendUser->isAllowed(BackendUser::CAN_EDIT_PAGE, $objPage->row()) == true ? 1 : 0);
      $this->addLabelsToHeader();
    }
  }

  private function checkAccess(): bool {
    if (TL_MODE == 'FE' && $this->backendUser !== null && $this->pagemountAccess == true && !$this->alpdeskfee_livemodus) {
      return true;
    }
    return false;
  }

  private function createElementsTags(string $buffer, string $classes, array $attributes) {
    $dataAttributes = \array_filter($attributes, function ($v) {
      return null !== $v;
    });
    $buffer = \preg_replace_callback('|<([a-zA-Z0-9]+)(\s[^>]*?)?(?<!/)>|', function ($matches) use ($classes, $dataAttributes) {
      $tag = $matches[1];
      $attributes = $matches[2];

      $attributes = preg_replace('/class="([^"]+)"/', 'class="$1 ' . $classes . '"', $attributes, 1, $count);
      if (0 === $count) {
        $attributes .= ' class="' . $classes . '"';
      }

      foreach ($dataAttributes as $key => $value) {
        $attributes .= ' ' . $key . "='" . $value . "'";
      }

      return "<{$tag}{$attributes}>";
    }, $buffer, 1);

    return $buffer;
  }

  public function onCompileArticle(FrontendTemplate $template, array $data, Module $module): void {

    if ($this->checkAccess()) {

      if ($this->backendUser->hasAccess('article', 'modules')) {

        $canEdit = $this->backendUser->isAllowed(BackendUser::CAN_EDIT_ARTICLES, $module->getModel()->row());
        $canDelete = $this->backendUser->isAllowed(BackendUser::CAN_DELETE_ARTICLES, $module->getModel()->row());
        $canPublish = $this->backendUser->hasAccess('tl_article::published', 'alexf');

        $tdata = [
            'type' => 'article',
            'do' => 'article',
            'id' => $data['id'],
            'invisible' => ($data['published'] == 0 ? true : false),
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
        array_unshift($elements, $articleContainerTwig);
        $template->elements = $elements;
      }
    }
  }

  public function onGetContentElement(ContentModel $element, string $buffer, $el): string {

    if ($this->checkAccess()) {

      $modDoType = Custom::processElement($element, $this->alpdeskfeeEventDispatcher, $this->mappingconfig);

      // We have a module as content element
      if ($modDoType->getType() == CustomViewItem::$TYPE_MODULE || $modDoType->getType() == CustomViewItem::$TYPE_FORM) {
        return $this->renderModuleOutput($modDoType, $buffer);
      }

      // Check if access to element
      $hasElementAccess = true;
      if (!$this->backendUser->hasAccess($element->type, 'elements') || !$this->backendUser->hasAccess($element->type, 'alpdesk_fee_elements')) {
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
      if (!$this->backendUser->hasAccess($modulesCheck, 'modules')) {
        $hasBackendModuleAccess = false;
      }

      // Check access if Module wasn´´ specially mappend in Custom::class and is invalid there
      // getHasParentAccess() means e.g. if user has no access to special News-Feed where the contentelement is stored
      if ($modDoType->getValid() == false) {
        if (!$hasElementAccess || !$hasBackendModuleAccess || !$modDoType->getHasParentAccess()) {
          return $buffer;
        }
      }

      // Check when Artikel if the element can be edited
      // Maybe the element can be inserted by inserttags in other Module without Article
      // @TODO im not sure if it´s possible that a user can edit article::text but not tl_news::text!?
      $canEdit = true;
      if ($element->ptable == 'tl_article') {
        $parentArticleModel = ArticleModel::findById($element->pid);
        if ($parentArticleModel !== null) {
          $canEdit = $this->backendUser->isAllowed(BackendUser::CAN_EDIT_ARTICLES, $parentArticleModel->row());
        }
      }
      $canPublish = $this->backendUser->hasAccess('tl_content::invisible', 'alexf');

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

      $data = [
          'type' => 'ce',
          'do' => $do,
          'access' => $access,
          'id' => $element->id,
          'pid' => $element->pid,
          'invisible' => ($element->invisible == 1 ? true : false),
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

  private function renderModuleOutput(CustomViewItem $modDoType, string $buffer) {

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
  public function onGetFrontendModule(ModuleModel $model, string $buffer, $module): string {

    if ($this->checkAccess()) {

      if ($module instanceof Module) {
        $modDoType = Custom::processModule($module, $this->alpdeskfeeEventDispatcher, $this->mappingconfig);
        return $this->renderModuleOutput($modDoType, $buffer);
      }
    }

    return $buffer;
  }

}
