<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes;

use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;

class TypeSimple extends Base
{
    public function run(CustomViewItem $item): CustomViewItem
    {
        $item->setValid(true);

        $ap = '';
        if (\is_array($this->additional_static_params) && \count($this->additional_static_params) > 0) {
            $ap = '&' . (\implode('&', $this->additional_static_params));
        }

        $item->setPath('do=' . $this->backendmodule . $ap);

        return $item;
    }

}
