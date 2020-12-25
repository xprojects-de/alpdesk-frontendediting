<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes;

use Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes\Base;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;
use Contao\BackendUser;
use Contao\Input;
use Contao\StringUtil;

class TypeEventreader extends Base {

  private static $DO = 'do=calendar&table=tl_content';

  public function run(CustomViewItem $item): CustomViewItem {

    if (class_exists('\Contao\CalendarEventsModel')) {
      $calendars = StringUtil::deserialize($this->module->cal_calendar);
      $objEvent = \Contao\CalendarEventsModel::findPublishedByParentAndIdOrAlias(Input::get('events'), $calendars);
      if ($objEvent !== null) {
        $objCalendar = $objEvent->getRelated('pid');
        if (BackendUser::getInstance()->hasAccess($objCalendar->id, 'calendars')) {
          $item->setValid(true);
          $item->setIcon($this->icon);
          $item->setIconclass($this->iconclass);
          $item->setPath(self::$DO . '&id=' . $objEvent->id);
          $item->setLabel($GLOBALS['TL_LANG']['alpdeskfee_mapping_lables']['events']);
        }
      }
    }

    return $item;
  }

}
