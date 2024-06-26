# OpenCart

## Обзор

[![Минимальная версия PHP](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg?style=flat-square)](https://php.net/)
[![Релизы на GitHub](https://img.shields.io/github/v/release/opencart/opencart)](https://github.com/opencart/opencart)

OpenCart - это бесплатная платформа электронной коммерции с открытым исходным кодом для онлайн-продавцов. OpenCart обеспечивает профессиональную и надежную основу для создания успешного интернет-магазина.

## Как установить

Прочтите [инструкции по установке](INSTALL.md), включенную в репозиторий или скачайте файл.

## Как обновиться с предыдущих версий

Пожалуйста, прочтите [инструкции по обновлению](UPGRADE.md), включенную в репозиторий или скачайте файл.

## Сообщение об ошибке

Прочтите инструкции ниже, прежде чем создавать отчет об ошибке.

1. Выполните поиск на [форуме OpenCart](https://forum.opencart.com/viewforum.php?f=201), спросите сообщество, видели ли они ошибку или знают, как ее исправить.
2. Проверьте все открытые и закрытые проблемы на [трекере ошибок GitHub](https://github.com/opencart/opencart/issues).
3. Если Ваша ошибка связана с основным кодом OpenCart, пожалуйста, создайте отчет об ошибке на GitHub.
4. ПРОЧИТАЙТЕ [журнал изменений для основной ветки](https://github.com/opencart/opencart/blob/master/CHANGELOG.md)
5. Используйте [Google](https://www.google.com) для поиска своей проблемы.
6. Убедитесь, что Ваша ошибка/проблема не связана с Вашей средой хостинга.

Если Вы не уверены в своей проблеме, всегда лучше спросить сообщество в нашей [ветке форума об ошибках](https://forum.opencart.com/viewforum.php?f=201)

**Важно!**

-   Если Ваш отчет об ошибке не связан с основным кодом (например, сторонним модулем или конфигурацией Вашего сервера), проблема будет закрыта без причины. Вы должны связаться с разработчиком расширения, использовать форум или найти коммерческого партнера для решения проблемы с кодом третьей стороны.
-   Если Вы хотите сообщить о серьезной ошибке безопасности, пожалуйста, оставьте сообщение модератору/администратору OpenCart на форуме. Пожалуйста, не сообщайте о концепциях/идеях/недоказанных недостатках безопасности - все отчеты о безопасности воспринимаются серьезно, но Вы должны включать ТОЧНЫЕ подробные шаги для их воспроизведения. Пожалуйста, НЕ размещайте уязвимости безопасности в публичных местах.

## Как внести свой вклад

Форкните репозиторий, отредактируйте и [отправьте запрос на слияние](https://docs.opencart.name/ru-ru/developer/creating-pull-request/).

Пожалуйста, будьте предельно ясны в своих сообщениях о фиксации и запросе на перенос, пустые сообщения запроса на перенос могут быть отклонены без причины.

Ваши стандарты кода должны соответствовать [стандартам кодирования OpenCart](https://docs.opencart.name/ru-ru/developer/codding_standards/). Мы используем автоматический сканер кода для проверки большинства основных ошибок - если тест не пройдет, Ваш запрос на слияние будет отклонен.

## Управление версиями

Версия разбита на 4 пункта, например 1.2.3.4. Мы используем MAJOR.MINOR.FEATURE.PATCH для описания номеров версий.

MAJOR встречается очень редко, он будет рассматриваться только в том случае, если исходный текст был эффективно переписан или был желателен полный разрыв по другим причинам. Это приращение, вероятно, сломает большинство сторонних модулей.

MINOR - это когда есть значительные изменения, которые влияют на основные структуры. Это приращение, вероятно, приведет к поломке некоторых сторонних модулей.

Версия FEATURE - это когда добавляются новые расширения или функции (например, платежный шлюз, модуль доставки и т.д.). Обновление версии функции сопряжено с низким риском поломки сторонних модулей.

Версия PATCH - это когда исправление добавлено, следует считать безопасным обновлять версии исправлений, например, с 1.2.3.4 до 1.2.3.5.

## Релизы

OpenCart объявит разработчикам за 1 неделю до публичного выпуска версий FEATURE, это позволит протестировать их собственные модули на совместимость. Для более крупных выпусков (тех, которые содержат много основных изменений, функций и исправлений) будет рассмотрен расширенный период после объявления кандидата на выпуск (RC). Версии исправлений (которые считаются безопасными для обновления) могут иметь значительно сокращенный период выпуска для разработчиков.

Основная ветвь всегда будет содержать постфикс "\_rc" следующей предполагаемой версии. Следующая версия "\_rc" может измениться в любое время.

Исходный код выпуска разработчика не изменится после добавления тега.

Если в объявленном выпуске разработчика обнаруживается значительная ошибка (например, не работает основная функция), выпуск будет удален. На замену будет выпущена версия патча, в зависимости от серьезности патча может быть объявлен расширенный период тестирования. Если версия выпуска для разработчиков никогда не была опубликована, то предыдущий тег версии исправления будет удален.

Чтобы получать уведомления разработчиков об информации о выпуске, подпишитесь на рассылку новостей на [веб-сайте OpenCart](https://www.opencart.com), расположенном в нижнем колонтитуле. Затем выберите вариант новостей для разработчиков.

## Лицензия

[Стандартная общественная лицензия GNU версии 3 (GPLv3)](https://github.com/opencart/opencart/blob/master/license.txt)

## Ссылки РУ

-   [Форум русскоговорящего сообщества OpenCart](https://forum.opencart.name/)
-   [Домашняя страница русскоязычного сообщества OpenCart](https://opencart.name/)
-   [Перевод официальной документации opencart](http://docs.opencart.name/)

## Ссылки EN

-   [Домашняя страница OpenCart](https://www.opencart.com/)
-   [Форумы OpenCart](https://forum.opencart.com/)
-   [Блог OpenCart](https://www.opencart.com/index.php?route=feature/blog)
-   [Как оформлять документацию](http://docs.opencart.name/ru-ru/introduction/)
-   [Информационная рассылка](https://newsletter.opencart.com/h/r/B660EBBE4980C85C)
-   [Обсуждения](https://github.com/opencart/opencart/discussions)
