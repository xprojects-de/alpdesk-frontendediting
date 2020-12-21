<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Custom;

class CustomSubviewItem {

  private $path = '';
  private $icon = '';
  private $iconclass = '';

  public function getPath(): string {
    return $this->path;
  }

  public function getIcon(): string {
    return $this->icon;
  }

  public function getIconclass(): string {
    return $this->iconclass;
  }

  public function setPath(string $path): void {
    $this->path = $path;
  }

  public function setIcon(string $icon): void {
    $this->icon = $icon;
  }

  public function setIconclass(string $iconclass): void {
    $this->iconclass = $iconclass;
  }

}
