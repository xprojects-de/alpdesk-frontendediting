<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Custom;

class CustomViewItem {

  public static $TYPE_MODULE = 1;
  public static $TYPE_CE = 2;
  private $type = 0;
  private $valid = false;
  private $path = '';
  private $sublevelpath = '';
  private $label = '';

  function getType(): int {
    return $this->type;
  }

  function setType(int $type): void {
    $this->type = $type;
  }

  function getValid(): bool {
    return $this->valid;
  }

  function getPath(): string {
    return $this->path;
  }

  function getLabel(): string {
    return $this->label;
  }

  function setValid(bool $valid): void {
    $this->valid = $valid;
  }

  function setPath(string $path): void {
    $this->path = $path;
  }

  function setLabel(string $label): void {
    $this->label = $label;
  }

  function getSublevelpath(): string {
    return $this->sublevelpath;
  }

  function setSublevelpath(string $sublevelpath): void {
    $this->sublevelpath = $sublevelpath;
  }

}
