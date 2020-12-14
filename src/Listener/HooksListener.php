<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Listener;

use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use Contao\ContentModel;
use Contao\ModuleModel;
use Contao\Module;
use Contao\BackendUser;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Alpdesk\AlpdeskFrontendediting\Utils\Utils;

class HooksListener {

  private $tokenChecker = null;
  private $backendUser = null;

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
    }
  }

  private function checkAccess(): bool {
    if (TL_MODE == 'FE' && $this->backendUser !== null) {
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

  public function onGetContentElement(ContentModel $element, string $buffer): string {

    if ($this->checkAccess()) {

      if (!$this->backendUser->isAdmin) {
        if (\count($this->backendUser->elements) <= 0 || !\in_array($element->type, $this->backendUser->elements)) {
          return $buffer;
        }
      }

      $modDoType = Utils::getModDoTypeCe($element->type);

      $buffer = $this->createElementsTags($buffer, 'alpdeskfee-ce', [
          'data-alpdeskfee-type' => 'ce',
          'data-alpdeskfee-subtype' => ($modDoType !== '' ? $modDoType : ''),
          'data-alpdeskfee-desc' => $GLOBALS['TL_LANG']['alpdeskfee']['ce'],
          'data-alpdeskfee-do' => str_replace('tl_', '', $element->ptable),
          'data-alpdeskfee-id' => $element->id,
          'data-alpdeskfee-pid' => $element->pid
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
            'data-alpdeskfee-desc' => $GLOBALS['TL_LANG']['alpdeskfee']['mod'],
            'data-alpdeskfee-do' => $modDoType
        ]);
      }
    }

    return $buffer;
  }

}
