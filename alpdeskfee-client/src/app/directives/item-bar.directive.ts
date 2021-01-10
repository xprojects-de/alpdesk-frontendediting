import { AfterViewInit, Directive, ElementRef, Input, OnChanges, OnDestroy } from '@angular/core';
import { fromEvent, Subscription } from 'rxjs';
import { takeUntil } from 'rxjs/operators';

@Directive({
  selector: '[appItemBar]'
})
export class ItemBarDirective implements AfterViewInit, OnDestroy, OnChanges {

  @Input() frameContentDocument!: HTMLDocument;
  @Input() selectedElement!: HTMLElement;

  private element: HTMLElement;
  private changed: boolean = false;

  private subscriptions: Subscription[] = [];

  constructor(el: ElementRef) {
    this.element = el.nativeElement;
  }

  ngAfterViewInit() {
    this.draggableElement();
  }

  ngOnChanges() {
    this.changed = true;
  }

  ngOnDestroy(): void {
    this.subscriptions.forEach((s) => {
      if (s !== null && s !== undefined) {
        s.unsubscribe()
      }
    });
  }

  private getTransformMatrix(value: string) {

    if (value !== null && value !== undefined && value !== '') {
      const values = value.split(/\w+\(|\);?/);
      const transform = values[1].split(/,\s?/g).map((numStr: string) => parseInt(numStr));
      return { x: transform[0], y: transform[1], z: transform[2] };
    }
    return { x: 0, y: 0, z: 0 };
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

        let transformMatrix = this.getTransformMatrix(this.element.style.transform);
        if (transformMatrix.x === 0) {
          currentX = 0;
        }
        if (transformMatrix.y === 0) {
          currentY = 0;
        }

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
