import { Component, Input } from '@angular/core';
import { BaseItemComponent } from '../base-item/base-item.component';

@Component({
  selector: 'app-item-desc',
  templateUrl: './item-desc.component.html',
  styleUrls: ['./item-desc.component.scss']
})
export class ItemDescComponent extends BaseItemComponent {

  @Input() label: string = '';

}
