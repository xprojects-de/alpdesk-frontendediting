<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Custom;

use Contao\BackendUser;
use Contao\Module;
use Contao\Form;
use Contao\ContentModel;
use Contao\ModuleModel;
use Alpdesk\AlpdeskFrontendediting\Mapping\Mapping;
use Alpdesk\AlpdeskFrontendediting\Events\AlpdeskFrontendeditingEventService;

class Custom
{
    public static function getModuleTypeInstanceById(mixed $moduleId): mixed
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

    public static function processForm(Form $form, AlpdeskFrontendeditingEventService $alpdeskfeeEventDispatcher, array $mappingconfig, BackendUser $backendUser): CustomViewItem
    {
        $response = new CustomViewItem($backendUser);
        $response->setType(CustomViewItem::$TYPE_FORM);

        return (new Mapping($alpdeskfeeEventDispatcher, $mappingconfig, $backendUser))->mapForm($response, $form);
    }

    public static function processModule(Module $module, AlpdeskFrontendeditingEventService $alpdeskfeeEventDispatcher, array $mappingconfig, BackendUser $backendUser): CustomViewItem
    {
        $response = new CustomViewItem($backendUser);
        $response->setType(CustomViewItem::$TYPE_MODULE);

        return (new Mapping($alpdeskfeeEventDispatcher, $mappingconfig, $backendUser))->mapModule($response, $module);
    }

    public static function processElement(ContentModel $element, AlpdeskFrontendeditingEventService $alpdeskfeeEventDispatcher, array $mappingconfig, BackendUser $backendUser): CustomViewItem
    {
        $response = new CustomViewItem($backendUser);
        $response->setType(CustomViewItem::$TYPE_CE);

        if ($element->type === 'module') {

            $objModule = self::getModuleTypeInstanceById($element->module);
            if ($objModule !== null) {

                if ($objModule instanceof Module) {
                    return self::processModule($objModule, $alpdeskfeeEventDispatcher, $mappingconfig, $backendUser);
                } else if ($objModule instanceof Form) {
                    return self::processForm($objModule, $alpdeskfeeEventDispatcher, $mappingconfig, $backendUser);
                }

            }

            return $response;

        }

        return (new Mapping($alpdeskfeeEventDispatcher, $mappingconfig, $backendUser))->mapContentElement($response, $element);
    }

}
