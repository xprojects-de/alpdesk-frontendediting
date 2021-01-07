import { Component, ComponentFactoryResolver, ComponentRef, ElementRef, HostListener, Input, OnInit, ViewChild, ViewContainerRef } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { DomSanitizer } from '@angular/platform-browser';
import { UrlGenerator } from './classes/url-generator';
import { ItemContainerComponent } from './item-container/item-container.component';
import { DialogData, ModalIframeComponent } from './utils/modal-iframe/modal-iframe.component';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent implements OnInit {

  // Just for Testing - Will be as Input from Component
  @Input() alpdeskfeePageid = 19;
  @Input() alpdeskfeeCanPageEdit = true;
  @Input() alpdeskfeeLabels = '{"ce":"Element","mod":"Modul","page":"Seite","article":"Artikel","delete_confirm_article":"Artikel wirklich l\u00f6schen?","delete_confirm_element":"Element wirklich l\u00f6schen?","page_edit_top":"Diese Seite bearbeiten","article_edit_top":"Artikel der Seite bearbeiten","page_structure":"Seitenstruktur","page_edit":"Diese Seite bearbeiten","article_all":"\u00dcbersicht","edit_article":"Artikel editieren","delete_article":"Artikel l\u00f6schen","article_visible":"Artikel verstecken\/anzeigen","element_all":"\u00dcbersicht","new_element_top":"Neues Element erstellen","new_element":"Neues Element nach diesem Element erstellen","edit_element":"Element editieren","copy_element":"Element kopieren","delete_element":"Element l\u00f6schen","element_visible":"Element verstecken\/anzeigen","element_mod":"Modul bearbeiten"}';

  static ALPDESK_EVENTNAME = 'alpdesk_frontendediting_event'
  static ACTION_INIT = 'init';

  TARGETTYPE_PAGE = 'page';
  TARGETTYPE_ARTICLE = 'article';
  TARGETTYPE_CE = 'ce';
  TARGETTYPE_MOD = 'mod';

  @HostListener('document:' + AppComponent.ALPDESK_EVENTNAME, ['$event']) onAFEE_Event(event: CustomEvent) {
    console.log(event.detail);
    if (event.detail.dialog !== null && event.detail.dialog !== undefined && event.detail.dialog === true) {
      this.openDialog(event.detail);
    }
  }

  @ViewChild('alpdeskfeeframe') alpdeskfeeframe!: ElementRef;

  title = 'alpdeskfee-client';
  url: any;
  urlBase = 'https://contao.local:8890/preview.php';
  frameHeight = (window.innerHeight - 100) + 'px';
  frameWidth = '100%';
  frameLocation!: any;

  constructor(private _sanitizer: DomSanitizer, private vcRef: ViewContainerRef, private resolver: ComponentFactoryResolver, private dialog: MatDialog) {
  }

  ngOnInit() {
    this.url = this._sanitizer.bypassSecurityTrustResourceUrl(this.urlBase);
  }

  openDialog(params: any) {

    const ug: UrlGenerator = new UrlGenerator()

    const dialogData: DialogData = { url: ug.generateUrl(params) };

    const dialogRef = this.dialog.open(ModalIframeComponent, {
      width: '900px',
      data: dialogData
    });

    dialogRef.afterClosed().subscribe(result => {
      this.reloadIframe();
    });
  }

  reloadIframe() {
    this.alpdeskfeeframe.nativeElement.contentWindow.location.reload();
  }

  iframeLoad() {
    this.frameLocation = this.alpdeskfeeframe.nativeElement.contentWindow.location.href;
    this.scanElements(this.alpdeskfeeLabels, this.alpdeskfeeCanPageEdit, this.alpdeskfeePageid);
  }

  scanElements(objLabels: any, pageEdit: boolean, pageId: number) {

    if (objLabels !== null && objLabels !== undefined) {

      const frameContentWindow = this.alpdeskfeeframe.nativeElement.contentWindow;
      const frameContentDocument = this.alpdeskfeeframe.nativeElement.contentDocument;

      if (frameContentWindow !== null && frameContentWindow !== undefined && frameContentDocument !== null && frameContentDocument !== undefined) {

        const compFactory = this.resolver.resolveComponentFactory(ItemContainerComponent);
        const compRef: ComponentRef<ItemContainerComponent> = this.vcRef.createComponent(compFactory);
        compRef.instance.objLabels = objLabels;
        compRef.instance.pageEdit = pageEdit;
        compRef.instance.pageId = pageId;

        frameContentDocument.body.prepend(compRef.location.nativeElement);

        let data = frameContentWindow.document.querySelectorAll("*[data-alpdeskfee]");
        data.forEach((e: HTMLElement) => {
          let jsonData = e.getAttribute('data-alpdeskfee');
          if (jsonData !== null && jsonData !== undefined && jsonData !== '') {
            const obj = JSON.parse(jsonData);
            if (obj !== null && obj !== undefined) {
              if (obj.type === this.TARGETTYPE_ARTICLE) {
                let parentNode = e.parentElement;
                if (parentNode !== null) {
                  parentNode.style.minHeight = '50px';
                  parentNode.classList.add('alpdeskfee-article-container');
                  parentNode.onmouseover = function (event) {
                    if (parentNode !== null && parentNode !== undefined) {
                      parentNode.style.borderRight = '2px solid rgb(244, 124, 0)';
                    }
                  };
                  parentNode.onmouseout = function () {
                    if (parentNode !== null && parentNode !== undefined) {
                      parentNode.style.border = 'none';
                    }
                  };
                  parentNode.onclick = function () {
                    compRef.instance.changeParent(obj, parentNode, frameContentDocument.documentElement.scrollTop);
                    compRef.changeDetectorRef.detectChanges();
                  };
                }
              } else {
                e.classList.add('alpdeskfee-ce-container');
                e.onmouseover = function () {
                  e.style.outline = '1px dashed rgb(244, 124, 0)';
                  e.style.outlineOffset = '2px';
                };
                e.onmouseout = function () {
                  e.style.outline = '0px dashed rgb(244, 124, 0)';
                  e.style.outlineOffset = '0px';
                };
                e.onclick = function () {
                  let cData = frameContentWindow.document.querySelectorAll("*[data-alpdeskfee]");
                  cData.forEach((eC: HTMLElement) => {
                    if(eC !== e) {
                      eC.style.border = 'none';
                      eC.style.zIndex = '1';
                    }
                  });
                  e.style.border = '2px solid rgb(244, 124, 0)';
                  e.style.position = 'relative';
                  e.style.zIndex = '200000';
                  compRef.instance.changeElement(obj, e, frameContentDocument.documentElement.scrollTop);
                  compRef.changeDetectorRef.detectChanges();
                };
              }
            }
          }
        });
      }
    }
  }

}
