import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UtilsContainerComponent } from './utils-container.component';

describe('UtilsContainerComponent', () => {
  let component: UtilsContainerComponent;
  let fixture: ComponentFixture<UtilsContainerComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ UtilsContainerComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(UtilsContainerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
