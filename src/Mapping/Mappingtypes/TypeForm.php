<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes;

use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;
use Contao\BackendUser;

class TypeForm extends Base
{
    public function run(CustomViewItem $item): CustomViewItem
    {
        if ($this->element !== null) {

            if (BackendUser::getInstance()->hasAccess($this->element->form, 'forms')) {
                $item->setValid(true);
                $item->setPath('do=' . $this->backendmodule . '&table=' . $this->table . '&id=' . $this->element->form);
            }

        } else if ($this->form !== null) {

            if (BackendUser::getInstance()->hasAccess($this->form->id, 'forms')) {
                $item->setValid(true);
                $item->setPath('do=' . $this->backendmodule . '&table=' . $this->table . '&id=' . $this->form->id);
            }

        }

        return $item;
    }

}
