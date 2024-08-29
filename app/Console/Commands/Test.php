<?php

namespace App\Console\Commands;

use App\Models\PassDetails\DTicket;
use App\Services\UIC918\Parser\TicketParser;
use Illuminate\Console\Command;

class Test extends Command
{
    protected $signature = 'wallaby:test';

    protected $description = 'Command description';

    public function handle(): void
    {
        // D-Ticket
        $dTicket = DTicket::find(1);
        $data = $dTicket->barcode;

        // V01 Ticket
        //        $data = base64_decode('I1VUMDExMDgwMDAwMDEwLAIUTMKMY0klw4bCiizDoR/DtBsUbMKSJsKbbSHDmwIUXcODwrF1w5DCp8OqOCpaw5dae8OtesOSw6pvwqlQAAAAADAzNTJ4wpwLwo3Dt3B1dDEwNDAwNTY0wrAwcAxyDHMPdDJlQALChsKWBiZGBkZGwobDhsOGFgYuwq4uwq7CocOxIT7CjsKRQE3ChsKWRgE+wo7CngYGQMKdw4ZAw5LDkMOIAATDjMK9cxLCi8KLU8KtDEzCgBwjwojCmFlAalHCscKeFcKYbWgBwrLDkMOAw5ItMcKjKDvCscKoJBXDjMKFaDY0cj/CvCfCpyQzPTs1wrPDhMOKw4DDkMOQFCQBwpQ3NDAyw5UzMMORA8K5w4TDgBjDqCbCiDDCiDIBw7LCjMKgPGMDI8K4DUZGQRnCqcKZeTnCiXkpwroBacKJOVXCuiHCmcOJw5nCqSXCocOxbj7CrhFAwqXChgbDpknCmxjDmh7Dsh7DrMOteiEqw4jDkcOIKsOTw6LCuMKUbUvClcOBwqwFXA0MHCsuwpw6w4TCscOiw5TCs8KXwowsLgx2wowMworCvVUXw5NOw6XCnVEzwpPClcOZw7rDsMOpZQ12w6deBsKlC8KsDQ0NOksuwpzCunTDp8OGwqE7J8KiFsKcOXTDo0vDlMKKS8OHwq7CnXoBCsKZwrBQcMO4GjHCpAADwpPCmTHDo8OnW0bCiRLDpi8Sw6UOMz0YHGZJMDDDsMKVwoAEw5nDr8KwCgh3MMOwAAARw7R5w5I=');

        // V02 Ticket
        //        $data = base64_decode('I1VUMDIxMDgwMDAwMDJZJ8Khwq3DiHHCjcKbFMOvSsKaw7rDgw/DlWbClMKsFsKJwpbDhwfDnE/CugVreEp+wop8akEgw7vCnh0Hw6k3wo/CogFkMcOEYxJ4Gmkpwr0+DT8hAD7DpsOaMDI1OHjCnAvCjXfDs3HCjTA0NjDCtDBLw5rDhMOQw7bCkMO3YG9XwoHCqCBHI8KrTMKLw7Nswq3CpcKRw6ciF3A1MHDCrMK4cMOqEMOHwopTw49eMsKywrjChMObMTIoGXsba13CnB5gw6ZjwrFCOcOxw6LDqTjCuVjCo3nCnXvDpcO6w73CmC7DncKIwrlzM8Kaw6vDgsKtWDlXw4E5M8KiZ8OdwpwbM8KXSzgiKsK6w4sLJMOoEzorw4LCr8KDwp/Dh8KHwo/Ch3/DosOVw6DCqVxdAhMDwromCcKGTDk1cSLDl8OEwovDgcOCw50XLl8Mw6bCmix8WcKYa8OiJS/Cr8KJDQYMF1h0GTw4GDXDjsO2wqzDp8Ojw7NZwqvCl2BgYGEQFmpgCMKkw40ZUhgYGBjCmTJ+wr7DuSVRw4J+QcKiw4xhKgPCg8ODNAnCoCgDSMKQw6MOG8KvRFnClVwDwpDDsxvCm8KKw59QFULCmQBQw5lkw5Q=');

        //        $data = base64_decode('I1VUMDIxMDgwMDAwMDIZFE8uTsO+dMKKYUBfwoDCocKIwrDDqsKzJl06w5UbXBAAw7DDr8OKw4XCmcOlwqA0wr1hwrzDkCzCkinDlMKkw5DCqjolRXfCt3FBdXtsI1DDj8Oxw7bDu8OSw5zCtS8wMjU2eMKcAcO1AArDv1VfRkxFWDEzMDE5MmLCsgDChsOhDcOBwqLDjGAVEQjCgQUcwoRBwpEiVGjCsRnCoArCgcKACMKYw4LDpsOoDsKmw4bDkMOkw5LDjMOow4wBwoDCgiIrwp/CiMKAEQ3DhHgyIlrCqBsmHFQRwrDDqMOlwq8PLsKZT0vCjVA6w4jDoUnCrC0tw49FaG0uwo8gwohtDsKuRsOqw40sJ0QHwoYmBwYHw4UIwoglSEjCqcOKZUnCqCVISkXDqsOpw6UlSWglScOow6VIw4pFSEgqZ8KGJicGwqfDi07CrMKuTSxtAMOIAGgCFsKOAEJQAMKcEFrCrCDCjMOYw4rDsMOgw6TDisOSw6ZAworDqsOkw57DoMOCMDA4MFZVMDEwMDUzAGQAAAABAS7CjsKCNBh0B8OQGHZBXgAAQV8YAAAAAC7CjsKCNAjDnAYNGHZ6EsOwSMK8Xgg=');

        // Gruppenticket
        $data = base64_decode('I1VUMDExMDgwMDAwMDEwLAIUD8K8AMOtcMONw5rDrAnCkw4gw7TCumXDuBxswpweAhQCFcOYw47CoMORE8OlDil2bXrDr8KrwrlXTCU7AAAAADAzNzl4wpwLwo3Dt3B1dDEwNDAwNTY0wrAwcDEKwok0D3fDsWNAAgZmwoYGRgZGRsKGJkbCpgYuwq4uwq7CocOxIT7CjsKRQE1GRiYBPsKOwp4GBkDCnSZAw5LDkMOIAATDjMK9cxLCi8KLU8KtDEzCgRwjwojCmFlAalHCscKeFcKYbWgBwrLDkMOAw5ItMcKjKDvCscKoJBXDjMKFaDY0cj/CvCfCpyQzPTs1wrPDhMOKw4DDkMOQFGQCUMOew4QwLD9Pw4HDgEzDj8OQQA/DpBoFA0vCoGlcTsKZw4UKBsOmCEFjwpAVJsKGwpYgw6PCoMKmwpoCeUZQwp7CqcKBMcOcAUbDhsKBwqXCqUXCuinCpUXDiRnDhcK6PsKJeSnCuiHCmcOJw5nCqSXCocOxbj7CrhHChsOGQMObwo3CkjYxwrQ9w6Q9wrjDqMO0ElFBwo5GVsKZFsKXw5TCoMOTVR/DlRXCuBobOGYcesO2woJvw5nCsQtPLsKdeVHDhsOYw5DCpMOEw4DDj8OQIHBqw4rCssOLK33CksKEwqXDucO6wqI+fHo5wqVdworDgXUBNyMTQ8Ksw6vDlcKpJ8K2TsK8esKyd8OhwpnCrcKGwq07J2wNXMOawrt2w6oFUMOwwoXChcKCI8OBwoghBRjDosKswowJw4LCj8KkJEpYNkrClDvCunkwOMK6SzAwSEfCgATDmcOvwrAKCHcwMAIAwqQ+f08=');

        $data = mb_convert_encoding($data, 'latin1', 'utf-8');

        $ticket = (new TicketParser)->parse($data);
        ray($ticket->flexibleContent);
    }
}
