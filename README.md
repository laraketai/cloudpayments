# Cloud Payments PHP-клиент

Пакет предоставляет простой способ использования [Cloud Payments API](https://developers.cloudpayments.ru/#api).

## Установка

Для этого пакета с composer, используя следующую команду:

```shell
$ composer require laraketai/cloudpayments
```

## Конфигурация

Для настройки клиента используйте экземпляр `Config`. Это конструктор требует ** Public ID ** и ** API Secret **
котопый вы можете найти в личном кабинете ClodPayments.

```php
use Tamaco\CloudPayments\Config;

$config = new Config('pk_some_key', 'some_api_key');
```

## Применение

Выберите один из [requset builders](#request-builders):

```php
$request_builder = new CardsAuthRequestBuilder;
```

Установите все необходимые параметры с помощью установщиков:

```php
$request_builder->setAccountId('some_id');
$request_builder->setName('name');
```

PSR7 запрос:

```php
use Psr\Http\Message\RequestInterface;

/** @var RequestInterface $request **/
$request = $request_builder->buildRequest();
```

Настройте клиент и отправьте запрос:

```php
use Tamaco\CloudPayments\Config;
use Tamaco\CloudPayments\Client;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;

$clinet = new Client(new GuzzleClient, new Config('public_id', 'api_key'));

/** @var ResponseInterface $response **/
$response = $client->send($request);
```

## Api клиент

### Конструктор

Конструктору требуется любой экземпляр `GuzzleHttp \ ClientInterface` и экземпляр` Config`

```php
use Tamaco\CloudPayments\Client;
use GuzzleHttp\Client as GuzzleClient;

$client = new Client(new GuzzleClient, $config);
```

### Отправка

Этот метод позволяет отправить любой `Psr \ Http \ Message \ RequestInterface` 
и возвращает только` Psr \ Http \ Message \ ResponseInterface`,
которые позволяют вам создавать собственные запросы по своему усмотрению или использовать один из предоставленных конструкторов запросов.

Этот клиент делает только одно: авторизует запросы на CloudPayments и отправляет их.

```php
use GuzzleHttp\Psr7\Request;

$request = new Request('POST','https://api',[],'{"foo":"bar"}');

$response = $client->send($request);
```

## Request builders

Поддерживаемые builders:

Builder | Описание | Ссылка на документацию
:----- | :---------- | :----:
`TestRequestBuilder` | The method to test the interaction with the API | [Link][method_test_doc]
`CardsAuthRequestBuilder` | The method to make a payment by a cryptogram | [Link][method_payment_by_cryptogram]
`CardsChargeRequestBuilder` | The method to make a payment by a cryptogram. Charge only | [Link][method_payment_by_cryptogram]
`CardsPost3DsRequestBuilder` | 3-D Secure Processing | [Link][method_payment_3ds]
`TokensAuthRequestBuilder` | The method to make a payment by a token | [Link][method_payment_token]
`TokensChargeRequestBuilder` | The method to make a payment by a token. Charge only | [Link][method_payment_token]
`PaymentsConfirmRequestBuilder` | Payment Confirmation | [Link][method_payment_confirm]
`PaymentsVoidRequestBuilder` | Payment Cancellation | [Link][method_payment_cancel]
`SubscriptionsCreateRequestBuilder` | Creation of Subscriptions on Recurrent Payments | [Link][method_subscription_create]
`SubscriptionsGetRequestBuilder` | Subscription Details | [Link][method_subscription_get]
`SubscriptionsFindRequestBuilder` | Subscriptions Search | [Link][method_subscription_find]
`SubscriptionsUpdateRequestBuilder` | Recurrent Payments Subscription Change | [Link][method_subscription_update]
`SubscriptionsCancelRequestBuilder` | Subscription on Recurrent Payments Cancellation | [Link][method_subscription_cancel]

> How to get [card cryptogram packet](https://developers.cloudpayments.ru/#skript-checkout)?

### Идемпотентность

**Идемпотентность** — свойство API при повторном запросе выдавать тот же результат, что на первичный без повторной обработки. Это значит, что вы можете отправить несколько запросов к системе с одинаковым идентификатором, при этом обработан будет только один запрос, а все ответы будут идентичными. Таким образом реализуется защита от сетевых ошибок, которые приводят к созданию дублированных записей и действий.
Для включения идемпотентности необходимо в запросе к API передавать заголовок с ключом `setRequestId('request_id')`, содержащий уникальный идентификатор. Формирование идентификатора запроса остается на вашей стороне — это может быть guid, комбинация из номера заказа, даты и суммы или любое другое значение на ваше усмотрение.
Каждый новый запрос, который необходимо обработать, должен включать новое значение `request_id`. Обработанный результат хранится в системе в течение 1 часа.

[method_test_doc]:https://developers.cloudpayments.ru/#testovyy-metod
[method_payment_by_cryptogram]:https://developers.cloudpayments.ru/#oplata-po-kriptogramme
[method_payment_3ds]:https://developers.cloudpayments.ru/#obrabotka-3-d-secure
[method_payment_token]:https://developers.cloudpayments.ru/#oplata-po-tokenu-rekarring
[method_payment_confirm]:https://developers.cloudpayments.ru/#podtverzhdenie-oplaty
[method_payment_cancel]:https://developers.cloudpayments.ru/#otmena-oplaty
[method_subscription_create]:https://developers.cloudpayments.ru/#sozdanie-podpiski-na-rekurrentnye-platezhi
[method_subscription_get]:https://developers.cloudpayments.ru/#zapros-informatsii-o-podpiske
[method_subscription_find]:https://developers.cloudpayments.ru/#poisk-podpisok
[method_subscription_update]:https://developers.cloudpayments.ru/#izmenenie-podpiski-na-rekurrentnye-platezhi
[method_subscription_cancel]:https://developers.cloudpayments.ru/#otmena-podpiski-na-rekurrentnye-platezhi

## Интеграция фреймворка

### Laravel

Laravel 5.5 и выше использует автоматическое обнаружение пакетов, поэтому не требуется вручную регистрировать поставщика услуг. В противном случае вы должны добавить сервис-провайдера в массив `provider` в`. / Config / app.php`:

```php
'providers' => [
    // ...
    Tamaco\CloudPayments\Frameworks\Laravel\ServiceProvider::class,
]
```

#### Laravel конфигурация

Поставщик услуг выбирает конфигурацию из конфигурации `services.cloud_payments`. Вам требуется прописать его в
Файл `config / services.php`.
Например:

```php
return [
    // ...
    /*
    |--------------------------------------------------------------------------
    | CloudPayments Settings
    |--------------------------------------------------------------------------
    | - `public_id` (string) - Public ID  (You can find it in personal area)
    | - `api_key`   (string) - API Secret (You can find it in personal area)
    |
    */
    'cloud_payments' => [
        'public_id' => env('CLOUD_PAYMENTS_PUBLIC_ID', 'some id'),
        'api_key'   => env('CLOUD_PAYMENTS_API_KEY', 'some api key'),
    ],
];
```

## Тестирование

Для тестирования пакетов используйте в терминале `phpunit`:

```shell
$ make build
$ make install
$ make test
```

## Лицензия

Это программное обеспечение с открытым исходным кодом, лицензированное по лицензии MIT.

