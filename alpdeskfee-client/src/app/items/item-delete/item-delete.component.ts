import { Component, Input } from '@angular/core';
import { BaseItemComponent } from '../base-item/base-item.component';

@Component({
  selector: 'app-item-delete',
  templateUrl: './item-delete.component.html',
  styleUrls: ['./item-delete.component.scss']
})
export class ItemDeleteComponent extends BaseItemComponent {

  @Input() title: string = '';
  @Input() action: string = '';
  @Input() targetType: string = '';
  @Input() do: string = '';
  @Input() id: string = '';

  click() {
    this.dispatchEvent({
      action: this.action,
      targetType: this.targetType,
      do: this.do,
      id: this.id,
      /*targetPageId: this.pageid*/
    });
  }

}
