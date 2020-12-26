<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes;

use Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes\Base;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;
use Contao\BackendUser;
use Contao\StringUtil;

class TypeEventlist extends Base {

  public function run(CustomViewItem $item): CustomViewItem {

    if (class_exists('\Contao\CalendarModel')) {
      $calendars = StringUtil::deserialize($this->module->cal_calendar);
      if (count($calendars) == 1) {
        $objCalendar = \Contao\CalendarModel::findById($calendars[0]);
        if ($objCalendar !== null) {
          if (BackendUser::getInstance()->hasAccess($objCalendar->id, 'calendars')) {
            $item->setValid(true);
            $item->setPath('do=' . $this->backendmodule . '&table=' . $this->table . '&id=' . $objCalendar->id);
          }
        }
      } else {
        $item->setValid(true);
        $item->setPath('do=' . $this->backendmodule);
      }
    }

    return $item;
  }

}
