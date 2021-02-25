# PHP implementation of the Web ARChive (WARC) archive format
Copyright 2019-2021 Philippe Paquet

---

### Description

The Web ARChive (WARC) archive format specifies a method for combining multiple digital resources into an aggregate archive file together with related information.

WARC has traditionally been used to store web crawls. [wget](https://www.gnu.org/software/wget/) for example can create WARC archives since version 1.14.

You can find specifications of the WARC format on the [BNF website](http://www.bnf.fr/):
* [WARC 1.1 / draft as of January 2017](http://bibnum.bnf.fr/WARC/WARC_ISO_28500_version1-1_latestdraft.pdf)
* [WARC 1.0 / draft as of November 2008](http://bibnum.bnf.fr/WARC/WARC_ISO_28500_version1_latestdraft.pdf)

For some history, check [Wikipedia](https://en.wikipedia.org/wiki/Web_ARChive)

This PHP implementation of WARC will allow you to read WARC archives. It support both uncompressed and compressed WARC archives. It returns records as arrays, already parsed.

---

### Usage

Include `warc.inc.php` which specify the `warc` class:

```php
require_once('include/warc.inc.php');
```

Create a warc object:

```php
$warc = new warc();
```

After creation, the warc object offer 4 simple functions:

```php
// Open the WARC file
// Return TRUE if the operation was sucessful
// Return FALSE if there was an error
$result = $warc->open($filepath);
```

```php
// Read a record from the WARC file
// Return the record as an array if the operation is sucessful
// Return FALSE if there was an error
$record = $warc->read();
```

```php
// Close the WARC file
// Return TRUE if the operation was sucessful
// Return FALSE if there was an error
$result = $warc->close();
```

```php
// Return the last error as a string
$error = $warc->error();
```

Records are returned as arrays, already parsed:

```
array(2) {
  ["header"]=>
  array(8) {
    ["Version"]=>
    string(8) "WARC/1.0"
    ["WARC-Type"]=>
    string(8) "warcinfo"
    ["Content-Type"]=>
    string(23) "application/warc-fields"
    ["WARC-Date"]=>
    string(20) "2021-02-24T04:15:29Z"
    ["WARC-Record-ID"]=>
    string(47) "<urn:uuid:9632A98C-DE17-4578-B5DE-0B1DACE417CE>"
    ["WARC-Filename"]=>
    string(15) "example.warc.gz"
    ["WARC-Block-Digest"]=>
    string(37) "sha1:IN3U3MJE2BBQJS4D6YOCHPOJDLUJQYWH"
    ["Content-Length"]=>
    string(3) "239"
  }
  ["content"]=>
  string(239) "software: Wget/1.21.1 (darwin20.2.0)
               format: WARC File Format 1.0
               conformsTo: http://bibnum.bnf.fr/WARC/WARC_ISO_28500_version1_latestdraft.pdf
               robots: classic
               wget-arguments: "http://example.com/" "--mirror" "--warc-file=example"
               "
}
```

You should note that the record content is returned as a raw string. Depending on your use case, you may have to parse it.

---

### Example

```php
require_once('include/warc.inc.php');

// Create the warc object
$warc = new warc();

// Open example.warc.gz
if (FALSE === $warc->open('example.warc.gz')) {
  echo $warc->error() . PHP_EOL;
  exit();
}

// Read records
while (FALSE !== ($record = $warc->read())) {

  // Check that the content of the record is of type application/http;msgtype=request
  if ('application/http;msgtype=request' == $record['header']['Content-Type']) {

    // Dump the content of the record (raw http request)
    var_dump($record['content']);
  }

  // Check that the content of the record is of type application/http;msgtype=request
  if ('application/http;msgtype=response' == $record['header']['Content-Type']) {

    // Dump the content of the record (raw http response)
    var_dump($record['content']);
  }
}

// Close example.warc.gz
if (FALSE === $warc->close()) {
  echo $warc->error() . PHP_EOL;
  exit();
}
```

This example will output the following:

```http
GET / HTTP/1.1
User-Agent: Wget/1.21.1
Accept: */*
Accept-Encoding: identity
Host: example.com
Connection: Keep-Alive


HTTP/1.1 200 OK
Age: 464271
Cache-Control: max-age=604800
Content-Type: text/html; charset=UTF-8
Date: Wed, 24 Feb 2021 04:15:29 GMT
Etag: "3147526947+ident"
Expires: Wed, 03 Mar 2021 04:15:29 GMT
Last-Modified: Thu, 17 Oct 2019 07:18:26 GMT
Server: ECS (oxr/8316)
Vary: Accept-Encoding
X-Cache: HIT
Content-Length: 1256

<!doctype html>
<html>
<head>
    <title>Example Domain</title>

    <meta charset="utf-8" />
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style type="text/css">
    body {
        background-color: #f0f0f2;
        margin: 0;
        padding: 0;
        font-family: -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
        
    }
    div {
        width: 600px;
        margin: 5em auto;
        padding: 2em;
        background-color: #fdfdff;
        border-radius: 0.5em;
        box-shadow: 2px 3px 7px 2px rgba(0,0,0,0.02);
    }
    a:link, a:visited {
        color: #38488f;
        text-decoration: none;
    }
    @media (max-width: 700px) {
        div {
            margin: 0 auto;
            width: auto;
        }
    }
    </style>    
</head>

<body>
<div>
    <h1>Example Domain</h1>
    <p>This domain is for use in illustrative examples in documents. You may use this
    domain in literature without prior coordination or asking for permission.</p>
    <p><a href="https://www.iana.org/domains/example">More information...</a></p>
</div>
</body>
</html>

```

---

### Contributing

Bug reports and suggestions for improvements are most welcome.

---

### Contact

If you have any questions, comments or suggestions, do not hesitate to contact me at philippe@paquet.email
