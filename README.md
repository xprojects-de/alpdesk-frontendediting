
# Alpdesk Frontend-Editing für Contao
Mit Hilfe dieser Erweiterung lässt sich deine Webseite visuell direkt im Backend bearbeiten.


## Konzept

Die Erweiterung ist ein normales Backendmodul und läuft somit direkt im Backend-Scope ohne dass (noch nicht) irgendwelche anderen Routen bereitgestellt werden.
Auf Basis des Javascript-Eventhandlings werden lediglich lokale Events der Frontendansicht ins Backend geschickt und dort verarbeitet.
Das Backend bedient sich dann dem mächtigen Backend-Editing von Contao indem einfach die passenden URLs in einem Overlay-Dialog angezeigt werden. Somit steht das komplette Backend-Editing wie gewohnt zur Verfügung, nur halt ein bisschen visueller dargestellt.

## Verwendung

Die Erweiterung gliedert sich als normales BackendModul ins Contao-Backend ein und lässt sich pro User aktivieren bzw. deaktivieren.
Desweiteren wird das komplette Rechtemanagement von Contao beachtet. So können z.B. nur die Inhaltselemente bearbeitet werden, welche den Rechten des Backend-Users entsprechen.
Weiter kann zusätzlich in den Benutzereinstellungen angewählt werden, welche Elemente dem Benutzer angezeigt werden. Somit kann z.B. auch ein berechtiges Inhaltselement ausgeblendet werden.

## Was kann alles editiert werden

- Alle Inhalselemente
- Newsmodul (Newsliste und Newsreader)
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
- Ist kein Backend-User eingelogged wird der ganze "zusätzliche" Code NICHT ausgeführt und die Hooks werden sofort wieder verlassen. Somit gibt es hier keine Performanceprobleme im normalen Betrieb.

Für diese Erweiterung übernehme ich keinerlei Haftung!

## Bilder
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/b1.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/b2.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/b3.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/b4.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/b5.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/b6.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/b7.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/b8.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/b9.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/b10.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/b11.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/b12.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/b13.png" alt=""></p>  
<p><img src="https://x-projects.de/files/alpdesk/frontendediting/b14.png" alt=""></p>  

