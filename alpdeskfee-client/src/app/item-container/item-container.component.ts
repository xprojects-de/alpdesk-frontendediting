import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';

@Component({
  selector: 'app-item-container',
  templateUrl: './item-container.component.html',
  styleUrls: ['./item-container.component.scss']
})
export class ItemContainerComponent implements OnInit {

  @ViewChild('articleContainer') articleContainer!: ElementRef;
  @ViewChild('elementContainer') elementContainer!: ElementRef;
  @ViewChild('modContainer') modContainer!: ElementRef;

  frameContentDocument!: HTMLDocument;

  currentHeight = 35;

  base: string = '';
  rt: string = '';

  objLabels: any;
  pageEdit: boolean = false;
  pageId: number = 0;

  elementParent!: HTMLElement;
  jsonDataParent: any;
  offsetTopParent: string = '0px';
  
  elementElement!: HTMLElement;
  jsonDataElement: any;
  offsetTopElement: string = '0px';
  offsetLeftElement: string = '0px';
  transformElement: string =  'translate3d(0, 0, 0)';
  

  TARGETTYPE_PAGE = 'page';
  TARGETTYPE_ARTICLE = 'article';
  TARGETTYPE_CE = 'ce';
  TARGETTYPE_MOD = 'mod';

  ACTION_PARENT_EDIT = 'parent_edit';
  ACTION_ELEMENT_EDIT = 'element_edit';
  ACTION_ELEMENT_VISIBILITY = 'element_visibility';
  ACTION_ELEMENT_DELETE = 'element_delete';
  ACTION_ELEMENT_SHOW = 'element_show';
  ACTION_ELEMENT_NEW = 'element_new';
  ACTION_ELEMENT_COPY = 'element_copy';

  constructor() { }

  ngOnInit(): void {
  }

  changeParent(jsonData: any, element: HTMLElement): void {
    this.jsonDataParent = jsonData;
    this.elementParent = element;  
    if (this.elementParent !== null) {
      let top = element.getBoundingClientRect().top - this.currentHeight;
      if (top > 0) {
        this.offsetTopParent = (top + this.frameContentDocument.documentElement.scrollTop) + 'px';
      } else {
        this.offsetTopParent = this.frameContentDocument.documentElement.scrollTop + 'px';
      }
    } else {
      this.offsetTopParent = '0px';
    }  
  }

  changeElement(jsonData: any, element: HTMLElement): void {
    this.jsonDataElement = jsonData;
    this.elementElement = element;
    if (this.elementElement !== null) {
      let top = element.getBoundingClientRect().top - this.currentHeight;
      if (top > 0) {
        this.offsetTopElement = (top + this.frameContentDocument.documentElement.scrollTop) + 'px';
      } else {
        this.offsetTopElement = this.frameContentDocument.documentElement.scrollTop + 'px';
      }
      this.offsetLeftElement = element.getBoundingClientRect().left + 'px';
      if(this.elementContainer !== null && this.elementContainer !== undefined) {
        this.elementContainer.nativeElement.style.transform = this.transformElement;
      }
      if(this.modContainer !== null && this.modContainer !== undefined) {
        this.modContainer.nativeElement.style.transform = this.transformElement;
      }
    } else {
      this.offsetTopElement = '0px';
      this.offsetLeftElement = '0px';
      if(this.elementContainer !== null && this.elementContainer !== undefined) {
        this.elementContainer.nativeElement.style.transform = this.transformElement;
      }
      if(this.modContainer !== null && this.modContainer !== undefined) {
        this.modContainer.nativeElement.style.transform = this.transformElement;
      }
    }

  }

}
