import {Component, signal} from '@angular/core';
import { RouterLink } from '@angular/router';
import { ConfigSignalService } from '../services/config-signal.service';
import {FormsModule} from '@angular/forms';

@Component({
  selector: 'app-step1-2',
  standalone: true,
  imports: [RouterLink, FormsModule],
  templateUrl: './step1-2.component.html',
  styleUrls: ['./step1-2.component.css']
})

export class Step12Component {
  /*jobTitle = signal<string>('');*/
  constructor(public config: ConfigSignalService) {}

  // Getter statt Zuweisung im Rumpf
  get selectedCategory() {
    return this.config.selectedCategory();
  }

  updateJobTitle(value: string) {
    this.config.setJobTitle(value); // zum Zwischenspeichern
  }

  get counterQuestion() {
    return this.config.counterQuestion();
  }

  counterQuestionAdd() {
    if (this.counterQuestion < 10){
      this.config.incrementCounter();
    }

  }

  counterQuestionMinus() {
    if (this.counterQuestion > 3){
      this.config.decrementCounter();
    }

  }

  setCategory(category: string) {
    this.config.setCategory(category);
  }

  getCounterInConsole() {
    const jobToSend = this.config.selectedCategory() === 'jobspezifische fragen'
      ? this.config.jobTitle()
      : this.config.selectedCategory();

    console.log("Gesendete Kategorie/Jobtitel:", jobToSend);
    console.log("Fragenanzahl ist:", this.config.counterQuestion());
  }


  setJobAsCategory(job: string) {
    this.config.setCategory(job); // gleich auf selectedCategory setzen
  }






  onFileSelected(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (file) {
      console.log("Datei ausgewählt:", file.name);
    }
  }
}
