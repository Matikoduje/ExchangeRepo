ExchangeProject
===============

Ważne:

Apka jest pod linkiem na serwerze Heroku. Z tego względu występuje pewna niedogodność. Po każdym pół
godziny w czasie w którym nie ma żadnego żądania na aplikacje zostaje ona uśpiona. I przy pierwszym wejściu po tym czasie 
aplikacja dopiero się budzi. I w tym właśnie momencie występuje bug. Objawia się na jeden z dwóch sposobów:

1) Przeglądarka wyświetla informacje o braku możliwości połączenia. Wystarczy odświeżyć i już się do apki podłącza i działa jak należy.
2) Przeglądarka wykrywa apkę za pierwszym razem ale w momencie zalogowania nie działa komunikacja z back-endem apki i nie działa AJAX. Należy się wylogować i zalogować raz jeszcze. Wtedy działa jak należy.

Do następnego uśpienia apka działa poprawnie. A później znów trzeba ją budzić pierwszym wywołaniem.
Jak widać korzystanie z darmowej chmury ma swoje wady :)


