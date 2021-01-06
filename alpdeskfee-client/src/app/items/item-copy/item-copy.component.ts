import { Component, Input } from '@angular/core';
import { BaseItemComponent } from '../base-item/base-item.component';

@Component({
  selector: 'app-item-copy',
  templateUrl: './item-copy.component.html',
  styleUrls: ['./item-copy.component.scss']
})
export class ItemCopyComponent extends BaseItemComponent {

  @Input() title: string = '';
  @Input() action: string = '';
  @Input() targetType: string = '';
  @Input() do: string = '';
  @Input() id: string = '';
  @Input() pid: string = '';

  click() {
    this.dispatchEvent({
      action: this.action,
      targetType: this.targetType,
      do: this.do,
      id: this.id,
      pid: this.pid,
      /*targetPageId: this.pageid*/
    });
  }

}
