import { AfterViewInit, Directive, ElementRef, Input } from '@angular/core';

@Directive({
  selector: '[appItemBar]'
})
export class ItemBarDirective implements AfterViewInit {

  @Input() frameContentDocument!: HTMLDocument;
  @Input() selectedElement!: HTMLElement;

  private element: HTMLElement;
  private isSticky: boolean = false;

  constructor(el: ElementRef) {
    this.element = el.nativeElement;
  }

  ngAfterViewInit() {
    //this.stickyBarItem();
  }

  private stickyBarItem() {
    if (this.frameContentDocument !== null && this.frameContentDocument !== undefined) {
      this.frameContentDocument.addEventListener('scroll', () => {
        if (this.selectedElement !== null && this.selectedElement !== undefined && this.element !== null && this.element !== undefined) {
          const selectedElementTop = this.selectedElement.getBoundingClientRect().top - this.element.offsetHeight;
          if (selectedElementTop <= 0 && this.isSticky === false) {
            this.element.style.position = 'fixed';
            this.element.style.top = '0px';
            this.isSticky = true;
          } else if (selectedElementTop > 0 && this.isSticky === true) {
            this.element.style.position = 'absolute';
            this.element.style.top = (selectedElementTop + this.frameContentDocument.documentElement.scrollTop) + 'px';
            this.isSticky = false;
          }
        }

      });
    }
  }

}
