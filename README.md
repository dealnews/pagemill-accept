# HTTP Accept Header Parser

Parses an Accept header and determines which content type is preferred by the
client according to [RFC 2616](https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html).

```
// content types with which this web service can respond
$valid_content_types = [
    "application/json",
    "text/xml"
];

$accept = new \PageMill\Accept\Accept();
$content_type = $accept->determine($valid_content_types);

// If Accept did not find an acceptable content type from the list,
// the server should tell the client with a 406
if ($content_type === false) {
    // Respond with 406 Not Acceptable
}
```
