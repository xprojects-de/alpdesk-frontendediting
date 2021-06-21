<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping;

use Contao\BackendUser;
use Contao\FrontendTemplate;
use Contao\Module;

class MappingArticle
{
    private $template;
    private $newsEntry;
    private $module;
    private $currentPageId;

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
     * @param string $buffer
     * @param string $classes
     * @param array $attributes
     * @return array|string|string[]|null
     */
    private function createElementsTags(string $buffer, string $classes, array $attributes)
    {
        $dataAttributes = \array_filter($attributes, function ($v) {
            return null !== $v;
        });

        return \preg_replace_callback('|<([a-zA-Z0-9]+)(\s[^>]*?)?(?<!/)>|', function ($matches) use ($classes, $dataAttributes) {
            $tag = $matches[1];
            $attributes = $matches[2];

            $attributes = preg_replace('/class="([^"]+)"/', 'class="$1 ' . $classes . '"', $attributes, 1, $count);
            if (0 === $count) {
                $attributes .= ' class="' . $classes . '"';
            }

            foreach ($dataAttributes as $key => $value) {
                $attributes .= ' ' . $key . "='" . $value . "'";
            }

            return "<{$tag}{$attributes}>";
        }, $buffer, 1);
    }

    public function prepare(): void
    {
        if (class_exists('\Contao\ModuleNewsList') && $this->module instanceof \Contao\ModuleNewsList) {

            if (BackendUser::getInstance()->hasAccess($this->newsEntry['pid'], 'news')) {

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

                if ($this->template->teaser === null || $this->template->teaser === '') {
                    $this->template->teaser = '<p>&nbsp;</p>';
                }

                $this->template->teaser = $this->createElementsTags($this->template->teaser, 'alpdeskfee-ce', [
                    'data-alpdeskfee' => \json_encode($data)
                ]);

            }

        }


    }

}