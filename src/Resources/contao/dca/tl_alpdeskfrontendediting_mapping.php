<?php

use Contao\System;
use Contao\Controller;
use Contao\DataContainer;
use Contao\Backend;
use Contao\StringUtil;

System::loadLanguageFile('tl_user');
Controller::loadDataContainer('tl_content');

$GLOBALS['TL_DCA']['tl_alpdeskfrontendediting_mapping'] = array
    (
    'config' => array
        (
        'dataContainer' => 'Table',
        'enableVersioning' => false,
        'sql' => array
            (
            'keys' => array
                (
                'id' => 'primary'
            )
        )
    ),
    'list' => array
        (
        'sorting' => array
            (
            'mode' => 2,
            'fields' => array('title ASC'),
            'flag' => 1,
            'panelLayout' => 'filter;sort,search,limit'
        ),
        'label' => array
            (
            'fields' => array('title'),
            'showColumns' => true
        ),
        'global_operations' => array
            (
            'all' => array
                (
                'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
        'operations' => array
            (
            'edit' => array
                (
                'label' => &$GLOBALS['TL_LANG']['tl_alpdeskfrontendediting_mapping']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.gif'
            ),
            'delete' => array
                (
                'label' => &$GLOBALS['TL_LANG']['tl_alpdeskfrontendediting_mapping']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            )
        )
    ),
    'palettes' => array
        (
        'default' => 'title;modules,elements;acteditident'
    ),
    'fields' => array
        (
        'id' => array
            (
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp' => array
            (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'title' => array
            (
            'label' => &$GLOBALS['TL_LANG']['tl_alpdeskfrontendediting_mapping']['title'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'maxlength' => 250, 'tl_class' => 'w50'),
            'sql' => "varchar(250) NOT NULL default ''"
        ),
        'modules' => array
            (
            'label' => &$GLOBALS['TL_LANG']['tl_user']['modules'],
            'exclude' => true,
            'filter' => true,
            'inputType' => 'select',
            'options_callback' => array('tl_alpdeskfrontendediting_mapping', 'getModules'),
            'reference' => &$GLOBALS['TL_LANG']['MOD'],
            'eval' => array('multiple' => false, 'helpwizard' => true, 'unique' => true, 'tl_class' => 'w50'),
            'sql' => "varchar(250) NOT NULL default ''"
        ),
        'elements' => array
            (
            'label' => &$GLOBALS['TL_LANG']['tl_user']['elements'],
            'exclude' => true,
            'filter' => true,
            'inputType' => 'select',
            'options_callback' => array('tl_alpdeskfrontendediting_mapping', 'getContentElements'),
            'reference' => &$GLOBALS['TL_LANG']['CTE'],
            'eval' => array('multiple' => false, 'helpwizard' => true, 'unique' => true, 'tl_class' => 'w50'),
            'sql' => "varchar(250) NOT NULL default ''"
        ),
        'acteditident' => array
            (
            'label' => &$GLOBALS['TL_LANG']['tl_alpdeskfrontendediting_mapping']['acteditident'],
            'exclude' => true,
            'filter' => true,
            'inputType' => 'select',
            'options_callback' => array('tl_alpdeskfrontendediting_mapping', 'getContentElementsFields'),
            'eval' => array('multiple' => false, 'unique' => false, 'tl_class' => 'w50', 'includeBlankOption' => true),
            'sql' => "varchar(250) NOT NULL default ''"
        ),
    )
);

class tl_alpdeskfrontendediting_mapping extends Backend {

  public function getModules(DataContainer $dc) {
    $arrModules = array();

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
