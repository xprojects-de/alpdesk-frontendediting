import { AfterViewInit, Directive, ElementRef, Input, OnDestroy } from '@angular/core';
import { fromEvent, Subscription } from 'rxjs';
import { takeUntil } from 'rxjs/operators';

@Directive({
  selector: '[appItemBar]'
})
export class ItemBarDirective implements AfterViewInit, OnDestroy {

  @Input() frameContentDocument!: HTMLDocument;
  @Input() selectedElement!: HTMLElement;
  @Input() sticky: boolean = false;

  private element: HTMLElement;
  private isSticky: boolean = false;

  private subscriptions: Subscription[] = [];

  constructor(el: ElementRef) {
    this.element = el.nativeElement;
  }

  ngAfterViewInit() {
    //this.stickyBarItem();
    this.draggableElement();
  }

  ngOnDestroy(): void {
    this.subscriptions.forEach((s) => {
      if (s !== null && s !== undefined) {
        s.unsubscribe()
      }
    });
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

  draggableElement() {

    let moveItem = this.element.querySelector('app-item-move') as HTMLElement;

    if (moveItem !== null && moveItem !== undefined) {

      const dragStart$ = fromEvent<MouseEvent>(moveItem, "mousedown");
      const dragEnd$ = fromEvent<MouseEvent>(this.frameContentDocument, "mouseup");
      const drag$ = fromEvent<MouseEvent>(this.frameContentDocument, "mousemove").pipe(takeUntil(dragEnd$));

      let initialX: number, initialY: number, currentX = 0, currentY = 0;
      let dragSub!: Subscription;

      const dragStartSub = dragStart$.subscribe((event: MouseEvent) => {
        initialX = event.clientX - currentX;
        initialY = event.clientY - currentY;

        dragSub = drag$.subscribe((event: MouseEvent) => {
          event.preventDefault();
          currentX = event.clientX - initialX;
          currentY = event.clientY - initialY;
          this.element.style.transform = "translate3d(" + currentX + "px, " + currentY + "px, 0)";
        });
      });

      const dragEndSub = dragEnd$.subscribe(() => {
        initialX = currentX;
        initialY = currentY;
        if (dragSub) {
          dragSub.unsubscribe();
        }
      });

      this.subscriptions.push.apply(this.subscriptions, [dragStartSub, dragSub, dragEndSub,]);
    }
  }
}
