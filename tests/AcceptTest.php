<?php

/**
 * Tests the Accept class
 *
 * @author      Brian Moon <brianm@dealnews.com>
 * @copyright   1997-Present DealNews.com, Inc
 * @package     PageMill
 *
 */

namespace PageMill\Accept\Request;

use \PageMill\Accept\Accept;

class AcceptTest extends \PHPUnit\Framework\TestCase {

    /**
     * @dataProvider determineDataProvider
     */
    public function testDetermine($valid_mime_types, $accept, $expect, $message) {
        $r = new Accept();

        $resp = $r->determine(
            $valid_mime_types,
            [
                "HTTP_ACCEPT" => $accept

            ]
        );

        $this->assertEquals(
            $expect,
            $resp,
            $message
        );
    }

    public function determineDataProvider() {
        return [
            [
                [
                    "text/html"
                ],
                "text/plain;q=0.1",
                null,
                "No Match"
            ],
            [
                [
                    "text/html"
                ],
                "text/html;q=0.1",
                "text/html",
                "Basic Test"
            ],
            [
                [
                    "text/html",
                    "application/json"
                ],
                "application/json;q=1.0,text/html;q=0.1",
                "application/json",
                "Test Client Preference"
            ],
            [
                [
                    "application/json",
                    "text/html"
                ],
                "text/html;q=1.0,application/json;q=1.0",
                "application/json",
                "Test Input Preference"
            ],
            [
                [
                    "application/json",
                    "text/html"
                ],
                null,
                "application/json",
                "Empty Accept Header"
            ],
            [
                [
                    "application/json",
                    "text/html"
                ],
                "text/html;q=1.0,*/json;q=1.0",
                "application/json",
                "Partial Wildcard"
            ],
            [
                [
                    "application/json"
                ],
                "text/html;q=1.0,*/*;q=1.0",
                "application/json",
                "Wildcard"
            ],
            [
                [
                    "application/JSON",
                    "text/html"
                ],
                "text/html;q=1.0,*/Json;q=1.0",
                "application/JSON",
                "Case Insensitive"
            ]
        ];
    }
}
