import { Component, ComponentFactoryResolver, ComponentRef, ElementRef, OnInit, ViewChild, ViewContainerRef } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { ItemContainerComponent } from './item-container/item-container.component';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent implements OnInit {

  @ViewChild('alpdeskfeeframe') alpdeskfeeframe!: ElementRef;

  title = 'alpdeskfee-client';  
  url: any;
  urlBase = 'https://contao.local:8890/preview.php';
  frameHeight = (window.innerHeight - 100) + 'px';
  frameWidth = '100%';

  compRef!: ComponentRef<ItemContainerComponent>;

  constructor(private _sanitizer: DomSanitizer, private vcRef: ViewContainerRef, private resolver: ComponentFactoryResolver) { 
  }

  ngOnInit() {
    this.url = this._sanitizer.bypassSecurityTrustResourceUrl(this.urlBase);
  }

  getFrameUrl() {
    return this._sanitizer.bypassSecurityTrustResourceUrl(this.url);
  }

  iframeLoad() {
    console.log('drinnen2');

    /*let doc = this.alpdeskfeeframe.nativeElement.contentDocument || this.alpdeskfeeframe.nativeElement.contentWindow;

    const compFactory = this.resolver.resolveComponentFactory(ItemContainerComponent);
    this.compRef = this.vcRef.createComponent(compFactory);
    this.compRef.location.nativeElement.id = 'innerComp';

    doc.body.prepend(this.compRef.location.nativeElement);*/
    console.log(this.alpdeskfeeframe.nativeElement.contentWindow.location.href);
  }

  scanElements() {

    /*let objLabels = null;
    if (alpdeskfeeLabels !== null && alpdeskfeeLabels !== undefined && alpdeskfeeLabels !== '') {
      objLabels = JSON.parse(alpdeskfeeLabels);
    }*/

    /*let data = document.querySelectorAll("*[data-alpdeskfee]");
    for (let i = 0; i < data.length; i++) {
      let jsonData = data[i].getAttribute('data-alpdeskfee');
      if (jsonData !== null && jsonData !== undefined && jsonData !== '') {
        const obj = JSON.parse(jsonData);
        if (obj !== null && obj !== undefined) {
          if (obj.type === TARGETTYPE_ARTICLE) {
            let parentNode = data[i].parentElement;
            parentNode.classList.add('alpdeskfee-article-container');
            appendUtilsContainer(obj, data[i], false, objLabels, true);
            parentNode.onmouseover = function () {
              data[i].classList.add("alpdeskfee-parent-active");
            };
            parentNode.onmouseout = function () {
              data[i].classList.remove("alpdeskfee-parent-active");
            };
          } else {
            data[i].classList.add('alpdeskfee-ce-container');
            appendUtilsContainer(obj, data[i], true, objLabels, true);
            data[i].onmouseover = function () {
              data[i].classList.add('alpdeskfee-active');
            };
            data[i].onmouseout = function () {
              data[i].classList.remove('alpdeskfee-active');
            };
            setContextMenu(data[i], 'alpdeskfee-active-force', '*[data-alpdeskfee]');
          }
        }
      }
    }*/
  }

}
