<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Backend;

use Contao\BackendTemplate;
use Contao\Environment;
use Contao\Input;
use Contao\Controller;
use Contao\BackendUser;
use Contao\UserModel;
use Contao\PageModel;
use Contao\System;

class AlpdeskFrontendeditingContainer {

  private function toggleFullesize() {
    $userModel = UserModel::findById(BackendUser::getInstance()->id);
    if ($userModel !== null) {
      $userModel->fullscreen = ($userModel->fullscreen == 1 ? 0 : 1);
      $userModel->save();
    }
    Controller::reload();
  }

  private function toggleLiveModus() {
    $liveModus = System::getContainer()->get('session')->get('alpdeskfee_livemodus');
    if ($liveModus === true) {
      System::getContainer()->get('session')->set('alpdeskfee_livemodus', false);
    } else {
      System::getContainer()->get('session')->set('alpdeskfee_livemodus', true);
    }
    Controller::reload();
  }

  private function getPageAlias($id) {
    $pageModel = PageModel::findById($id);
    if ($pageModel !== null) {
      System::getContainer()->get('session')->set('alpdeskfee_pageselect', $pageModel->alias);
    } else {
      System::getContainer()->get('session')->set('alpdeskfee_pageselect', '');
    }
    Controller::redirect('/contao?do=alpdeskfrontendediting');
  }

  public function generate(): string {

    if (Input::post('toggleFullsize')) {
      $this->toggleFullesize();
    } else if (Input::post('toggleLivemodus')) {
      $this->toggleLiveModus();
    } else if (Input::get('pageselect')) {
      $this->getPageAlias(Input::get('pageselect'));
    }

    $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/alpdeskfrontendediting/js/alpdeskfrontendediting_be.js';
    $GLOBALS['TL_CSS'][] = 'bundles/alpdeskfrontendediting/css/alpdeskfrontendediting_be.css';
    $GLOBALS['TL_CSS'][] = 'bundles/alpdeskfrontendediting/css/angular/styles.css';


    $containerTemplate = new BackendTemplate('be_alpdeskfrontendediting_container');
    $containerTemplate->token = REQUEST_TOKEN;
    $containerTemplate->base = Environment::get('base');
    $containerTemplate->livemodus = System::getContainer()->get('session')->get('alpdeskfee_livemodus');
    $alias = System::getContainer()->get('session')->get('alpdeskfee_pageselect');
    if ($alias !== null && $alias !== '') {
      $objUrlGenerator = System::getContainer()->get('contao.routing.url_generator');
      $containerTemplate->url = '/preview.php' . $objUrlGenerator->generate($alias);
    } else {
      $containerTemplate->url = '/preview.php';
    }

    return $containerTemplate->parse();
  }

}
