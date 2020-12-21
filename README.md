
# Alpdesk Frontend-Editing für Contao
Mit Hilfe dieser Erweiterung lässt sich deine Webseite visuell direkt im Backend bearbeiten.


## Konzept

Die Erweiterung ist ein normales Backendmodul und läuft somit direkt im Backend-Scope ohne dass irgendwelche anderen Routen bereitgestellt werden.
Auf Basis des Javascript-Eventhandlings werden lediglich lokale Events (Same-Origin-Policy) der Frontendansicht ins Backend geschickt und dort verarbeitet.
Das Backend bedient sich dann dem mächtigen Backend-Editing von Contao indem einfach die passenden URLs in einem Overlay-Dialog angezeigt werden. Somit steht das komplette Backend-Editing wie gewohnt zur Verfügung, nur halt ein bisschen visueller dargestellt.

Aufgrund der "Same-Origin-Policy" ist es nur möglich und auch gewollt das Frontend-Editing direkt übers Backend zu bedienen.
Wird die Frontend-Vorschau aufgerufen, sind die Elemente nicht verfügbar! Aus Sicherheitsgründen ist das auch so gewollt, da das Backend nur einen EventListener auf Events des "Same-Origin" bereitstellt und diese verarbeitet. Es wird KEIN "postMessage-Cross-Domain"-Eventlistenerer bereitgestellt, was auch gut so ist :-)

## Verwendung

Die Erweiterung gliedert sich als normales BackendModul ins Contao-Backend ein und lässt sich pro User aktivieren bzw. deaktivieren.
Desweiteren wird das komplette Rechtemanagement von Contao beachtet. So können z.B. nur die Inhaltselemente bearbeitet werden, welche den Rechten des Backend-Users entsprechen.
Weiter kann zusätzlich in den Benutzereinstellungen angewählt werden, welche Elemente dem Benutzer angezeigt werden. Somit kann z.B. auch ein berechtiges Inhaltselement ausgeblendet werden.

## Was kann alles editiert werden

- Alle Inhalselemente
- Newsmodul (Newsliste und Newsreader)
- Eventmodul (Eventliste und Eventreader)
- RocksolidSlider
- ... mehr werden folgen wenn diese gebraucht werden :-) Aber man kann auch auch selber tätig werden... (siehe Technisches)

## Mapping Backendmodule zu Inhaltselementen und Frontendmodulen

Die Herausforderung am Frontendediting ist nicht der Support der Inhalselemente und Frontendmodule an sich, sondern das passende Mapping zum passenden Backendmodul wo die Inhalte editiert werden können.
Die Erweiterung selber wird stehts weiterenwtickelt werden und die Standardmodule von Contao bei Zeit supporten (Events, Formulare, etc.).
Aber es ist es auch möglich die eigene Erweiterung über das Symfony-Eventhandling ins Frontend zum Editieren zu bringen. (mehr unter Technisches).


## Sonstiges

- Die Erweiterung verwendet bewusst keine Javascript-Libs und kommt mit nativem Javascript aus. Somit sollte es keine Probleme im Frontend geben
- Tests wurden mit den aktuellen Versionen von Firefox, Chrome und Safari durchgeführt


Die Erweiterung sollte voll funktionsfähig sein, bis auf die Bugs die ich selber nicht gefunden habe :-) Aber dennoch ist Sie noch "frisch" und ich bin über jegliches Feedback froh was die Erweiterung weiter bringt.

## Technisches

- Die Erweiterung stellt zwei Events bereit (alpdeskfrontendediting.element und alpdeskfrontendediting.module) auf welche man sich per EventListener registieren kann.
-  Das Event wird bei bei jedem Rendern eines FrontendModules und Inhaltselement getriggert und liefert jeweils das Model und das spezifische FrontendEditing-Object (Hooks getContentElement und getFrontendModule). Auf diesem Wege können vorhandene Frontend-Bar-Items angepasst werden oder sogar neue, eigene Frontend-Bar-Items hinzugefügt werden und mit passender Logik zum BackendModul versehen werden.

Beispiel um einen eigenen Icon für ein Inhaltselement einzufügen:

```
# service.yml oder listener.yml

services:

  projects.listener.alpdeskfrontendediting.element:
    class: XProjects\Projects\Events\ProjectsAlpdeskFrontendViewListener
    tags:
      - { name: kernel.event_listener, event: alpdeskfrontendediting.element}
```

```

// Passende Eventklasse dazu
// Modul funktioniert analog

declare(strict_types=1);

namespace XProjects\Projects\Events;

use Alpdesk\AlpdeskFrontendediting\Events\AlpdeskFrontendeditingEventElement;
use Contao\Input;
use Contao\Database;

class ProjectsAlpdeskFrontendViewListener {

  private static $icon = '../../../system/themes/flexible/icons/tablewizard.svg';
  private static $iconclass = 'tl_projects_baritem';

  public function __invoke(AlpdeskFrontendeditingEventElement $event): void {

    if ($event->getElement()->type === 'xprojects_overview') {
      $event->getItem()->setValid(true);
      $event->getItem()->setIcon(self::$icon);
      $event->getItem()->setIconclass(self::$iconclass);
      $event->getItem()->setPath('do=xprojects');
      $event->getItem()->setLabel($GLOBALS['TL_LANG']['projects_label']);
    } else if ($event->getElement()->type === 'xprojects_detail') {
      $alias = Input::get('projekte');
      if ($alias !== null && $alias !== '') {
        // Better use Model but Extention does not have a model
        $projectObj = Database::getInstance()->prepare("SELECT id FROM tl_xprojects WHERE alias=?")->execute($alias);
        if ($projectObj->numRows > 0) {
          $event->getItem()->setValid(true);
          $event->getItem()->setIcon(self::$icon);
          $event->getItem()->setIconclass(self::$iconclass);
          $event->getItem()->setPath('do=xprojects&table=tl_content&id=' . $projectObj->id);
          $event->getItem()->setLabel($GLOBALS['TL_LANG']['projects_label']);
        }
      }
    }
  }

}
```

- Die Item-Bars und die Rollover-Markierung des Frontend-Views arbeiten mit einem z-index: 1000. Manchmal kann es hier Probleme mit einem z.B. fixed header geben wo prinzipiell der Content hinter den Header läuft. Hat das fixe Element einen z-index < 1000 dann überlagert der Overlay diesen. Wenn nicht dann kann das in eurem Frontend-CSS angepasst werden. Hier müsst ihr dann einfach folgende CSS-Klassen überschreiben.

```
.alpdeskfee-active {z-index: XXXX;}
div.alpdeskfee-utilscontainer {z-index: XXXX;}
```



- Ist kein Backend-User eingelogged wird der ganze "zusätzliche" Code NICHT ausgeführt und die Hooks werden sofort wieder verlassen. Somit gibt es hier keine Performanceprobleme im normalen Betrieb.

## Was ist noch zu tun?

- Module optimieren (z.B. möglich machen die Teaser der Newsliste auch einzeln bearbeitbar zu machen)
- Weitere Module untersützen
- Bugfixing
- ...

Für diese Erweiterung übernehme ich keinerlei Haftung!

## Bilder
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/bild1.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/bild2.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/bild3.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/bild4.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/bild5.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/bild6.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/bild7.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/bild8.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/bild9.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/bild10.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/bild11.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/bild12.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/bild13.png" alt=""></p>   

