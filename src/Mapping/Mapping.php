<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Mapping;

use Alpdesk\AlpdeskFrontendediting\Custom\CustomViewItem;
use Contao\Module;
use Contao\Form;
use Contao\ContentModel;
use Contao\BackendUser;
use Alpdesk\AlpdeskFrontendediting\Mapping\Mappingtypes\Base;
use Alpdesk\AlpdeskFrontendediting\Events\AlpdeskFrontendeditingEventService;
use Alpdesk\AlpdeskFrontendediting\Events\AlpdeskFrontendeditingEventElement;
use Alpdesk\AlpdeskFrontendediting\Events\AlpdeskFrontendeditingEventModule;
use Alpdesk\AlpdeskFrontendediting\Events\AlpdeskFrontendeditingEventForm;

class Mapping
{
    private AlpdeskFrontendeditingEventService $alpdeskfeeEventDispatcher;
    private ?array $mappingconfig;

    private BackendUser $backendUser;

    public function __construct(AlpdeskFrontendeditingEventService $alpdeskfeeEventDispatcher, ?array $mappingconfig, BackendUser $backendUser)
    {
        $this->alpdeskfeeEventDispatcher = $alpdeskfeeEventDispatcher;
        $this->mappingconfig = $mappingconfig;
        $this->backendUser = $backendUser;
    }

    public function checkCustomTypeElementAccess(ContentModel $element, CustomViewItem $item): void
    {
        //e.g. if user has access to special news item or event item defined in user settings
        // Check if tl_content element can be editied e.g. news which have a different ptable
        if (\is_array($this->mappingconfig)) {

            $pTable = str_replace('tl_', '', $element->ptable);
            if (\array_key_exists($pTable, $this->mappingconfig['alpdesk_frontendediting_mapping']['element_access_check'])) {

                $model = $this->mappingconfig['alpdesk_frontendediting_mapping']['element_access_check'][$pTable]['model'];
                $item->setHasParentAccess(false);
                $accesskey = $this->mappingconfig['alpdesk_frontendediting_mapping']['element_access_check'][$pTable]['accesskey'];
                if (class_exists($model)) {

                    $objModel = $model::findById($element->pid);
                    if ($objModel !== null) {

                        $objModelParent = $objModel->getRelated('pid');
                        if ($this->backendUser->hasAccess($objModelParent->id, $accesskey)) {
                            $item->setHasParentAccess(true);
                        }

                    }

                }

            }

        }
    }

    public function checkCustomBackendModule(ContentModel $element, CustomViewItem $item): void
    {
        // Maybe a tl_content element hast a custom BackendModule not equal to ptable
        if ($this->mappingconfig !== null && \is_array($this->mappingconfig)) {

            $pTable = str_replace('tl_', '', $element->ptable);
            if (\array_key_exists($pTable, $this->mappingconfig['alpdesk_frontendediting_mapping']['element_backendmodule_mapping'])) {
                $backendmodule = $this->mappingconfig['alpdesk_frontendediting_mapping']['element_backendmodule_mapping'][$pTable]['backend_module'];
                $item->setCustomBackendModule($backendmodule);
            }

        }
    }

    public function mapContentElement(CustomViewItem $item, ContentModel $element): CustomViewItem
    {
        // If a tl_content item of any other parent than article is rendered (e.g. News) check rights an map backendmodule
        $this->checkCustomTypeElementAccess($element, $item);
        $this->checkCustomBackendModule($element, $item);

        $instance = Base::findClassByElement($element, $this->mappingconfig, $this->backendUser);
        if ($instance !== null) {

            $item->setIcon($instance->icon);
            $item->setIconclass($instance->iconclass);
            $item->setLabel($instance->label);

            $modifiedItem = $instance->run($item);

            $eventElement = new AlpdeskFrontendeditingEventElement($modifiedItem, $element);
            $this->alpdeskfeeEventDispatcher->getDispatcher()->dispatch($eventElement, AlpdeskFrontendeditingEventElement::NAME);

            return $eventElement->getItem();

        }

        $eventElement = new AlpdeskFrontendeditingEventElement($item, $element);
        $this->alpdeskfeeEventDispatcher->getDispatcher()->dispatch($eventElement, AlpdeskFrontendeditingEventElement::NAME);

        return $eventElement->getItem();
    }

    public function mapModule(CustomViewItem $item, Module $module): CustomViewItem
    {
        $instance = Base::findClassByModule($module, $this->mappingconfig, $this->backendUser);
        if ($instance !== null) {

            $item->setIcon($instance->icon);
            $item->setIconclass($instance->iconclass);
            $item->setLabel($instance->label);

            $modifiedItem = $instance->run($item);

            $eventModule = new AlpdeskFrontendeditingEventModule($modifiedItem, $module);
            $this->alpdeskfeeEventDispatcher->getDispatcher()->dispatch($eventModule, AlpdeskFrontendeditingEventModule::NAME);

            return $eventModule->getItem();

        }

        $eventModule = new AlpdeskFrontendeditingEventModule($item, $module);
        $this->alpdeskfeeEventDispatcher->getDispatcher()->dispatch($eventModule, AlpdeskFrontendeditingEventModule::NAME);

        return $eventModule->getItem();
    }

    public function mapForm(CustomViewItem $item, Form $form): CustomViewItem
    {
        $instance = Base::findClassByForm($form, $this->mappingconfig, $this->backendUser);
        if ($instance !== null) {

            $item->setIcon($instance->icon);
            $item->setIconclass($instance->iconclass);
            $item->setLabel($instance->label);

            $modifiedItem = $instance->run($item);

            $eventModule = new AlpdeskFrontendeditingEventForm($modifiedItem, $form);
            $this->alpdeskfeeEventDispatcher->getDispatcher()->dispatch($eventModule, AlpdeskFrontendeditingEventForm::NAME);

            return $eventModule->getItem();

        }

        $eventModule = new AlpdeskFrontendeditingEventForm($item, $form);
        $this->alpdeskfeeEventDispatcher->getDispatcher()->dispatch($eventModule, AlpdeskFrontendeditingEventForm::NAME);

        return $eventModule->getItem();

    }

}
