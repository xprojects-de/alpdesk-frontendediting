<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Listener;

use Symfony\Component\Security\Core\Security;
use Contao\CoreBundle\Event\MenuEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Contao\BackendUser;
use Alpdesk\AlpdeskFrontendediting\Utils\Utils;

class BackendmenuListener
{
    protected $router;
    protected $requestStack;
    private $security;

    public function __construct(Security $security, RouterInterface $router, RequestStack $requestStack)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->security = $security;
    }

    public function __invoke(MenuEvent $event): void
    {
        $backendUser = $this->security->getUser();

        if (!$backendUser instanceof BackendUser) {
            return;
        }

        Utils::mergeUserGroupPersmissions();
        if (!$backendUser->isAdmin && $backendUser->alpdesk_fee_enabled != 1) {
            return;
        }

        $factory = $event->getFactory();
        $tree = $event->getTree();

        if ('mainMenu' === $tree->getName()) {

            $contentNode = $tree->getChild('content');
            $node = $factory
                ->createItem('alpdesk_frontendediting_backend')
                ->setUri($this->router->generate('alpdesk_frontendediting_backend'))
                ->setLabel('Frontend-Editing')
                ->setLinkAttribute('title', 'Frontend-Editing')
                ->setLinkAttribute('class', 'alpdesk_frontendediting_backend')
                ->setCurrent($this->requestStack->getCurrentRequest()->get('_route') === 'alpdesk_frontendediting_backend');

            $contentNode->addChild($node);

        } else if ('headerMenu' === $tree->getName()) {

            $node = $factory
                ->createItem('alpdesk_frontendediting_backend')
                ->setUri($this->router->generate('alpdesk_frontendediting_backend'))
                ->setLabel('Frontend-Editing')
                ->setLinkAttribute('title', 'Frontend-Editing')
                ->setLinkAttribute('class', 'alpdesk_frontendediting_backend')
                ->setCurrent($this->requestStack->getCurrentRequest()->get('_route') === 'alpdesk_frontendediting_backend');

            $tree->addChild($node);
            $childs = $tree->getChildren();
            $newChilds = \array_merge(array_splice($childs, -1), $childs);
            $tree->setChildren($newChilds);

        }
    }

}
