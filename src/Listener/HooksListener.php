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

class HooksListener {

  private $tokenChecker = null;
  private $alpdeskfeeEventDispatcher = null;
  private $backendUser = null;
  private $currentPageId = null;
  private $pagemountAccess = false;
  private $pageChmodEdit = false;

  public function __construct(TokenChecker $tokenChecker, AlpdeskFrontendeditingEventService $alpdeskfeeEventDispatcher) {
    $this->tokenChecker = $tokenChecker;
    $this->alpdeskfeeEventDispatcher = $alpdeskfeeEventDispatcher;
    $this->getBackendUser();
  }

  private function getBackendUser() {
    if ($this->tokenChecker->hasBackendUser()) {
      Utils::mergeUserGroupPersmissions();
      $this->backendUser = BackendUser::getInstance();
      System::loadLanguageFile('default');
    }
  }

  public function onGetPageLayout(PageModel $objPage, LayoutModel $objLayout, PageRegular $objPageRegular): void {

    if ($this->backendUser !== null) {
      $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/alpdeskfrontendediting/js/alpdeskfrontendediting_fe.js|async';
      $GLOBALS['TL_CSS'][] = 'bundles/alpdeskfrontendediting/css/alpdeskfrontendediting_fe.css';

      if ($this->backendUser->hasAccess('page', 'modules')) {
        $this->currentPageId = $objPage->id;
      }
      $this->pagemountAccess = Utils::hasPagemountAccess($objPage);
      $this->pageChmodEdit = $this->backendUser->isAllowed(BackendUser::CAN_EDIT_PAGE, $objPage->row());
    }
  }

  private function checkAccess(): bool {
    if (TL_MODE == 'FE' && $this->backendUser !== null && $this->pagemountAccess == true) {
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
        $canPublish = $this->backendUser->hasAccess('tl_article::published', 'alexf');

        $tdata = [
            'type' => 'article',
            'desc' => $GLOBALS['TL_LANG']['alpdeskfee_lables']['article'],
            'do' => 'article',
            'id' => $data['id'],
            'invisible' => ($data['published'] == 0 ? true : false),
            'articleChmodEdit' => $canEdit,
            'canPublish' => $canPublish,
            'chmodpageedit' => $this->pageChmodEdit,
            'pageid' => $this->currentPageId
        ];

        $templateArticle = new FrontendTemplate('alpdeskfrontendediting_article');
        $templateArticle->data = \json_encode($tdata);
        $elements = $template->elements;
        array_unshift($elements, $templateArticle->parse());
        $template->elements = $elements;
      }
    }
  }

  public function onGetContentElement(ContentModel $element, string $buffer): string {

    if ($this->checkAccess()) {

      $modDoType = Custom::processElement($element, $this->alpdeskfeeEventDispatcher);

      // We have a module as content element
      if ($modDoType->getType() == CustomViewItem::$TYPE_MODULE) {
        return $this->renderModuleOutput($modDoType, $buffer);
      }

      // Check if access to element
      $hasElementAccess = true;
      if (!$this->backendUser->hasAccess($element->type, 'elements') || !$this->backendUser->hasAccess($element->type, 'alpdesk_fee_elements')) {
        $hasElementAccess = false;
      }

      // Check if access to parent element
      $hasParentAccess = true;
      if (!$this->backendUser->hasAccess(str_replace('tl_', '', $element->ptable), 'modules')) {
        $hasParentAccess = false;
      }

      // We have a normale ContentElement
      // If it is not mapped in Backend we have to check the rights
      // If it´s mapped we show to enable Backendmodule edit

      if ($modDoType->getValid() == false) {
        if (!$hasElementAccess || !$hasParentAccess) {
          return $buffer;
        }
      }

      // Check when Artikel if the element can be edited
      // Maybe the element can be inserted by inserttags in other Module without Article
      // @TODO Check whene Module has inserttag content then two bars will be shown because getContent and Module is triggered
      $canEdit = true;
      if ($element->ptable == 'tl_article') {
        $parentArticleModel = ArticleModel::findBy(['id = ?'], $element->pid);
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

      // Maybe the User should not edit ContentElements but edit mapped Module
      // So only mapped path show be shown
      $do = str_replace('tl_', '', $element->ptable);
      if (!$hasElementAccess || !$hasParentAccess) {
        $do = '';
      }

      $data = [
          'type' => 'ce',
          'desc' => $label,
          'do' => $do,
          'id' => $element->id,
          'pid' => $element->pid,
          'invisible' => ($element->invisible == 1 ? true : false),
          'articleChmodEdit' => $canEdit,
          'canPublish' => $canPublish,
          'chmodpageedit' => $this->pageChmodEdit,
          'pageid' => $this->currentPageId,
          'act' => ($modDoType->getValid() == true ? $modDoType->getPath() : ''),
      ];
      $buffer = $this->createElementsTags($buffer, 'alpdeskfee-ce', [
          'data-alpdeskfee' => \json_encode($data)
      ]);
    }

    return $buffer;
  }

  private function renderModuleOutput(CustomViewItem $modDoType, string $buffer) {

    if ($modDoType->getValid() === true && $modDoType->getType() == CustomViewItem::$TYPE_MODULE) {
      $data = [
          'type' => 'mod',
          'desc' => $modDoType->getLabel(),
          'do' => $modDoType->getPath(),
          'act' => $modDoType->getSublevelpath(),
          'chmodpageedit' => $this->pageChmodEdit,
          'pageid' => $this->currentPageId,
          'subviewitems' => $modDoType->getDecodesSubviewItems(),
      ];
      $buffer = $this->createElementsTags($buffer, 'alpdeskfee-ce', [
          'data-alpdeskfee' => \json_encode($data)
      ]);
    }

    return $buffer;
  }

  public function onGetFrontendModule(ModuleModel $model, string $buffer, Module $module): string {

    if ($this->checkAccess()) {

      $modDoType = Custom::processModule($module, $this->alpdeskfeeEventDispatcher);
      return $this->renderModuleOutput($modDoType, $buffer);
    }

    return $buffer;
  }

}
