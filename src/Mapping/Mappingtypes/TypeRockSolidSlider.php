<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes;

use Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes\Base;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;

class TypeRockSolidSlider extends Base {

  private static $DO = 'do=rocksolid_slider';

  public function run(CustomViewItem $item): CustomViewItem {

    $do = self::$DO . '&act=edit&id=' . $this->element->rsts_id;

    if (class_exists('\MadeYourDay\RockSolidSlider\Model\SliderModel')) {
      $sliderModel = \MadeYourDay\RockSolidSlider\Model\SliderModel::findById($this->element->rsts_id);
      if ($sliderModel !== null) {
        if ($sliderModel->type == 'content') {
          $do = self::$DO . '&table=tl_rocksolid_slide&id=' . $this->element->rsts_id;
        }
      }
    }

    $item->setValid(true);
    $item->setPath($do);
    $item->setLabel($GLOBALS['TL_LANG']['alpdeskfee_mapping_lables']['rocksolidslider']);

    return $item;
  }

}
