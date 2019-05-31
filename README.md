# FES - Flurex Encryption System

**FES** - перестановочный алгоритм шифрования данных для **PHP** 5+. Специфику, криптостойкость, описания и всё-всё-всё смотреть в группе вк по хештегу [#enfesto_fes](https://vk.com/feed?c%5Bq%5D=%23enfesto_fes&section=search)

### Пример использования

```php
<?php

$text = 'Hello, World!';
$key  = 'My Super Key';

echo $encode = Flurex::encode ($text, $key);
echo PHP_EOL;
echo Flurex::decode ($encode, $key);
```

Автор: [Подвирный Никита](https://vk.com/technomindlp). Специально для [Enfesto Studio Group](http://vk.com/hphp_convertation)