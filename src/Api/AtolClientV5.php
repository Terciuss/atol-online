<?php

/*
 * Copyright (c) 2020-2021 Антон Аксенов (Anthony Axenov)
 *
 * This code is licensed under MIT.
 * Этот код распространяется по лицензии MIT.
 * https://github.com/anthonyaxenov/atol-online/blob/master/LICENSE
 */

declare(strict_types=1);

namespace AtolOnline\Api;

use AtolOnline\Constraints;
use AtolOnline\Exceptions\{AuthFailedException,
    EmptyLoginException,
    EmptyPasswordException,
    TooLongLoginException,
    TooLongPasswordException};
use AtolOnline\TestEnvParams;
use GuzzleHttp\{Client, Exception\GuzzleException};
use JetBrains\PhpStorm\Pure;

/**
 * Класс для подключения АТОЛ Онлайн API
 */
abstract class AtolClientV5
{
    /**
     * @var array Последний запрос к серверу АТОЛ
     */
    protected array $request;

    /**
     * @var AtolResponse|null Последний ответ сервера АТОЛ
     */
    protected ?AtolResponse $response;

    /**
     * @var bool Флаг тестового режима
     */
    protected bool $test_mode;

    /**
     * @var Client HTTP-клиент для работы с API
     */
    protected Client $http;

    /**
     * @var string|null Логин доступа к API
     */
    private ?string $login = null;

    /**
     * @var string|null Пароль доступа к API (readonly)
     */
    private ?string $password = null;

    /**
     * @var string|null Токен авторизации
     */
    private ?string $token = null;

    /**
     * Конструктор
     *
     * @param bool $test_mode
     * @param string|null $login
     * @param string|null $password
     * @param array $config
     * @throws EmptyLoginException
     * @throws EmptyPasswordException
     * @throws TooLongLoginException
     * @throws TooLongPasswordException
     * @see https://guzzle.readthedocs.io/en/latest/request-options.html Допустимые параметры для $config
     */
    public function __construct(
        bool    $test_mode = true,
        ?string $login = null,
        ?string $password = null,
        array   $config = []
    )
    {
        $this->http = new Client(
            array_merge($config, [
                'http_errors' => $config['http_errors'] ?? false,
            ])
        );
        $this->setTestMode($test_mode);
        !is_null($login) && $this->setLogin($login);
        !is_null($password) && $this->setPassword($password);
    }

    /**
     * Возвращает последний запрос к серверу
     *
     * @return array
     */
    public function getLastRequest(): array
    {
        return $this->request;
    }

    /**
     * Возвращает последний ответ сервера
     *
     * @return AtolResponse|null
     */
    public function getLastResponse(): ?AtolResponse
    {
        return $this->response;
    }

    /**
     * Возвращает установленный флаг тестового режима
     *
     * @return bool
     */
    public function isTestMode(): bool
    {
        return $this->test_mode;
    }

    /**
     * Устанавливает флаг тестового режима
     *
     * @param bool $test_mode
     * @return $this
     */
    public function setTestMode(bool $test_mode = true): self
    {
        $this->test_mode = $test_mode;
        return $this;
    }

    /**
     * Возвращает текущий токен авторизации
     *
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Устанавливает токен авторизации
     *
     * @param string|null $token
     * @return $this
     */
    public function setToken(?string $token): self
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Возвращает логин доступа к API
     *
     * @return string|null
     */
    #[Pure]
    public function getLogin(): ?string
    {
        return $this->isTestMode()
            ? TestEnvParams::FFD12()['login']
            : $this->login;
    }

    /**
     * Устанавливает логин доступа к API
     *
     * @param string $login
     * @return $this
     * @throws EmptyLoginException
     * @throws TooLongLoginException
     */
    public function setLogin(string $login): self
    {
        $login = trim($login);
        if (empty($login)) {
            throw new EmptyLoginException();
        } elseif (mb_strlen($login) > Constraints::MAX_LENGTH_LOGIN) {
            throw new TooLongLoginException($login);
        }
        $this->login = $login;
        return $this;
    }

