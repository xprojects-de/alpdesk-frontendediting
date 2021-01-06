import { Component, ComponentFactoryResolver, ComponentRef, ElementRef, HostListener, OnInit, ViewChild, ViewContainerRef } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { ItemContainerComponent } from './item-container/item-container.component';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent implements OnInit {

  static ALPDESK_EVENTNAME = 'alpdesk_frontendediting_event'
  static ACTION_INIT = 'init';

  TARGETTYPE_PAGE = 'page';
  TARGETTYPE_ARTICLE = 'article';
  TARGETTYPE_CE = 'ce';
  TARGETTYPE_MOD = 'mod';

  @HostListener('document:' + AppComponent.ALPDESK_EVENTNAME, ['$event']) onAFEE_Event(event: CustomEvent) {
    console.log(event.detail);
    if (event.detail.action === AppComponent.ACTION_INIT) {
      this.scanElements(event.detail.labels);
    } 
  }

  @ViewChild('alpdeskfeeframe') alpdeskfeeframe!: ElementRef;

  title = 'alpdeskfee-client';
  url: any;
  urlBase = 'https://contao.local:8890/preview.php';
  frameHeight = (window.innerHeight - 100) + 'px';
  frameWidth = '100%';
  frameLocation!: any;

  constructor(private _sanitizer: DomSanitizer, private vcRef: ViewContainerRef, private resolver: ComponentFactoryResolver) {
  }

  ngOnInit() {
    this.url = this._sanitizer.bypassSecurityTrustResourceUrl(this.urlBase);
  }


  iframeLoad() {
    this.frameLocation = this.alpdeskfeeframe.nativeElement.contentWindow.location.href;
  }

  scanElements(objLabels: any) {

    if (objLabels !== null && objLabels !== undefined) {

      const frameContentWindow = this.alpdeskfeeframe.nativeElement.contentWindow;
      const frameContentDocument = this.alpdeskfeeframe.nativeElement.contentDocument;

      if (frameContentWindow !== null && frameContentWindow !== undefined && frameContentDocument !== null && frameContentDocument !== undefined) {

        const compFactory = this.resolver.resolveComponentFactory(ItemContainerComponent);
        const compRef: ComponentRef<ItemContainerComponent> = this.vcRef.createComponent(compFactory);
        compRef.instance.objLabels = objLabels;

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
                  parentNode.classList.add('alpdeskfee-article-container');
                  parentNode.onmouseover = function (event) {
                    if (parentNode !== null && parentNode !== undefined) {
                      parentNode.classList.add("alpdeskfee-parent-active");
                      compRef.instance.changeParent(obj, parentNode, frameContentDocument.documentElement.scrollTop);
                      compRef.changeDetectorRef.detectChanges();
                    }
                  };
                  parentNode.onmouseout = function () {
                    if (parentNode !== null && parentNode !== undefined) {
                      parentNode.classList.remove("alpdeskfee-parent-active");
                      compRef.instance.changeParent(null, null, 0);
                      compRef.changeDetectorRef.detectChanges();
                    }
                  };
                }
              } else {
                e.classList.add('alpdeskfee-ce-container');
                e.onmouseover = function (event) {
                  e.classList.add('alpdeskfee-active');
                  compRef.instance.changeElement(obj, e, frameContentDocument.documentElement.scrollTop);
                  compRef.changeDetectorRef.detectChanges();
                };
                e.onmouseout = function () {
                  e.classList.remove('alpdeskfee-active');
                  //compRef.instance.changeElement(null, null, 0);
                  //compRef.changeDetectorRef.detectChanges();
                };
                //setContextMenu(data[i], 'alpdeskfee-active-force', '*[data-alpdeskfee]');
              }
            }
          }
        });
      }
    }
  }

}
