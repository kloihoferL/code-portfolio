import { Component, OnInit } from '@angular/core';
import {Router} from '@angular/router';

@Component({
  selector: 'app-logout',
  imports: [],
  templateUrl: './logout.component.html',
  styleUrl: './logout.component.css'
})

export class LogoutComponent implements OnInit {
  constructor(private router: Router) {}

  ngOnInit(): void {

    setTimeout(() => {
      this.router.navigate(['/login']);
    }, 3000);
  }
}
