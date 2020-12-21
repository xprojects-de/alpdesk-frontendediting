<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes;

use Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes\Base;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;
use Contao\BackendUser;
use Contao\StringUtil;

class TypeEventlist extends Base {

  private static $icon = '../../../system/themes/flexible/icons/group.svg';
  private static $iconclass = 'tl_event_baritem';

  public function run(CustomViewItem $item): CustomViewItem {

    if (class_exists('\Contao\CalendarModel')) {
      $calendars = StringUtil::deserialize($this->module->cal_calendar);
      if (count($calendars) == 1) {
        $objCalendar = \Contao\CalendarModel::findById($calendars[0]);
        if ($objCalendar !== null) {
          if (BackendUser::getInstance()->hasAccess($objCalendar->id, 'calendars')) {
            $item->setValid(true);
            $item->setIcon(self::$icon);
            $item->setIconclass(self::$iconclass);
            $item->setPath('do=calendar&table=tl_calendar_events&id=' . $objCalendar->id);
            $item->setLabel($GLOBALS['TL_LANG']['alpdeskfee_mapping_lables']['events']);
          }
        }
      } else {
        $item->setValid(true);
        $item->setIcon(self::$icon);
        $item->setIconclass(self::$iconclass);
        $item->setPath('do=calendar');
        $item->setLabel($GLOBALS['TL_LANG']['alpdeskfee_mapping_lables']['events']);
      }
    }

    return $item;
  }

}
