<?php

use Contao\CoreBundle\DataContainer\PaletteManipulator;

PaletteManipulator::create()
    ->addLegend('alpdeskfee_legend', 'elements_legend', PaletteManipulator::POSITION_BEFORE, true)
    ->addField('alpdesk_fee_enabled', 'alpdeskfee_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('alpdesk_fee_elements', 'alpdeskfee_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('extend', 'tl_user')
    ->applyToPalette('custom', 'tl_user');

PaletteManipulator::create()
    ->addField('alpdesk_fee_admin_disabled', 'admin_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('admin', 'tl_user');

$GLOBALS['TL_DCA']['tl_user']['fields']['alpdesk_fee_enabled'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_user']['alpdesk_fee_enabled'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'reference' => &$GLOBALS['TL_LANG']['CTE'],
    'eval' => array('multiple' => false, 'helpwizard' => false, 'tl_class' => 'clr'),
    'sql' => "int(10) unsigned NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_user']['fields']['alpdesk_fee_admin_disabled'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_user']['alpdesk_fee_admin_disabled'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => array('multiple' => false, 'helpwizard' => false, 'tl_class' => 'clr'),
    'sql' => "int(10) unsigned NOT NULL default '0'"
];

$GLOBALS['TL_DCA']['tl_user']['fields']['alpdesk_fee_elements'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_user_group']['alpdesk_fee_elements'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'options_callback' => array_map('array_keys', $GLOBALS['TL_CTE']),
    'reference' => &$GLOBALS['TL_LANG']['CTE'],
    'eval' => array('multiple' => true, 'helpwizard' => true, 'tl_class' => 'clr'),
    'sql' => "blob NULL"
];
