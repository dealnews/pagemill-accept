# HTTP Accept Header Parser

Parses an Accept header and determines which content type is preferred by the
client according to [RFC 2616](https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html).

```php
// content types with which the web service can respond
$valid_content_types = [
    "application/json",
    "text/xml"
];

$accept = new \PageMill\Accept\Accept();
$content_type = $accept->determine($valid_content_types);

// If Accept did not find an acceptable content type, the determine method
// will return null. The server should respond to the client with a 406.
if ($content_type === null) {
    // Respond with 406 Not Acceptable
    http_response_code(406);
    exit();
}
```
