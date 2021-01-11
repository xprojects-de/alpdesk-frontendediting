import { Component, ComponentFactoryResolver, ComponentRef, ElementRef, HostListener, Input, OnInit, ViewChild, ViewContainerRef } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { DomSanitizer } from '@angular/platform-browser';
import { Constants } from './classes/constants';
import { UrlGenerator } from './classes/url-generator';
import { ItemContainerComponent } from './item-container/item-container.component';
import { AlpdeskFeeServiceService } from './services/alpdesk-fee-service.service';
import { DialogData, ModalIframeComponent } from './utils/modal-iframe/modal-iframe.component';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent implements OnInit {

  // Just for Testing - Will be as Input from Component
  @Input('base') base: string = 'https://contao.local:8890/';
  @Input('rt') rt: string = 'GplU6loiSGPizKIrwqtSgWVnYe8TIJEbyrc_kmp7B-0&ref=E4Mql9-C';
  @Input('frameurl') frameurl: string = '/preview.php';
  @Input('frameheight') frameheight: string = '800px';

  @HostListener('document:' + Constants.ALPDESK_EVENTNAME, ['$event']) onAFEE_Event(event: CustomEvent) {
    //console.log(event.detail);
    if (event.detail.preRequestGet !== null && event.detail.preRequestGet !== undefined && event.detail.preRequestGet === true) {
      let params = event.detail;
      params.preRequestGet = false;
      this._alpdeskFeeService.callGetRequest(event.detail.url).subscribe(
        (data) => {
          //console.log(data);
          document.dispatchEvent(new CustomEvent(AlpdeskFeeServiceService.ALPDESK_EVENTNAME, {
            detail: params
          }));
        },
        (error) => {
          console.log(error);
          document.dispatchEvent(new CustomEvent(AlpdeskFeeServiceService.ALPDESK_EVENTNAME, {
            detail: params
          }));
        }
      );
    } else if (event.detail.preRequestPost !== null && event.detail.preRequestPost !== undefined && event.detail.preRequestPost === true) {
      let params = event.detail;
      params.preRequestPost = false;
      this._alpdeskFeeService.callPostRequest(event.detail.url, event.detail).subscribe(
        (data) => {
          document.dispatchEvent(new CustomEvent(AlpdeskFeeServiceService.ALPDESK_EVENTNAME, {
            detail: params
          }));
        },
        (error) => {
          console.log(error);
        }
      );
    } else if (event.detail.action !== null && event.detail.action !== undefined && event.detail.action === 'init') {
      this.scanElements(event.detail.labels, event.detail.pageEdit, event.detail.pageId);
    } else if (event.detail.dialog !== null && event.detail.dialog !== undefined && event.detail.dialog === true) {
      this.openDialog(event.detail);
    } else if (event.detail.reloadFrame !== null && event.detail.reloadFrame !== undefined && event.detail.reloadFrame === true) {
      this.reloadIframe();
    } else if (event.detail.framelocation !== null && event.detail.framelocation !== undefined && event.detail.framelocation !== '') {
      this.iframeLocation(event.detail.framelocation);
    }
  }

  @HostListener('document:' + Constants.ALPDESK_EVENTNAME_FRAME, ['$event']) onAFEEFrame_Event(event: CustomEvent) {
    //console.log(event.detail);    
  }

  @ViewChild('alpdeskfeeframe') alpdeskfeeframe!: ElementRef;

  title = 'alpdeskfee-client';
  url: any;
  frameWidth = '100%';

  constructor(private _sanitizer: DomSanitizer, private vcRef: ViewContainerRef, private resolver: ComponentFactoryResolver, private dialog: MatDialog, private _alpdeskFeeService: AlpdeskFeeServiceService) {
  }

  ngOnInit() {
    this.url = this._sanitizer.bypassSecurityTrustResourceUrl(this.frameurl);
    //console.log(this.url);
  }

  openDialog(params: any) {

    const ug: UrlGenerator = new UrlGenerator()

    const url = ug.generateUrl(params, this.base, this.rt);
    const dialogData: DialogData = { url: url };

    const dialogRef = this.dialog.open(ModalIframeComponent, {
      width: '900px',
      data: dialogData
    });

    dialogRef.afterClosed().subscribe(result => {
      this.reloadIframe();
    });
  }

  reloadIframe() {
    document.dispatchEvent(new CustomEvent(Constants.ALPDESK_EVENTNAME_FRAME, {
      detail: {
        reload: true
      }
    }));
    this.alpdeskfeeframe.nativeElement.contentWindow.location.reload();
  }

  iframeLocation(location: string) {
    this.alpdeskfeeframe.nativeElement.contentWindow.location.href = location;
  }

  iframeLoad() {
    document.dispatchEvent(new CustomEvent(Constants.ALPDESK_EVENTNAME_FRAME, {
      detail: {
        location: this.alpdeskfeeframe.nativeElement.contentWindow.location.href
      }
    }));
  }

  scanElements(objLabels: any, pageEdit: boolean, pageId: number) {

    if (objLabels !== null && objLabels !== undefined) {

      const frameContentWindow = this.alpdeskfeeframe.nativeElement.contentWindow;
      const frameContentDocument = this.alpdeskfeeframe.nativeElement.contentDocument;

      if (frameContentWindow !== null && frameContentWindow !== undefined && frameContentDocument !== null && frameContentDocument !== undefined) {

        const compFactory = this.resolver.resolveComponentFactory(ItemContainerComponent);
        const compRef: ComponentRef<ItemContainerComponent> = this.vcRef.createComponent(compFactory);
        compRef.instance.frameContentDocument = frameContentDocument;
        compRef.instance.base = this.base;
        compRef.instance.rt = this.rt;
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
              if (obj.type === Constants.TARGETTYPE_ARTICLE) {
                let parentNode = e.parentElement;
                if (parentNode !== null) {
                  parentNode.style.minHeight = '50px';
                  parentNode.classList.add('alpdeskfee-article-container');
                  /*parentNode.onmouseover = function (event) {
                    if (parentNode !== null && parentNode !== undefined) {
                      parentNode.style.outline = '2px dashed rgb(244, 124, 0)';
                    }
                  };
                  parentNode.onmouseout = function () {
                    if (parentNode !== null && parentNode !== undefined) {
                      parentNode.style.outline = '0px dashed rgb(244, 124, 0)';
                    }
                  };*/
                  parentNode.onclick = function () {
                    if (parentNode !== null) {
                      compRef.instance.changeParent(obj, parentNode);
                      compRef.changeDetectorRef.detectChanges();
                      //parentNode.style.outlineOffset = '4px';
                      //parentNode.style.borderLeft = '2px solid rgb(244, 124, 0)';
                    }
                  };
                }
              } else {
                e.classList.add('alpdeskfee-ce-container');
                e.onmouseover = function () {
                  e.style.outline = '2px dashed rgb(244, 124, 0)';
                  e.style.outlineOffset = '2px';
                };
                e.onmouseout = function () {
                  e.style.outline = '0px dashed rgb(244, 124, 0)';
                  e.style.outlineOffset = '0px';
                };
                e.onclick = function (event: Event) {

                  let cData = frameContentWindow.document.querySelectorAll("*[data-alpdeskfee]");
                  cData.forEach((eC: HTMLElement) => {
                    if (eC !== e) {                      
                      eC.style.border = 'none';
                    } 
                  });                  
                  e.style.outlineOffset = '4px';
                  e.style.border = '2px solid rgb(244, 124, 0)';

                  let currentElement = event.target as HTMLElement;
                  if (currentElement !== null && currentElement !== undefined) {
                    let jsonDataElement = currentElement.getAttribute('data-alpdeskfee');
                    if (jsonDataElement !== null && jsonDataElement !== undefined && jsonDataElement !== '') {                      
                      const objElement = JSON.parse(jsonDataElement);
                      if (objElement !== null && objElement !== undefined) {
                        compRef.instance.changeElement(objElement, currentElement);
                        compRef.changeDetectorRef.detectChanges();
                      }
                    } else {                      
                      let closestElement = currentElement.closest('*[data-alpdeskfee]') as HTMLElement;
                      if (closestElement !== null && closestElement !== undefined) {
                        let jsonDataElement = closestElement.getAttribute('data-alpdeskfee');
                        if (jsonDataElement !== null && jsonDataElement !== undefined && jsonDataElement !== '') {
                          const objElement = JSON.parse(jsonDataElement);
                          if (objElement !== null && objElement !== undefined) {
                            compRef.instance.changeElement(objElement, closestElement);
                            compRef.changeDetectorRef.detectChanges();
                          }
                        }
                      }
                    }
                  }
                };
              }
            }
          }
        });
      }
    }
  }

}
