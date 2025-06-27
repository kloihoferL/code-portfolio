import { Component } from '@angular/core';
import {Router, RouterLink} from '@angular/router';
import {FormsModule} from '@angular/forms';
import {NgIf} from '@angular/common';

@Component({
  selector: 'app-login',
  imports: [
    RouterLink,
    FormsModule,
    NgIf
  ],
  templateUrl: './login.component.html',
  styles: ``
})
export class LoginComponent {
  email: string = '';
  password: string = '';
  error: string = '';

  // Beispiel statischer Benutzer
  private readonly staticEmail = 'test@test.com';
  private readonly staticPassword = 'test';

  constructor(private router: Router) {}

  login() {
    if (this.email === this.staticEmail && this.password === this.staticPassword) {
      this.router.navigate(['/home']);
    } else {
      this.error = 'E-Mail oder Passwort ist falsch oder fehlerhaft.';
    }
  }
}
