import {Component, OnInit} from '@angular/core';
import {RouterLink} from '@angular/router';
import {NgClass} from '@angular/common';
import {ConfigSignalService} from '../services/config-signal.service';
import {FormsModule} from '@angular/forms';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-feedback',
  imports: [
    RouterLink,
    NgClass,
    FormsModule, CommonModule
  ],
  templateUrl: './feedback.component.html',
  styleUrl: './feedback.component.css'
})
export class FeedbackComponent implements  OnInit {
  constructor(private config: ConfigSignalService) {}

  selectedCategory = '';
  selectedCount = 0;
  ngOnInit() {
    this.selectedCategory = this.config.selectedCategory();
    this.selectedCount = this.config.counterQuestion();
  }


  interview = {
    title: 'Bewerbungsgespräch 1',
    date: '01.04.2025',
    duration: '21 Minuten',
    category: 'Jobspezifische Fragen',
    questions: [
      {
        id: 1,
        title: 'Frage 1',
        question: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut',
        answer: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut',
        isOpen: false
      },
      {
        id: 2,
        title: 'Frage 2',
        question: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut',
        answer: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut',
        isOpen: false
      },
      {
        id: 3,
        title: 'Frage 3',
        question: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut',
        answer: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut',
        isOpen: false
      }
    ],
    feedback: {
      general: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut',
      goodPoints: [
        { icon: '👍', text: 'Das hast du gut gemacht', description: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut' }
      ],
      improvementPoints: [
        { icon: '💡', text: 'Das kannst du noch verbessern', description: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut' }
      ],
      additionalTips: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut'
    }
  };

  toggleQuestion(id: number) {
    this.interview.questions = this.interview.questions.map((q) =>
      q.id === id ? { ...q, isOpen: !q.isOpen } : q
    );
  }
}

