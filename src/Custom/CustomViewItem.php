<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Custom;

class CustomViewItem
{
    public static $TYPE_MODULE = 1;
    public static $TYPE_CE = 2;
    public static $TYPE_FORM = 3;
    private $type = 0;
    private $valid = false;
    private $path = '';
    private $sublevelpath = '';
    private $label = '';
    private $subviewitems = [];
    // Access to BackendModule
    private $hasParentAccess = true;
    private $icon = '';
    private $iconclass = '';
    private $customBackendModule = '';

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }

    public function getValid(): bool
    {
        return $this->valid;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setValid(bool $valid): void
    {
        $this->valid = $valid;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getSublevelpath(): string
    {
        return $this->sublevelpath;
    }

    public function setSublevelpath(string $sublevelpath): void
    {
        $this->sublevelpath = $sublevelpath;
    }

    public function getSubviewitems(): CustomSubviewItem
    {
        return $this->subviewitems;
    }

    public function addSubviewitems(CustomSubviewItem $subviewitem): void
    {
        \array_push($this->subviewitems, $subviewitem);
    }

    public function getHasParentAccess(): bool
    {
        return $this->hasParentAccess;
    }

    public function setHasParentAccess(bool $hasParentAccess): void
    {
        $this->hasParentAccess = $hasParentAccess;
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    public function setIconclass(string $iconclass): void
    {
        $this->iconclass = $iconclass;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getIconclass(): string
    {
        return $this->iconclass;
    }

    public function getCustomBackendModule(): string
    {
        return $this->customBackendModule;
    }

    public function setCustomBackendModule(string $customBackendModule): void
    {
        $this->customBackendModule = $customBackendModule;
    }

    public function getDecodesSubviewItems(): array
    {
        $data = [];

        if (\count($this->subviewitems) > 0) {
            foreach ($this->subviewitems as $subItem) {
                \array_push($data, [
                    'path' => $subItem->getPath(),
                    'icon' => $subItem->getIcon(),
                    'iconclass' => $subItem->getIconclass()
                ]);
            }
        }

        return $data;
    }

}
