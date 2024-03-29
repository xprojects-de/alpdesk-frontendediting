<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes;

use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;
use Contao\BackendUser;
use Contao\Input;
use Contao\StringUtil;

class TypeNewsReader extends Base
{
    public function run(CustomViewItem $item): CustomViewItem
    {
        if (class_exists('\Contao\NewsModel')) {

            $newsarchives = StringUtil::deserialize($this->module->news_archives);
            $objNews = \Contao\NewsModel::findPublishedByParentAndIdOrAlias(Input::get('items'), $newsarchives);
            if ($objNews !== null) {

                $objArchive = $objNews->getRelated('pid');
                if (BackendUser::getInstance()->hasAccess($objArchive->id, 'news')) {
                    $item->setValid(true);
                    $item->setPath('do=' . $this->backendmodule . '&table=' . $this->table . '&id=' . $objNews->id);
                }

            }

        }

        return $item;
    }

}
