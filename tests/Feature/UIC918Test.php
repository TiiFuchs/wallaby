<?php

it('verifies v01 tickets correctly', function () {
    // V01 Ticket
    $data = base64_decode('I1VUMDExMDgwMDAwMDEwLAIUTMKMY0klw4bCiizDoR/DtBsUbMKSJsKbbSHDmwIUXcODwrF1w5DCp8OqOCpaw5dae8OtesOSw6pvwqlQAAAAADAzNTJ4wpwLwo3Dt3B1dDEwNDAwNTY0wrAwcAxyDHMPdDJlQALChsKWBiZGBkZGwobDhsOGFgYuwq4uwq7CocOxIT7CjsKRQE3ChsKWRgE+wo7CngYGQMKdw4ZAw5LDkMOIAATDjMK9cxLCi8KLU8KtDEzCgBwjwojCmFlAalHCscKeFcKYbWgBwrLDkMOAw5ItMcKjKDvCscKoJBXDjMKFaDY0cj/CvCfCpyQzPTs1wrPDhMOKw4DDkMOQFCQBwpQ3NDAyw5UzMMORA8K5w4TDgBjDqCbCiDDCiDIBw7LCjMKgPGMDI8K4DUZGQRnCqcKZeTnCiXkpwroBacKJOVXCuiHCmcOJw5nCqSXCocOxbj7CrhFAwqXChgbDpknCmxjDmh7Dsh7DrMOteiEqw4jDkcOIKsOTw6LCuMKUbUvClcOBwqwFXA0MHCsuwpw6w4TCscOiw5TCs8KXwowsLgx2wowMworCvVUXw5NOw6XCnVEzwpPClcOZw7rDsMOpZQ12w6deBsKlC8KsDQ0NOksuwpzCunTDp8OGwqE7J8KiFsKcOXTDo0vDlMKKS8OHwq7CnXoBCsKZwrBQcMO4GjHCpAADwpPCmTHDo8OnW0bCiRLDpi8Sw6UOMz0YHGZJMDDDsMKVwoAEw5nDr8KwCgh3MMOwAAARw7R5w5I=');
    $data = mb_convert_encoding($data, 'latin1', 'utf-8');

    $ticket = (new \App\Services\Uic918Parser\TicketParser($data))->parse();
    expect($ticket->messageTypeVersion)->toBe('01')
        ->and($ticket->isValid())->toBeTrue();
});

it('verifies v02 tickets correctly', function () {
    // V02 Ticket
    $data = base64_decode('I1VUMDIxMDgwMDAwMDJZJ8Khwq3DiHHCjcKbFMOvSsKaw7rDgw/DlWbClMKsFsKJwpbDhwfDnE/CugVreEp+wop8akEgw7vCnh0Hw6k3wo/CogFkMcOEYxJ4Gmkpwr0+DT8hAD7DpsOaMDI1OHjCnAvCjXfDs3HCjTA0NjDCtDBLw5rDhMOQw7bCkMO3YG9XwoHCqCBHI8KrTMKLw7Nswq3CpcKRw6ciF3A1MHDCrMK4cMOqEMOHwopTw49eMsKywrjChMObMTIoGXsba13CnB5gw6ZjwrFCOcOxw6LDqTjCuVjCo3nCnXvDpcO6w73CmC7DncKIwrlzM8Kaw6vDgsKtWDlXw4E5M8KiZ8OdwpwbM8KXSzgiKsK6w4sLJMOoEzorw4LCr8KDwp/Dh8KHwo/Ch3/DosOVw6DCqVxdAhMDwromCcKGTDk1cSLDl8OEwovDgcOCw50XLl8Mw6bCmix8WcKYa8OiJS/Cr8KJDQYMF1h0GTw4GDXDjsO2wqzDp8Ojw7NZwqvCl2BgYGEQFmpgCMKkw40ZUhgYGBjCmTJ+wr7DuSVRw4J+QcKiw4xhKgPCg8ODNAnCoCgDSMKQw6MOG8KvRFnClVwDwpDDsxvCm8KKw59QFULCmQBQw5lkw5Q=');
    $data = mb_convert_encoding($data, 'latin1', 'utf-8');

    $ticket = (new \App\Services\Uic918Parser\TicketParser($data))->parse();
    expect($ticket->messageTypeVersion)->toBe('02')
        ->and($ticket->isValid())->toBeTrue();
});
