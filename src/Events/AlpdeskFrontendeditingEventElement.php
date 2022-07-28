<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Events;

use Symfony\Contracts\EventDispatcher\Event;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;
use Contao\ContentModel;

class AlpdeskFrontendeditingEventElement extends Event
{
    public const NAME = 'alpdeskfrontendediting.element';

    private CustomViewItem $item;
    private ContentModel $element;

    public function __construct(CustomViewItem $item, ContentModel $element)
    {
        $this->item = $item;
        $this->element = $element;
    }

    public function getItem(): CustomViewItem
    {
        return $this->item;
    }

    public function getElement(): ContentModel
    {
        return $this->element;
    }

    public function setItem(CustomViewItem $item): void
    {
        $this->item = $item;
    }

    public function setElement(ContentModel $element): void
    {
        $this->element = $element;
    }

}
