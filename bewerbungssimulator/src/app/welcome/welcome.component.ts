import { Component } from '@angular/core';
import {RouterLink} from '@angular/router';

@Component({
  selector: 'app-welcome',
  imports: [
    RouterLink
  ],
  templateUrl: './welcome.component.html',
  styleUrl: './welcome.component.css'
})
export class WelcomeComponent {

  isModalOpen = false; // Kontrolliert, ob das Modal geöffnet ist
  videoUrl: string = ''; // Die URL des Videos, die in das iframe eingebettet wird

  // Diese Methode wird aufgerufen, wenn der Button gedrückt wird
  startVideo(): void {
    this.isModalOpen = true;
    // Beispiel-YouTube-URL, ersetze sie mit deiner eigenen URL
    this.videoUrl = 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1'; // Autoplay hinzufügen
  }

  // Diese Methode schließt das Modal
  closeModal(): void {
    this.isModalOpen = false;
    this.videoUrl = ''; // Stoppt das Video, indem die URL zurückgesetzt wird
  }
}
