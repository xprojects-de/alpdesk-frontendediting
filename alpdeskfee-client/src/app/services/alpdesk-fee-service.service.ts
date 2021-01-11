import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs/internal/Observable';
import { catchError, retry} from 'rxjs/operators';
import { of } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AlpdeskFeeServiceService {

  public static ALPDESK_EVENTNAME = 'alpdesk_frontendediting_event';

  constructor(private _httpClient: HttpClient) { }

  dispatchEvent(params: any) {
    document.dispatchEvent(new CustomEvent(AlpdeskFeeServiceService.ALPDESK_EVENTNAME, {
      detail: params
    }));
  }

  private handleError<T>(operation = 'operation', result?: T) {
    return (error: any): Observable<T> => {
      console.log(error);
      return of(result as T);
    };
  }

  callPostRequest(url: string, data: any): Observable<any> {
    const options = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json',
        'X-CSRFToken': data.rt
      })
    };
    const body = { data: JSON.stringify(data), rt: data.rt };
    //console.log(body);
    //console.log(url);

    return this._httpClient.post(url, body, options).pipe(
      retry(1), catchError(this.handleError<any[]>('call', undefined))
    );
  }

  callGetRequest(url: string): Observable<any> {
    //console.log(url);
    const httpHeaders: HttpHeaders = new HttpHeaders({
      'Content-Type': 'text/plain'
    });
    return this._httpClient.get(url, { headers: httpHeaders, observe: 'response', responseType: 'text' }).pipe(
      retry(1), catchError(this.handleError<any[]>('call', undefined))
    );
  }

}
