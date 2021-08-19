<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes;

use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;

class TypeRockSolidSlider extends Base
{
    public function run(CustomViewItem $item): CustomViewItem
    {
        if ($this->element !== null) {
            $this->parse($item, $this->element->rsts_id);
        } else if ($this->module !== null) {
            $this->parse($item, $this->module->rsts_id);
        }

        return $item;
    }

    private function parse(CustomViewItem $item, $rsts_id): void
    {
        $do = 'do=' . $this->backendmodule . '&act=edit&id=' . $rsts_id;

        if (class_exists('\MadeYourDay\RockSolidSlider\Model\SliderModel')) {

            $sliderModel = \MadeYourDay\RockSolidSlider\Model\SliderModel::findById($rsts_id);
            if ($sliderModel !== null) {
                if ($sliderModel->type == 'content') {
                    $do = 'do=' . $this->backendmodule . '&table=' . $this->table . '&id=' . $rsts_id;
                }
            }

        }

        $item->setValid(true);
        $item->setPath($do);

    }

}
