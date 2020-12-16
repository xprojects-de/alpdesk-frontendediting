<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Custom;

class CustomResponse {

  private $valid = false;
  private $path = '';
  private $label = '';

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

}
