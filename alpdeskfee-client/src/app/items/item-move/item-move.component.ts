import { AfterViewInit, Component, ElementRef, HostListener, Input, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { BaseItemComponent } from '../base-item/base-item.component';

@Component({
  selector: 'app-item-move',
  templateUrl: './item-move.component.html',
  styleUrls: ['./item-move.component.scss']
})
export class ItemMoveComponent extends BaseItemComponent implements OnInit, AfterViewInit, OnDestroy {

  @Input() containerElement!: any;

  @ViewChild('moveItem') moveItem!: ElementRef;

  ngOnInit() {

  }

  ngAfterViewInit() {
    if (this.containerElement !== null && this.containerElement !== undefined && this.moveItem !== null && this.moveItem !== undefined) {
      //this.draggableElement();
    }
  }

  ngOnDestroy() {
    //this.moveItem.nativeElement.removeEventListener('mousedown');
    //this.containerElement.removeEventListener('onmouseup');
    //this.containerElement.removeEventListener('onmousemove');
    console.log("Removed event listener");
  }

  draggableElement() {

    let initialX: number;
    let initialY: number;

    let element = this.moveItem.nativeElement;
    let containerElement = this.containerElement;

    let barContainer!: any;
    let currentElement!: any;

    // This element is unique and there is no check needed
    element.onmousedown = function (e: any) {
      e = e || window.event;
      initialX = e.clientX;
      initialY = e.clientY;
      if (e.target === this) {
        currentElement = this;
        barContainer = this.parentElement.parentElement;
      }
    };

    containerElement.onmouseup = function () {
      currentElement = null;
      barContainer = null;
    };

    containerElement.onmousemove = function (e: any) {
      if (currentElement !== null && currentElement !== undefined && barContainer !== null && barContainer !== undefined) {
        console.log(barContainer);
        e = e || window.event;
        e.preventDefault();
        let currentX = initialX - e.clientX;
        let currentY = initialY - e.clientY;
        initialX = e.clientX;
        initialY = e.clientY;

        let limit_bottom = containerElement.offsetHeight - currentY - barContainer.offsetHeight;
        let top = (barContainer.offsetTop - currentY);
        if (barContainer.offsetTop >= limit_bottom) {
          top = limit_bottom;
        } else if (top <= 0) {
          top = 0;
        }

        let limit_left = containerElement.offsetWidth - currentX - barContainer.offsetWidth;
        let left = (barContainer.offsetLeft - currentX);
        if (barContainer.offsetLeft >= limit_left) {
          left = limit_left;
        }
        // Bar is left not in the middle
        if (barContainer.classList.contains('alpdeskfee-utilscontainer-custommodule')) {
          if (left <= 0) {
            left = 0;
          }
        } else {
          if (left <= (barContainer.offsetWidth / 2)) {
            left = (barContainer.offsetWidth / 2);
          }
        }

        barContainer.style.top = top + "px";
        barContainer.style.left = left + "px";
        console.log(top);
        console.log(left);
      }
    };
  }
}
