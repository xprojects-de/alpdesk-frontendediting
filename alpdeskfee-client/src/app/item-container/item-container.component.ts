import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { Constants } from '../classes/constants';

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
  transformParent: string =  'translate3d(0, 0, 0)';
  
  elementElement!: HTMLElement;
  jsonDataElement: any;
  offsetTopElement: string = '0px';
  offsetLeftElement: string = '0px';
  transformElement: string =  'translate3d(0, 0, 0)';
  

  TARGETTYPE_PAGE = Constants.TARGETTYPE_PAGE;
  TARGETTYPE_ARTICLE = Constants.TARGETTYPE_ARTICLE;
  TARGETTYPE_CE = Constants.TARGETTYPE_CE;
  TARGETTYPE_MOD = Constants.TARGETTYPE_MOD;

  ACTION_PARENT_EDIT = Constants.ACTION_PARENT_EDIT;
  ACTION_ELEMENT_EDIT = Constants.ACTION_ELEMENT_EDIT;
  ACTION_ELEMENT_VISIBILITY = Constants.ACTION_ELEMENT_VISIBILITY;
  ACTION_ELEMENT_DELETE = Constants.ACTION_ELEMENT_DELETE;
  ACTION_ELEMENT_SHOW = Constants.ACTION_ELEMENT_SHOW;
  ACTION_ELEMENT_NEW = Constants.ACTION_ELEMENT_NEW;
  ACTION_ELEMENT_COPY = Constants.ACTION_ELEMENT_COPY;

  constructor() { }

  ngOnInit(): void {
  }

  changeParent(jsonData: any, element: HTMLElement): void {
    this.jsonDataParent = jsonData;
    this.elementParent = element;
    if (this.elementParent !== null) {
      let top = element.getBoundingClientRect().top - this.currentHeight;
      this.offsetTopParent = (top + this.frameContentDocument.documentElement.scrollTop) + 'px';
      if (this.articleContainer !== null && this.articleContainer !== undefined) {
        this.articleContainer.nativeElement.style.transform = this.transformParent;
      }
    } else {
      this.offsetTopParent = '0px';
      if (this.articleContainer !== null && this.articleContainer !== undefined) {
        this.articleContainer.nativeElement.style.transform = this.transformParent;
      }
    }
  }

  changeElement(jsonData: any, element: HTMLElement): void {
    this.jsonDataElement = jsonData;
    this.elementElement = element;
    if (this.elementElement !== null) {
      let top = element.getBoundingClientRect().top - this.currentHeight;
      this.offsetTopElement = (top + this.frameContentDocument.documentElement.scrollTop) + 'px';
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
