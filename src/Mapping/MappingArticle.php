<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping;

use Contao\BackendUser;
use Contao\FrontendTemplate;
use Contao\Module;

class MappingArticle
{
    private FrontendTemplate $template;
    private array $newsEntry;
    private Module $module;
    private string $currentPageId;

    private static array $mappedNewsListTemplates = [];

    /**
     * NewsListArticle constructor.
     * @param FrontendTemplate $template
     * @param array $newsEntry
     * @param Module $module
     * @param string $currentPageId
     */
    public function __construct(FrontendTemplate $template, array $newsEntry, Module $module, string $currentPageId)
    {
        $this->template = $template;
        $this->newsEntry = $newsEntry;
        $this->module = $module;
        $this->currentPageId = $currentPageId;
    }

    /**
     * @return array
     */
    public static function getMappedNewsListTemplates(): array
    {
        return self::$mappedNewsListTemplates;
    }

    public function prepare(): void
    {
        if (class_exists('\Contao\ModuleNewsList') && $this->module instanceof \Contao\ModuleNewsList) {

            if (BackendUser::getInstance()->hasAccess($this->newsEntry['pid'], 'news')) {

                $mappingString = 'alpdeskfee_newslist_item_' . $this->newsEntry['id'];

                if (!\array_key_exists($this->template->getName(), self::$mappedNewsListTemplates)) {
                    self::$mappedNewsListTemplates[$this->template->getName()] = [];
                }

                $data = [
                    'type' => 'mod',
                    'do' => 'do=news&table=tl_news&id=' . $this->newsEntry['id'] . '&act=edit',
                    'act' => '',
                    'icon' => '../../../system/themes/flexible/icons/header.svg',
                    'iconclass' => 'tl_news_teaseritem',
                    'pageid' => $this->currentPageId,
                    'subviewitems' => [],
                    'desc' => 'Teaser'
                ];

                self::$mappedNewsListTemplates[$this->template->getName()][] = [
                    'mappingString' => $mappingString,
                    'data' => $data
                ];

                // Not so nice but currently there is no way to modify news_latest Template
                $this->template->class = $this->template->class . ' ' . $mappingString;

            }

        }


    }

}