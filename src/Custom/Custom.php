<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Custom;

use Contao\Module;
use Contao\Form;
use Contao\ContentModel;
use Contao\ModuleModel;
use Alpdesk\AlpdeskFrontendediting\Mapping\Mapping;
use Alpdesk\AlpdeskFrontendediting\Events\AlpdeskFrontendeditingEventService;

class Custom
{
    public static function getModuleTypeInstanceById($moduleId)
    {
        try {

            $moduleObject = ModuleModel::findById($moduleId);

            $strClass = Module::findClass($moduleObject->type);
            if (!class_exists($strClass)) {
                return null;
            }

            $moduleObject->typePrefix = 'mod_';

            $objModule = new $strClass($moduleObject);
            if ($objModule !== null && $objModule instanceof Module) {
                return $objModule;
            } else if ($objModule !== null && $objModule instanceof Form) {
                return $objModule;
            } else {
                return null;
            }

        } catch (\Exception $e) {
            return null;
        }
    }

    public static function processForm(Form $form, AlpdeskFrontendeditingEventService $alpdeskfeeEventDispatcher, array $mappingconfig): CustomViewItem
    {
        $response = new CustomViewItem();
        $response->setType(CustomViewItem::$TYPE_FORM);

        return (new Mapping($alpdeskfeeEventDispatcher, $mappingconfig))->mapForm($response, $form);
    }

    public static function processModule(Module $module, AlpdeskFrontendeditingEventService $alpdeskfeeEventDispatcher, array $mappingconfig): CustomViewItem
    {
        $response = new CustomViewItem();
        $response->setType(CustomViewItem::$TYPE_MODULE);

        return (new Mapping($alpdeskfeeEventDispatcher, $mappingconfig))->mapModule($response, $module);
    }

    public static function processElement(ContentModel $element, AlpdeskFrontendeditingEventService $alpdeskfeeEventDispatcher, array $mappingconfig): CustomViewItem
    {
        $response = new CustomViewItem();
        $response->setType(CustomViewItem::$TYPE_CE);

        if ($element->type === 'module') {

            $objModule = self::getModuleTypeInstanceById($element->module);
            if ($objModule !== null) {

                if ($objModule instanceof Module) {
                    return self::processModule($objModule, $alpdeskfeeEventDispatcher, $mappingconfig);
                } else if ($objModule instanceof Form) {
                    return self::processForm($objModule, $alpdeskfeeEventDispatcher, $mappingconfig);
                }

            }

            return $response;

        }

        return (new Mapping($alpdeskfeeEventDispatcher, $mappingconfig))->mapContentElement($response, $element);
    }

}
