import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs/internal/Observable';

@Injectable({
  providedIn: 'root'
})
export class AlpdeskFeeServiceService {

  ALPDESK_EVENTNAME = 'alpdesk_frontendediting_event';

  constructor(private http: HttpClient) { }

  dispatchEvent(params: any) {

    if (params.preRequestGet !== null && params.preRequestGet !== undefined && params.preRequestGet === true) {
      this.callGetRequest(params.url).subscribe(
        (data) => {
          document.dispatchEvent(new CustomEvent(this.ALPDESK_EVENTNAME, {
            detail: params
          }));
        },
        (error) => {
          document.dispatchEvent(new CustomEvent(this.ALPDESK_EVENTNAME, {
            detail: params
          }));
        }
      );
    } else if (params.preRequestPost !== null && params.preRequestPost !== undefined && params.preRequestPost === true) {
      this.callPostRequest(params.url, params).subscribe(
        (data) => {
          document.dispatchEvent(new CustomEvent(this.ALPDESK_EVENTNAME, {
            detail: params
          }));
        },
        (error) => {
          console.log(error);
        }
      );
    } else {
      document.dispatchEvent(new CustomEvent(this.ALPDESK_EVENTNAME, {
        detail: params
      }));
    }
  }

  callPostRequest(url: string, data: any): Observable<any> {
    const options = {
      headers: new HttpHeaders().append('Content-Type', 'application/json')
    };
    const body = { data: JSON.stringify(data), rt: data.rt };
    //console.log(body);
    return this.http.post(url, body, options);
  }

  callGetRequest(url: string): Observable<any> {
    return this.http.get(url);
  }

}
