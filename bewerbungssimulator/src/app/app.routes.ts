import { Routes } from '@angular/router';
import {AppComponent} from './app.component';
import {TippsDetailComponent} from './tipps-detail/tipps-detail.component';
import {WelcomeComponent} from './welcome/welcome.component';
import {Step12Component} from './step1-2/step1-2.component';
import {LogoutComponent} from './logout/logout.component';
import {FeedbackComponent} from './feedback/feedback.component';
import {Step3Component} from './step3/step3.component';
import { SettingsComponent } from './settings/settings.component';
import {RegistrationComponent} from './registration/registration.component';
import {LoginComponent} from './login/login.component';
import {GeneralFeedbackComponent} from './general-feedback/general-feedback.component';

export const routes: Routes = [
  {path: '', redirectTo: 'login', pathMatch: 'full'},
  {path: 'login', component: LoginComponent},
  {path: 'registration', component: RegistrationComponent},
  {path: 'home', component: WelcomeComponent},
  {path: 'tipps/:title', component: TippsDetailComponent},
  { path: 'simulator', component: Step12Component },
  { path: 'logout', component: LogoutComponent },
  { path: 'feedback', component: FeedbackComponent },
  { path: 'chat', component: Step3Component },
  { path: 'settings', component: SettingsComponent },
  { path: 'general-feedback', component: GeneralFeedbackComponent },
];
