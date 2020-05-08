<?php

declare(strict_types = 1);

namespace Tamaco\CloudPayments\Requests\Subscriptions;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Tamaco\CloudPayments\Requests\AbstractRequestBuilder;

/**
 * @see https://developers.cloudpayments.ru/#zapros-informatsii-o-podpiske
 */
class SubscriptionsGetRequestBuilder extends AbstractRequestBuilder
{
    /**
     * Required.
     *
     * @var string|null
     */
    protected $id;

    /**
     * @param string $id
     *
     * @return SubscriptionsGetRequestBuilder
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    protected function getRequestPayload(): array
    {
        return [
            'Id' => $this->id,
        ];
    }

    protected function getUri(): UriInterface
    {
        return new Uri('/subscriptions/get');
    }
}
