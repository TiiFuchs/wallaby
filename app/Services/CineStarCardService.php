<?php

namespace App\Services;

use App\Exceptions\CineStarCard\InvalidAuthenticationException;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

class CineStarCardService
{
    const AUTH_START = 'https://www.cinestar.de/auth/connect?state=/kino-stade/account/willkommen';

    protected CookieJar $cookies;

    protected Client $client;

    protected ?string $username = null;

    protected ?string $password = null;

    public function __construct()
    {
        $this->cookies = new CookieJar();
        $this->client = new Client([
            'cookies' => $this->cookies,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
            ],
            'allow_redirects' => [
                'track_redirects' => true,
            ],
        ]);
    }

    public function username(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function password(#[\SensitiveParameter] string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @throws GuzzleException
     * @throws InvalidAuthenticationException
     */
    protected function login(): void
    {
        if ($this->username === null || $this->password === null) {
            throw new InvalidAuthenticationException('Missing authentication data');
        }

        $response = $this->client->get(self::AUTH_START);

        $crawler = new Crawler($response->getBody()->getContents());
        $form = $crawler->filterXPath('//*[@id="kc-form-login"]');
        $action = $form->attr('action');

        $response = $this->client->post($action, [
            'form_params' => [
                'username' => $this->username,
                'password' => $this->password,
                'credentialId' => '',
            ],
        ]);

        $redirects = $response->getHeader('X-Guzzle-Redirect-History');
        if (count($redirects) === 0) {
            throw new InvalidAuthenticationException('Invalid credentials');
        }
    }

    /**
     * @throws GuzzleException
     * @throws InvalidAuthenticationException
     */
    public function data(): array
    {
        $this->login();

        return [
            ...$this->profileData(),
            ...$this->welcomeData(),
        ];
    }

    /**
     * @throws GuzzleException
     */
    protected function welcomeData(): array
    {
        $data = [];

        $response = $this->client->get('https://www.cinestar.de/kino-stade/account/willkommen');
        $content = $response->getBody()->getContents();

        $crawler = new Crawler($content);
        $table = $crawler->filter('.keyValueList');

        $keyMap = [
            'Anzahl Prämienpunkte' => 'premium_points',
            'Kundennummer' => 'customer_number',
        ];

        /** @var \DOMElement $row */
        foreach ($table->children() as $row) {
            if ($row->childElementCount < 2) {
                continue;
            }

            $label = $row->childNodes->item(0)->firstChild->textContent;
            $value = $row->childNodes->item(1)->firstChild->textContent;

            if ($keyMap[$label] === 'premium_points') {
                $value = parseFloat($value);
            }

            $data[$keyMap[$label]] = $value;
        }

        return $data;
    }

    /**
     * @throws GuzzleException
     */
    protected function profileData(): array
    {
        $data = [];

        $response = $this->client->get('https://www.cinestar.de/kino-stade/account/mein-profil');
        $content = $response->getBody()->getContents();

        $crawler = new Crawler($content);
        $table = $crawler->filter('.keyValueList')->first();

        $keyMap = [
            'Kundennummer' => 'customer_number',
            'Geschlecht' => 'gender',
            'Vorname' => 'first_name',
            'Nachname' => 'last_name',
            'Straße/ Hausnummer' => 'street_and_number',
            'PLZ' => 'postal_code',
            'Ort' => 'city',
            'Mein CineStar Stammkino' => 'regular_cinema',
            'Geburtsdatum' => 'date_of_birth',
        ];

        /** @var \DOMElement $row */
        foreach ($table->children() as $row) {
            if ($row->childNodes->count() < 2) {
                continue;
            }

            $label = $row->firstChild->textContent;
            $value = '';
            for ($i = 1; $i < $row->childNodes->count(); $i++) {
                $value = trim($value.' '.$row->childNodes->item($i)->textContent);
            }

            $data[$keyMap[$label]] = $value;
        }

        return $data;
    }
}
