import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import {Router, RouterLink} from '@angular/router';

@Component({
  selector: 'app-registration',
  standalone: true,
  imports: [FormsModule, RouterLink],
  templateUrl: './registration.component.html',
  styles: ``
})
export class RegistrationComponent {
  firstName = '';
  lastName = '';
  birthDate = '';
  email = '';
  password = '';
  confirmPassword = '';
  error = '';

  constructor(private router: Router) {}

  register() {
    if (!this.firstName || !this.lastName || !this.birthDate || !this.email || !this.password || !this.confirmPassword) {
      this.error = 'Bitte füllen Sie alle Felder aus.';
      return;
    }

    if (this.password !== this.confirmPassword) {
      this.error = 'Passwörter stimmen nicht überein.';
      return;
    }

    // Erfolgreich
    this.error = '';
    this.router.navigate(['/home']);
  }
}
