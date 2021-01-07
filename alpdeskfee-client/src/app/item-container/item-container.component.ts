import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-item-container',
  templateUrl: './item-container.component.html',
  styleUrls: ['./item-container.component.scss']
})
export class ItemContainerComponent implements OnInit {

  currentHeight = 35;

  objLabels: any;
  pageEdit: boolean = false;
  pageId: number = 0;

  elementParent!: HTMLElement;
  jsonDataParent: any;
  offsetTopParent: string = '0px';
  
  elementElement!: HTMLElement;
  jsonDataElement: any;
  offsetTopElement: string = '0px';
  

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

  changeParent(jsonData: any, element: any, scrollTop: number): void {
    this.jsonDataParent = jsonData;
    this.elementParent = element;  
    if (this.elementParent !== null) {
      this.offsetTopParent = (element.getBoundingClientRect().top + scrollTop - this.currentHeight) + 'px';
    } else {
      this.offsetTopParent = '0px';
    }  
  }

  changeElement(jsonData: any, element: any, scrollTop: number): void {
    this.jsonDataElement = jsonData;
    this.elementElement = element;
    if (this.elementElement !== null) {
      this.offsetTopElement = (element.getBoundingClientRect().top + scrollTop - this.currentHeight) + 'px';
    } else {
      this.offsetTopElement = '0px';
    }
  }

}
