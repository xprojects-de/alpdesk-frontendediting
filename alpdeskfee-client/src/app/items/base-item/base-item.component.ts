import { Component } from '@angular/core';
import { AlpdeskFeeServiceService } from 'src/app/services/alpdesk-fee-service.service';

@Component({
  selector: 'app-base-item',
  templateUrl: './base-item.component.html',
  styleUrls: ['./base-item.component.scss']
})
export class BaseItemComponent {

  constructor() { }

  dispatchEvent(params: any) {
    document.dispatchEvent(new CustomEvent(AlpdeskFeeServiceService.ALPDESK_EVENTNAME, {
      detail: params
    }));
  }

}
