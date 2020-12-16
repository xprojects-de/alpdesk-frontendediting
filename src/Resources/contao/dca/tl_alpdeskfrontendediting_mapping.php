<?php

use Contao\System;
use Contao\Controller;
use Contao\DataContainer;
use Contao\Backend;
use Contao\StringUtil;

System::loadLanguageFile('tl_user');
Controller::loadDataContainer('tl_content');

$GLOBALS['TL_DCA']['tl_alpdeskfrontendediting_mapping'] = [
    'config' => [
        'dataContainer' => 'Table',
        'enableVersioning' => false,
        'sql' => [
            'keys' => [
                'id' => 'primary'
            ]
        ]
    ],
    'list' => [
        'sorting' => [
            'mode' => 2,
            'fields' => ['title ASC'],
            'flag' => 1,
            'panelLayout' => 'filter;sort,search,limit'
        ],
        'label' => [
            'fields' => ['title'],
            'showColumns' => true
        ],
        'global_operations' => [
            'all' => [
                'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            ]
        ],
        'operations' => [
            'edit' => [
                'label' => &$GLOBALS['TL_LANG']['tl_alpdeskfrontendediting_mapping']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.gif'
            ],
            'delete' => [
                'label' => &$GLOBALS['TL_LANG']['tl_alpdeskfrontendediting_mapping']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ]
        ]
    ],
    'palettes' => [
        'default' => 'title;modules;elements,acteditident;fe_modules'
    ],
    'fields' => [
        'id' => [
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ],
        'title' => [
            'label' => &$GLOBALS['TL_LANG']['tl_alpdeskfrontendediting_mapping']['title'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 250, 'tl_class' => 'w50'],
            'sql' => "varchar(250) NOT NULL default ''"
        ],
        'modules' => [
            'label' => &$GLOBALS['TL_LANG']['tl_user']['modules'],
            'exclude' => true,
            'filter' => true,
            'inputType' => 'select',
            'options_callback' => ['tl_alpdeskfrontendediting_mapping', 'getModules'],
            'reference' => &$GLOBALS['TL_LANG']['MOD'],
            'eval' => ['multiple' => false, 'helpwizard' => true, 'unique' => false, 'tl_class' => 'w50'],
            'sql' => "varchar(250) NOT NULL default ''"
        ],
        'elements' => [
            'label' => &$GLOBALS['TL_LANG']['tl_user']['elements'],
            'exclude' => true,
            'filter' => true,
            'inputType' => 'select',
            'options_callback' => ['tl_alpdeskfrontendediting_mapping', 'getContentElements'],
            'reference' => &$GLOBALS['TL_LANG']['CTE'],
            'eval' => ['multiple' => false, 'helpwizard' => true, 'unique' => true, 'tl_class' => 'w50', 'includeBlankOption' => true],
            'sql' => "varchar(250) NOT NULL default ''"
        ],
        'acteditident' => [
            'label' => &$GLOBALS['TL_LANG']['tl_alpdeskfrontendediting_mapping']['acteditident'],
            'exclude' => true,
            'filter' => true,
            'inputType' => 'select',
            'options_callback' => ['tl_alpdeskfrontendediting_mapping', 'getContentElementsFields'],
            'eval' => ['multiple' => false, 'unique' => false, 'tl_class' => 'w50', 'includeBlankOption' => true],
            'sql' => "varchar(250) NOT NULL default ''"
        ],
        'fe_modules' => [
            'label' => &$GLOBALS['TL_LANG']['tl_alpdeskfrontendediting_mapping']['fe_modules'],
            'exclude' => true,
            'filter' => true,
            'inputType' => 'select',
            'foreignKey' => 'tl_module.name',
            'eval' => ['multiple' => false, 'helpwizard' => false, 'unique' => true, 'tl_class' => 'w50', 'includeBlankOption' => true],
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ],
    ]
];

class tl_alpdeskfrontendediting_mapping extends Backend {

  public function getModules(DataContainer $dc) {
    $arrModules = [];

    foreach ($GLOBALS['BE_MOD'] as $k => $v) {
      if (empty($v)) {
        continue;
      }

      foreach ($v as $kk => $vv) {
        if (isset($vv['disablePermissionChecks']) && $vv['disablePermissionChecks'] === true) {
          unset($v[$kk]);
        }
      }

      $arrModules[$k] = \array_keys($v);
    }

    $modules = StringUtil::deserialize($dc->activeRecord->modules);

    // Unset the template editor unless the user is an administrator or has been granted access to the template editor
    if (!$this->User->isAdmin && (!\is_array($modules) || !\in_array('tpl_editor', $modules)) && ($key = \array_search('tpl_editor', $arrModules['design'])) !== false) {
      unset($arrModules['design'][$key]);
      $arrModules['design'] = \array_values($arrModules['design']);
    }

    return $arrModules;
  }

  public function getContentElements(DataContainer $dc) {
    return \array_map('array_keys', $GLOBALS['TL_CTE']);
  }

  public function getContentElementsFields(DataContainer $dc) {
    $data = [];
    foreach ($GLOBALS['TL_DCA']['tl_content']['fields'] as $key => $value) {
      $data[$key] = $key;
    }
    return $data;
  }

}