    /**
     * Возвращает пароль доступа к API
     *
     * @return string|null
     */
    #[Pure]
    public function getPassword(): ?string
    {
        return $this->isTestMode()
            ? TestEnvParams::FFD12()['password']
            : $this->password;
    }

    /**
     * Устанавливает пароль доступа к API
     *
     * @param string $password
     * @return $this
     * @throws EmptyPasswordException Пароль ККТ не может быть пустым
     * @throws TooLongPasswordException Слишком длинный пароль ККТ
     */
    public function setPassword(string $password): self
    {
        if (empty($password)) {
            throw new EmptyPasswordException();
        } elseif (mb_strlen($password) > Constraints::MAX_LENGTH_PASSWORD) {
            throw new TooLongPasswordException($password);
        }
        $this->password = $password;
        return $this;
    }

    /**
     * Возвращает набор заголовков для HTTP-запроса
     *
     * @return array
     */
    #[Pure]
    private function getHeaders(): array
    {
        $headers['Content-type'] = 'application/json; charset=utf-8';
        if ($this->getToken()) {
            $headers['Token'] = $this->getToken();
        }
        return $headers;
    }

    /**
     * Возвращает полный URL для запроса
     *
     * @param string $method
     * @return string
     */
    protected function getUrlToMethod(string $method): string
    {
        return $this->getMainEndpoint() . '/' . trim($method);
    }

    /**
     * Отправляет авторизационный запрос на сервер АТОЛ и возвращает авторизационный токен
     *
     * @return string|null
     * @throws AuthFailedException
     * @throws EmptyPasswordException
     * @throws EmptyLoginException
     * @throws GuzzleException
     */
    protected function doAuth(): ?string
    {
        $result = $this->sendRequest('POST', $this->getAuthEndpoint(), [
            'login' => $this->getLogin() ?? throw new EmptyLoginException(),
            'pass' => $this->getPassword() ?? throw new EmptyPasswordException(),
        ]);
        if (!$result->isSuccessful() || !$result->getContent()->token) {
            throw new AuthFailedException($result);
        }
        return $result->getContent()?->token;
    }

    /**
     * Отправляет запрос и возвращает декодированный ответ
     *
     * @param string $http_method Метод HTTP
     * @param string $url URL
     * @param array|null $data Данные для передачи
     * @param array|null $options Параметры Guzzle
     * @return AtolResponse
     * @throws GuzzleException
     * @see https://guzzle.readthedocs.io/en/latest/request-options.html
     */
    protected function sendRequest(
        string $http_method,
        string $url,
        ?array $data = null,
        ?array $options = null
    ): AtolResponse
    {
        $http_method = strtoupper(trim($http_method));
        $options['headers'] = array_merge($this->getHeaders(), $options['headers'] ?? []);
        $http_method != 'GET' && $options['json'] = $data;
        $this->request = array_merge([
            'method' => $http_method,
            'url' => $url,
        ], $options);
        $response = $this->http->request($http_method, $url, $options);
        return $this->response = new AtolResponse($response);
    }

    /**
     * Выполняет авторизацию на сервере АТОЛ
     * Авторизация выполнится только если неизвестен токен
     *
     * @return bool
     * @throws AuthFailedException
     * @throws EmptyLoginException
     * @throws EmptyPasswordException
     * @throws GuzzleException
     */
    public function auth(): bool
    {
        if (empty($this->getToken()) && $token = $this->doAuth()) {
            $this->setToken($token);
        }
        return !empty($this->getToken());
    }

    /**
     * Возвращает URL для запроса авторизации
     *
     * @return string
     */
    abstract protected function getAuthEndpoint(): string;

    /**
     * Возвращает URL для запросов
     *
     * @return string
     */
    abstract protected function getMainEndpoint(): string;
}
