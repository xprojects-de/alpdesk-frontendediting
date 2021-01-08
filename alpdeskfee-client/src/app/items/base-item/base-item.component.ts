import { Component, Input } from '@angular/core';
import { AlpdeskFeeServiceService } from 'src/app/services/alpdesk-fee-service.service';

@Component({
  selector: 'app-base-item',
  templateUrl: './base-item.component.html',
  styleUrls: ['./base-item.component.scss']
})
export class BaseItemComponent {

  constructor(private _alpdeskFeeService: AlpdeskFeeServiceService) { }

  dispatchEvent(params: any) {    
    this._alpdeskFeeService.dispatchEvent(params);
  }

}
