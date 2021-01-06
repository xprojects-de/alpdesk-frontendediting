import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-item-container',
  templateUrl: './item-container.component.html',
  styleUrls: ['./item-container.component.scss']
})
export class ItemContainerComponent implements OnInit {

  objLabels: any;

  elementParent!: HTMLElement;
  jsonDataParent: any;
  
  elementElement!: HTMLElement;
  jsonDataElement: any;

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

  changeParent(jsonData: any, element: any): void {
    this.jsonDataParent = jsonData;
    this.elementParent = element;    
  }

  changeElement(jsonData: any, element: any): void {
    this.jsonDataElement = jsonData;
    this.elementElement = element;
  }

}
