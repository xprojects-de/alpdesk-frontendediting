import { AfterViewInit, Component, ElementRef, ViewChild } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent implements AfterViewInit {

  @ViewChild('alpdeskfeeframe') alpdeskfeeframe!: ElementRef;

  title = 'alpdeskfee-client';  
  url = 'https://contao.local:8890/preview.php';
  frameHeight = (window.innerHeight - 100) + 'px';
  frameWidth = '100%';

  constructor(private _sanitizer: DomSanitizer) { 
  }

  ngAfterViewInit() {
    console.log(this.alpdeskfeeframe.nativeElement.contentDocument);
  }

  getFrameUrl() {
    return this._sanitizer.bypassSecurityTrustResourceUrl(this.url);
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
