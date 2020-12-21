<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes;

use Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes\Base;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;
use Contao\BackendUser;
use Contao\StringUtil;

class TypeNewslist extends Base {

  private static $icon = '../../../system/themes/flexible/icons/news.svg';
  private static $iconclass = 'tl_news_baritem';

  public function run(CustomViewItem $item): CustomViewItem {

    // @TODO very hard because there could be more news_teasers!! No Idea at the Moment when more news are selected
    // At the Moment link to global News if more News are selected
    if (class_exists('\Contao\NewsArchiveModel')) {
      $newsarchives = StringUtil::deserialize($this->module->news_archives);
      if (count($newsarchives) == 1) {
        $objNews = \Contao\NewsArchiveModel::findById($newsarchives[0]);
        if ($objNews !== null) {
          if (BackendUser::getInstance()->hasAccess($objNews->id, 'news')) {
            $item->setValid(true);
            $item->setIcon(self::$icon);
            $item->setIconclass(self::$iconclass);
            $item->setPath('do=news&table=tl_news&id=' . $objNews->id);
            $item->setLabel($GLOBALS['TL_LANG']['alpdeskfee_mapping_lables']['news']);
          }
        }
      } else {
        $item->setValid(true);
        $item->setIcon(self::$icon);
        $item->setIconclass(self::$iconclass);
        $item->setPath('do=news');
        $item->setLabel($GLOBALS['TL_LANG']['alpdeskfee_mapping_lables']['news']);
      }
    }

    return $item;
  }

}
