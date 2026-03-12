# 04 - Auth Pages Specification

## Scope

Ovaj dokument razrađuje `T-201: Auth Pages`.

## Files To Create

- `resources/views/auth/login.blade.php`
- `resources/views/auth/register.blade.php`
- `resources/views/auth/forgot-password.blade.php`
- `resources/views/auth/reset-password.blade.php`
- `resources/views/auth/verify-email.blade.php`
- `resources/views/auth/two-factor-challenge.blade.php`

Sve auth stranice koriste `layouts.guest`.

## Shared Rules

- Copy i validation poruke su na BHS jeziku
- Form width maksimalno `max-w-md`
- Jasni linkovi između login, register i password reset flow-a
- Svi inputi koriste shared komponentu iz `T-401`
- Error summary ide iznad forme, field error ide ispod inputa

## Login

### Required Elements
- Email
- Lozinka
- `Zapamti me` checkbox
- Primary CTA: `Prijavi se`
- Secondary action: `Prijava putem Google naloga`
- Link: `Zaboravili ste lozinku?`

### Behavior
- Nakon uspješne prijave redirect:
  - buyer -> `/dashboard`
  - seller / verified_seller -> `/seller/dashboard`
  - moderator / super_admin -> `/admin`

## Register

### Required Elements
- Ime i prezime
- Email
- Lozinka
- Potvrda lozinke
- Izbor tipa računa:
  - `Kupac`
  - `Prodavac`
- Checkbox za prihvatanje uslova korištenja

### UX Notes
- Tip računa mora biti vizualno jasan kao segmented izbor ili card picker
- Ako je odabran `Prodavac`, prikazati kratku napomenu o KYC i wallet setup-u nakon registracije

## Forgot / Reset Password

- Forgot page sadrži samo email i CTA `Pošalji link za reset`
- Reset page sadrži email, novu lozinku i potvrdu
- Success i status poruke moraju biti jasno odvojene od error stanja

## Verify Email

- Objašnjenje da je potvrda emaila obavezna prije punog korištenja platforme
- CTA za ponovno slanje verifikacijskog linka
- Sekundarni link za logout

## Two-Factor Challenge

- Jednokratni kod
- Alternativa za recovery code
- Kratka pomoćna poruka o authenticator aplikaciji

## Accessibility Checklist

- Svi inputi imaju povezane `label` elemente
- Password polja mogu imati show/hide toggle sa `aria-pressed`
- Role picker je dostupan tastaturom

## Test Checklist

- Ispravni auth linkovi i named routes postoje
- Redirect logika zavisi od role
- Validation greške se prikazuju na pravom polju
- Verify email i MFA stranice rade bez layout lomljenja na mobilnom
