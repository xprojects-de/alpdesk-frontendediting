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
use Contao\FrontendTemplate;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Alpdesk\AlpdeskFrontendediting\Utils\Utils;

class HooksListener {

  private $tokenChecker = null;
  private $backendUser = null;
  private $currentPageId = null;
  private $pagemountAccess = false;
  private $pageChmodEdit = false;

  public function __construct(TokenChecker $tokenChecker) {
    $this->tokenChecker = $tokenChecker;
    $this->getBackendUser();
  }

  private function getBackendUser() {
    if ($this->tokenChecker->hasBackendUser()) {
      $this->backendUser = BackendUser::getInstance();
    }
  }

  public function onGetPageLayout(PageModel $objPage, LayoutModel $objLayout, PageRegular $objPageRegular): void {

    if ($this->backendUser !== null) {
      $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/alpdeskfrontendediting/js/alpdeskfrontendediting_fe.js|async';
      $GLOBALS['TL_CSS'][] = 'bundles/alpdeskfrontendediting/css/alpdeskfrontendediting_fe.css';

      $this->currentPageId = $objPage->id;
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
        $attributes .= ' ' . $key . '="' . $value . '"';
      }

      return "<{$tag}{$attributes}>";
    }, $buffer, 1);

    return $buffer;
  }

  public function onCompileArticle(FrontendTemplate $template, array $data, Module $module): void {

    if ($this->checkAccess()) {

      $canEdit = $this->backendUser->isAllowed(BackendUser::CAN_EDIT_ARTICLES, $module->getModel()->row());

      $templateArticle = new FrontendTemplate('alpdeskfrontendediting_article');
      $templateArticle->type = 'article';
      $templateArticle->desc = $GLOBALS['TL_LANG']['alpdeskfee_lables']['article'];
      $templateArticle->do = 'article';
      $templateArticle->aid = $data['id'];
      $templateArticle->articleChmodEdit = $canEdit;
      $templateArticle->pageChmodEdit = $this->pageChmodEdit;
      $templateArticle->pageid = $this->currentPageId;
      $elements = $template->elements;
      array_unshift($elements, $templateArticle->parse());
      $template->elements = $elements;
    }
  }

  public function onGetContentElement(ContentModel $element, string $buffer): string {

    if ($this->checkAccess()) {

      if (!$this->backendUser->hasAccess($element->type, 'elements')) {
        return $buffer;
      }

      $canEdit = true;
      if ($element->ptable == 'tl_article') {
        $parentArticleModel = ArticleModel::findBy(['id=?'], $element->pid);
        if ($parentArticleModel !== null) {
          $canEdit = $this->backendUser->isAllowed(BackendUser::CAN_EDIT_ARTICLES, $parentArticleModel->row());
        }
      }

      $modDoType = Utils::getModDoTypeCe($element->type);

      $buffer = $this->createElementsTags($buffer, 'alpdeskfee-ce', [
          'data-alpdeskfee-type' => 'ce',
          'data-alpdeskfee-desc' => $GLOBALS['TL_LANG']['alpdeskfee_lables']['ce'],
          'data-alpdeskfee-subtype' => ($modDoType !== '' ? $modDoType : ''),
          'data-alpdeskfee-do' => str_replace('tl_', '', $element->ptable),
          'data-alpdeskfee-id' => $element->id,
          'data-alpdeskfee-pid' => $element->pid,
          'data-alpdeskfee-articleChmodEdit' => $canEdit,
          'data-alpdeskfee-chmodpageedit' => $this->pageChmodEdit,
          'data-alpdeskfee-pageid' => $this->currentPageId
      ]);
    }

    return $buffer;
  }

  public function onGetFrontendModule(ModuleModel $model, string $buffer, Module $module): string {

    if ($this->checkAccess()) {

      $modDoType = Utils::getModDoType($module);
      if ($modDoType !== null) {
        $buffer = $this->createElementsTags($buffer, 'alpdeskfee-ce', [
            'data-alpdeskfee-type' => 'mod',
            'data-alpdeskfee-desc' => $GLOBALS['TL_LANG']['alpdeskfee_lables']['mod'],
            'data-alpdeskfee-do' => $modDoType,
            'data-alpdeskfee-chmodpageedit' => $this->pageChmodEdit,
            'data-alpdeskfee-pageid' => $this->currentPageId
        ]);
      }
    }

    return $buffer;
  }

}
