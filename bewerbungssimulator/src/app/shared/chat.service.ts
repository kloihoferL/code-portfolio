import {HttpClient} from '@angular/common/http';
import {Observable} from 'rxjs';
import {Injectable} from '@angular/core';

@Injectable({
  providedIn: 'root',
})
export class ChatService {
  private baseUrl = 'http://localhost:3001';

  private currentIndex = 0;

  constructor(private http: HttpClient) {}

  initChat(category: string, count: number): Observable<{ response: string }> {
    this.currentIndex = 0;
    return this.http.post<{ response: string }>(`${this.baseUrl}/initChat`, {
      job: category,
      numberOfQuestions: count,
    });
  }

  /* old
  sendAnswer(answer: string): Observable<{ frage: string }> {
    return this.http.post<{ frage: string }>(`${this.baseUrl}/chat`,{ message: answer});
  }*/


  sendAnswer(answer: string): Observable<{ response: string }> {
    return this.http.post<{ response: string }>(`${this.baseUrl}/chat`,{ message: answer});
  }
}
