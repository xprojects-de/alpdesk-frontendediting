<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes;

use Contao\Module;
use Contao\Form;
use Contao\ContentModel;
use Contao\BackendUser;
use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;

abstract class Base
{
    abstract public function run(CustomViewItem $item): CustomViewItem;

    public mixed $module;
    public mixed $element;
    public mixed $form;
    public mixed $icon;
    public mixed $iconclass;
    public mixed $label;
    public mixed $backendmodule;
    public mixed $additional_static_params = [];
    public mixed $table;

    private static function checkModuleAccess(mixed $moduletype, BackendUser $backendUser): bool
    {
        if (!$backendUser->hasAccess($moduletype, 'modules')) {
            return false;
        }

        return true;
    }

    public static function findClassByElement(ContentModel $element, ?array $mappingconfig, BackendUser $backendUser): mixed
    {
        if (\is_array($mappingconfig)) {

            foreach ($mappingconfig['alpdesk_frontendediting_mapping']['type_mapping'] as $value) {

                if ($value['element'] !== null) {

                    if ($element->type == $value['element']) {

                        $backendmodule = $value['backend_module'];
                        $mappingObject = $value['mapping_object'];
                        $icon = $value['icon'];
                        $iconclass = $value['iconclass'];
                        $labelkey = $value['labelkey'];
                        $additional_static_params = $value['additional_static_params'];
                        $table = $value['table'];

                        if (self::checkModuleAccess($backendmodule, $backendUser)) {

                            $class = new $mappingObject();
                            $class->element = $element;
                            $class->icon = $icon;
                            $class->iconclass = $iconclass;
                            $class->label = $GLOBALS['TL_LANG']['alpdeskfee_mapping_lables'][$labelkey];
                            $class->backendmodule = $backendmodule;
                            $class->additional_static_params = $additional_static_params;
                            $class->table = $table;

                            return $class;

                        }

                        break;

                    }

                }

            }

        }

        return null;
    }

    public static function findClassByModule(Module $module, ?array $mappingconfig, BackendUser $backendUser): mixed
    {
        if (\is_array($mappingconfig)) {

            foreach ($mappingconfig['alpdesk_frontendediting_mapping']['type_mapping'] as $value) {

                if ($value['module'] !== null) {

                    $mappingObject = $value['mapping_object'];
                    $backendmodule = $value['backend_module'];
                    $moduleobject = $value['module'];
                    $icon = $value['icon'];
                    $iconclass = $value['iconclass'];
                    $labelkey = $value['labelkey'];
                    $additional_static_params = $value['additional_static_params'];
                    $table = $value['table'];

                    if (class_exists($moduleobject) && $module instanceof $moduleobject) {

                        if (self::checkModuleAccess($backendmodule, $backendUser)) {

                            $class = new $mappingObject();
                            $class->module = $module;
                            $class->icon = $icon;
                            $class->iconclass = $iconclass;
                            $class->label = $GLOBALS['TL_LANG']['alpdeskfee_mapping_lables'][$labelkey];
                            $class->backendmodule = $backendmodule;
                            $class->additional_static_params = $additional_static_params;
                            $class->table = $table;

                            return $class;

                        }

                        break;

                    }

                }

            }

        }

        return null;
    }

    public static function findClassByForm(Form $form, ?array $mappingconfig, BackendUser $backendUser): mixed
    {
        if (\is_array($mappingconfig)) {

            foreach ($mappingconfig['alpdesk_frontendediting_mapping']['type_mapping'] as $value) {

                if ($value['module'] !== null) {

                    $mappingObject = $value['mapping_object'];
                    $backendmodule = $value['backend_module'];
                    $moduleobject = $value['module'];
                    $icon = $value['icon'];
                    $iconclass = $value['iconclass'];
                    $labelkey = $value['labelkey'];
                    $additional_static_params = $value['additional_static_params'];
                    $table = $value['table'];

                    if (class_exists($moduleobject) && $form instanceof $moduleobject) {

                        if (self::checkModuleAccess($backendmodule, $backendUser)) {

                            $class = new $mappingObject();
                            $class->form = $form;
                            $class->icon = $icon;
                            $class->iconclass = $iconclass;
                            $class->label = $GLOBALS['TL_LANG']['alpdeskfee_mapping_lables'][$labelkey];
                            $class->backendmodule = $backendmodule;
                            $class->additional_static_params = $additional_static_params;
                            $class->table = $table;

                            return $class;

                        }

                        break;

                    }

                }

            }

        }

        return null;
    }

}
