<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Events;

use Symfony\Contracts\EventDispatcher\Event;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;
use Contao\Form;

class AlpdeskFrontendeditingEventForm extends Event
{
    public const NAME = 'alpdeskfrontendediting.form';

    private CustomViewItem $item;
    private Form $form;

    public function __construct(CustomViewItem $item, Form $form)
    {
        $this->item = $item;
        $this->form = $form;
    }

    public function getItem(): CustomViewItem
    {
        return $this->item;
    }

    public function setItem(CustomViewItem $item): void
    {
        $this->item = $item;
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function setForm(Form $form): void
    {
        $this->form = $form;
    }

}
