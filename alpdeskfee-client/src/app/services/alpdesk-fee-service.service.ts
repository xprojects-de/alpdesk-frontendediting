import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class AlpdeskFeeServiceService {

  ALPDESK_EVENTNAME = 'alpdesk_frontendediting_event';

  constructor() { }

  dispatchEvent(params: any) {

    document.dispatchEvent(new CustomEvent(this.ALPDESK_EVENTNAME, {
      detail: params
    }));

  }

}
