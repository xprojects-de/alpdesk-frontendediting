<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes;

use Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes\Base;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;
use Contao\BackendUser;
use Contao\Input;
use Contao\StringUtil;

class TypeNewsReader extends Base {

  private static $DO = 'do=news&table=tl_content';

  public function run(CustomViewItem $item): CustomViewItem {

    if (class_exists('\Contao\NewsModel')) {
      $newsarchives = StringUtil::deserialize($this->module->news_archives);
      $objNews = \Contao\NewsModel::findPublishedByParentAndIdOrAlias(Input::get('items'), $newsarchives);
      if ($objNews !== null) {
        $objArchive = $objNews->getRelated('pid');
        if (BackendUser::getInstance()->hasAccess($objArchive->id, 'news')) {
          $item->setValid(true);
          $item->setPath(self::$DO . '&id=' . $objNews->id);
        }
      }
    }

    return $item;
  }

}
