# INPUT FILE #
```
> vim record/1.log
265宜蘭縣羅東鎮公正路60號
970花蓮縣花蓮市中正路590號
...
..
.
```

# OUTPUT #
```
> cat config.php
<?php
$google_api_key = "xxxxxx";

> php google_lookup.php
```

# Lookup at OSX #
```
> cat google_query/`echo -ne "265宜蘭縣羅東鎮公正路60號" | md5`
{"lat":24.6780778,"lng":121.7730342}
> cat google_query/`echo -ne "970花蓮縣花蓮市中正路590號" | md5`
{"lat":23.9789525,"lng":121.6101892}
```
