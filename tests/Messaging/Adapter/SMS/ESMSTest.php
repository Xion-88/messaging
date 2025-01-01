<?php

namespace Utopia\Tests\Adapter\SMS;

use Utopia\Messaging\Adapter\SMS\ESMS;
use Utopia\Messaging\Messages\SMS;
use Utopia\Tests\Adapter\Base;

class ESMSTest extends Base
{
    public function testSendSMS(): void
    {

        $sender = new ESMS(getenv('ESMS_API_KEY'), getenv('ESMS_API_SECRET'));

        $message = new SMS(
            to: [getenv('ESMS_TO')],
            content: 'Test Content 888888',
        );

        $response = $sender->send($message);
        $this->assertResponse($response);
    }
}