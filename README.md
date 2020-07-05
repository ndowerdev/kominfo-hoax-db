# KOMINFO Hoax Database Grabber
 PHP Grabber for Kominfo Hoax Database using Curl and [Didom](https://github.com/Imangazaliev/DiDOM)


## Usage
Require autoload.php
```
require "autoload.php";
```

Get data per page, default page 0, type :  json / array
```
$data = KominfoHoaxDB::getData($page,$type);

```

Get details by slug, type :  json / array

```
$data = KominfoHoaxDB::getDetails($slug,$type);

```


## License

kominfo-hoax-db is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).