<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Events;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AlpdeskFrontendeditingEventService {

  protected $dispatcher;

  public function __construct(EventDispatcherInterface $dispatcher) {
    $this->dispatcher = $dispatcher;
  }

  public function getDispatcher(): EventDispatcherInterface {
    return $this->dispatcher;
  }

}
