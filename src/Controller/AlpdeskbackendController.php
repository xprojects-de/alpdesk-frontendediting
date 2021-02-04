<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;

class AlpdeskbackendController extends AbstractController {

  private $twig;

  public function __construct(TwigEnvironment $twig) {
    $this->twig = $twig;
  }

  public function endpoint(): Response {
    return new Response($this->twig->render('@AlpdeskFrontendediting/alpdeskfee_be.html.twig', []));
  }

}
