<?php
/**
 * Copyright (c) 2019, Nosto Solutions Ltd
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * 3. Neither the name of the copyright holder nor the names of its contributors
 * may be used to endorse or promote products derived from this software without
 * specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Nosto Solutions Ltd <contact@nosto.com>
 * @copyright 2019 Nosto Solutions Ltd
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 *
 */

namespace Nosto\Test\Unit\Operation;

use Codeception\Specify;
use Codeception\TestCase\Test;
use Nosto\Model\ExchangeRateCollection;
use Nosto\Model\Signup\Account;
use Nosto\Operation\SyncRates;
use Nosto\Request\Api\Token;
use Nosto\Test\Support\MockExchangeRate;

class ExchangeRateTest extends Test
{
    use Specify;

    /**
     * Tests that exchange rates can be synced to Nosto.
     */
    public function testSyncingExchangeRates()
    {
        $account = new Account('platform-00000000');
        $token = new Token('rates', 'token');
        $account->addApiToken($token);

        $rates = new ExchangeRateCollection();
        $rates->addRate("Euros", MockExchangeRate::EUR());
        $rates->addRate("Pounds", MockExchangeRate::GBP());
        $rates->addRate("Dollars", MockExchangeRate::USD());
        $op = new SyncRates($account);
        $result = $op->update($rates);

        $this->specify('successful exchange rates sync', function () use ($result) {
            $this->assertTrue($result);
        });
    }
}
