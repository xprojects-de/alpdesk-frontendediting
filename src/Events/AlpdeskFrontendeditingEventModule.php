<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Events;

use Symfony\Contracts\EventDispatcher\Event;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;
use Contao\Module;

class AlpdeskFrontendeditingEventModule extends Event {

  public const NAME = 'alpdeskfrontendediting.module';

  private $item;
  private $module;

  public function __construct(CustomViewItem $item, Module $module) {
    $this->item = $item;
    $this->module = $module;
  }

  public function getItem(): CustomViewItem {
    return $this->item;
  }

  public function setItem(CustomViewItem $item): void {
    $this->item = $item;
  }

  public function getModule(): Module {
    return $this->module;
  }

  public function setModule(Module $module): void {
    $this->module = $module;
  }

}
