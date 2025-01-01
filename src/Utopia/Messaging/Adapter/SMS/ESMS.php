<?php

namespace Utopia\Messaging\Adapter\SMS;

use Utopia\Messaging\Adapter\SMS as SMSAdapter;
use Utopia\Messaging\Messages\SMS as SMSMessage;
use Utopia\Messaging\Response;

class ESMS extends SMSAdapter
{
    protected const NAME = 'ESMS';

    /**
     * @param  string  $apiKey api key
     * @param  string  $apiSecret secret
     */
    public function __construct(
        private string $apiKey,
        private string $apiSecret
    ) {
    }

    public function getName(): string
    {
        return static::NAME;
    }

    public function getMaxMessagesPerRequest(): int
    {
        return 1000;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    protected function process(SMSMessage $message): array
    {
        $response = new Response($this->getType());

        $response->setDeliveredTo(\count($message->getTo()));
        if (substr(\implode(',', $message->getTo()), 0, 3) === '+60') {
            $result = $this->request(
                method: 'POST',
                url: 'https://api.esms.com.my/sms/send',
                headers: [
                    'Content-Type: application/x-www-form-urlencoded'
                ],
                body: [
                    'user' => $this->apiKey,
                    'pass' => $this->apiSecret,
                    'msg' => 'RM0.00 '.$message->getContent(),
                    'to' =>  \implode(',', $message->getTo()),
                ],
            );
        } else {
            $result = $this->request(
                method: 'POST',
                url: 'https://api.esms.com.my/sms/send',
                headers: [
                    'Content-Type: application/x-www-form-urlencoded'
                ],
                body: [
                    'user' => $this->apiKey,
                    'pass' => $this->apiSecret,
                    'msg' => $message->getContent(),
                    'to' =>  \implode(',', $message->getTo()),
                ],
            );
        }
        if ($result['statusCode'] === 200 && $result['response']['status'] == 0) {
            $response->setDeliveredTo(\count($message->getTo()));
            foreach ($message->getTo() as $to) {
                $response->addResult($to);
            }
        } else {
            foreach ($message->getTo() as $to) {
                $response->addResult($to,'Status Code : '.$result['response']['status']);
            }
        }

        return $response->toArray();
    }
}
