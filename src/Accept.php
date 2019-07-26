<?php

/**
 * @author      Brian Moon <brianm@dealnews.com>
 * @copyright   1997-Present DealNews.com, Inc
 * @package     PageMill
 */

namespace PageMill\Accept;

use \PageMill\Pattern\Pattern;

/**
 * Parses the HTTP Accept header and determes
 * the preferred content type
 */
class Accept {

    /**
     * Determines which of the valid content types is
     * preferred by the client as indicated by the Accept header.
     * @param  array      $valid_content_types Array of acceptable
     *                                         content types
     * @param  array|null $server              Optional array to use in place
     *                                         of $_SERVER
     * @return mixed      The preferred content type or false if none match
     *
     * @suppress PhanParamSuspiciousOrder
     */
    public function determine(array $valid_content_types, array $server = null) {

        $chosen_content_type = false;

        if (!empty($valid_content_types)) {

            // Default to $_SERVER if not server data provided
            if ($server === null) {
                $server = [];
                if (!empty($_SERVER)) {
                    $server = $_SERVER;
                }
            }

            // RFC 2616 states that if there is no Accept header is provided
            // that any type should be returned
            if (!isset($server["HTTP_ACCEPT"])) {
                $server["HTTP_ACCEPT"] = "*/*";
            }

            $content_type_preferences = $this->determine_preferred_content_types($server["HTTP_ACCEPT"]);

            $pattern = new Pattern();

            // match the accept list against the Accept header
            $preferred_content_types = [];
            foreach ($valid_content_types as $content_type) {
                foreach ($content_type_preferences as $content_type_pattern => $quality) {
                    $type = "exact";
                    $content_type_pattern = strtolower($content_type_pattern);
                    // if the pattern from the Accept header contains a *,
                    // convert this to a regex match
                    if (strpos($content_type_pattern, "*") !== false) {
                        $type = "regex";
                        $content_type_pattern = '|^'.str_replace($content_type_pattern, "*", ".+").'$|';
                    }
                    $result = $pattern->match(
                        $type,
                        [$content_type_pattern],
                        strtolower($content_type)
                    );
                    if ($result !== false) {
                        $preferred_content_types[$content_type] = $quality;
                    }
                }
            }

            if (!empty($preferred_content_types)) {
                uasort($preferred_content_types, function($a, $b) {
                    if ($a == $b) {
                        return 0;
                    }
                    return ($a > $b) ? -1 : 1;
                });
                $chosen_content_type = key($preferred_content_types);
            }
        }

        return $chosen_content_type;
    }

    /**
     * Parses an Accept header string
     * @param  string $accept_header Accept header value
     * @return array  Array of mime types and their preference value
     */
    public function determine_preferred_content_types(string $accept_header) {
        // see https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
        $accept_content_types = explode(",", $accept_header);
        $content_type_preference = [];
        foreach ($accept_content_types as $content_type) {
            $content_type = trim($content_type);
            $preference = 1.0;
            if (preg_match("/(.+?);q=(1|1.0|0.\d+)$/", $content_type, $match)) {
                $preference = (float)$match[2];
                $content_type = $match[1];
            }
            $content_type_preference[$content_type] = $preference;
        }
        return $content_type_preference;
    }
}
