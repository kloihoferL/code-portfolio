import { Injectable, signal } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class ConfigSignalService {
  private readonly _selectedCategory = signal<string>('');
  private readonly _counterQuestion = signal<number>(3);
  private readonly _jobTitle = signal<string>('');

  selectedCategory = () => this._selectedCategory();
  counterQuestion = () => this._counterQuestion();
  jobTitle = () => this._jobTitle();

  // Setter
  setCategory(category: string) {
    this._selectedCategory.set(category);
  }

  setCounter(count: number) {
    this._counterQuestion.set(count);
  }

  setJobTitle(title: string) {
    this._jobTitle.set(title);
  }

  incrementCounter() {
    this._counterQuestion.update(c => Math.min(c + 1, 10));
  }

  decrementCounter() {
    this._counterQuestion.update(c => Math.max(3, c - 1));
  }
}
