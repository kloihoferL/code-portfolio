import { Component } from '@angular/core';
import {RouterLink} from '@angular/router';

@Component({
  selector: 'app-general-feedback',
  imports: [
    RouterLink
  ],
  templateUrl: './general-feedback.component.html',
  styles: ``
})
export class GeneralFeedbackComponent {
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
