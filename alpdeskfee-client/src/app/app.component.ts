import { Component, ComponentFactoryResolver, ComponentRef, ElementRef, HostListener, OnInit, ViewChild, ViewContainerRef } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { ItemContainerComponent } from './item-container/item-container.component';
import { UtilsContainerComponent } from './utils-container/utils-container.component';

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
        compRef.location.nativeElement.id = 'innerComp';

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

                  const compFactoryUtils = this.resolver.resolveComponentFactory(UtilsContainerComponent);
                  const compRefUtils: ComponentRef<UtilsContainerComponent> = this.vcRef.createComponent(compFactoryUtils);
                  compRefUtils.location.nativeElement.classList.add('alpdeskfee-utils-container');
                  compRefUtils.location.nativeElement.id = 'alpdeskfee-' + obj.type + '-' + obj.id;
                  compRef.location.nativeElement.prepend(compRefUtils.location.nativeElement);

                  //appendUtilsContainer(obj, data[i], false, objLabels, true);
                  /*parentNode.onmouseover = function () {
                    data[i].classList.add("alpdeskfee-parent-active");
                  };
                  parentNode.onmouseout = function () {
                    data[i].classList.remove("alpdeskfee-parent-active");
                  };*/
                }
              } else {
                e.classList.add('alpdeskfee-ce-container');

                const compFactoryUtils = this.resolver.resolveComponentFactory(UtilsContainerComponent);
                const compRefUtils: ComponentRef<UtilsContainerComponent> = this.vcRef.createComponent(compFactoryUtils);
                compRefUtils.location.nativeElement.classList.add('alpdeskfee-utils-container');
                compRefUtils.location.nativeElement.id = 'alpdeskfee-' + obj.type + '-' + obj.id;
                compRef.location.nativeElement.prepend(compRefUtils.location.nativeElement);

                /*appendUtilsContainer(obj, data[i], true, objLabels, true);
                data[i].onmouseover = function () {
                  data[i].classList.add('alpdeskfee-active');
                };
                data[i].onmouseout = function () {
                  data[i].classList.remove('alpdeskfee-active');
                };
                setContextMenu(data[i], 'alpdeskfee-active-force', '*[data-alpdeskfee]');*/
              }
            }
          }
        });
      }
    }
  }

}
