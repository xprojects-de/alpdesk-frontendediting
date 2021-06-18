<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes;

use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;
use Contao\BackendUser;
use Contao\StringUtil;

class TypeNewslist extends Base
{
    public function run(CustomViewItem $item): CustomViewItem
    {
        // @TODO very hard because there could be more news_teasers!! No Idea at the Moment when more news are selected
        // At the Moment link to global News if more News are selected
        if (class_exists('\Contao\NewsArchiveModel')) {

            $newsarchives = StringUtil::deserialize($this->module->news_archives);
            if (count($newsarchives) == 1) {

                $objNews = \Contao\NewsArchiveModel::findById($newsarchives[0]);
                if ($objNews !== null) {

                    if (BackendUser::getInstance()->hasAccess($objNews->id, 'news')) {
                        $item->setValid(true);
                        $item->setPath('do=' . $this->backendmodule . '&table=' . $this->table . '&id=' . $objNews->id);
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
