# OCN Slider

Отдельный модуль слайдера для OpenCart 4. В тему не входит: баннер OpenCart на карусели Bootstrap остаётся слайдером по умолчанию, пока этот модуль сами не поставите на макет.

Каталог пакета: `ocn_slider.ocmod`. Код расширения: `ocn_slider`. Модуль в админке: Extensions → Modules → OCN Slider.

## Жёсткие ссылки

Контейнер монтирует только `upload`. Живая копия — жёсткие ссылки в `upload/extension/ocn_slider`. Родительский `.gitignore` эту копию игнорирует. Правьте файлы в `ocn/slider/ocn_slider.ocmod`.

Новый файл сам ссылкой не становится. Из каталога `ocn/slider` в PowerShell:

```powershell
$src = (Resolve-Path '.\ocn_slider.ocmod').Path
$dst = (Resolve-Path '..\..\upload\extension').Path + '\ocn_slider'
Get-ChildItem $src -Recurse -File | ForEach-Object {
  $rel = $_.FullName.Substring($src.Length).TrimStart('\')
  $target = Join-Path $dst $rel
  $dir = Split-Path $target
  if (!(Test-Path $dir)) { New-Item -ItemType Directory -Path $dir -Force | Out-Null }
  if (Test-Path $target) { Remove-Item -LiteralPath $target -Force }
  New-Item -ItemType HardLink -Path $target -Target $_.FullName | Out-Null
}
```

Если каталог `upload/extension/ocn_slider` уже есть, установщик напишет, что путь занят. Повторно пакет через загрузку не ставьте: запись уже лежит в `oc_extension_install`, модуль — в `oc_extension`.

## Сборка zip

Архив для установщика появляется рядом с пакетом: `dist/ocn_slider.ocmod.zip`. В корне архива `install.json`, без обёртки `upload/`.

```text
php ocn/slider/build.php
php build.php
```

Вручную, если PHP нет: зайти в `ocn_slider.ocmod` и упаковать его содержимое так, чтобы `install.json` лежал в корне zip, а не во вложенной папке.
