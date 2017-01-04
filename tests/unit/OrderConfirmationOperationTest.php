<?php
/**
 * Copyright (c) 2016, Nosto Solutions Ltd
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
 * @copyright 2016 Nosto Solutions Ltd
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 *
 */

require_once(dirname(__FILE__) . '/../_support/MockNostoOrderBuyer.php');
require_once(dirname(__FILE__) . '/../_support/MockNostoLineItem.php');
require_once(dirname(__FILE__) . '/../_support/MockNostoOrderStatus.php');
require_once(dirname(__FILE__) . '/../_support/MockNostoOrder.php');

class OrderConfirmationTest extends \Codeception\TestCase\Test
{
    use \Codeception\Specify;

    /**
     * @var NostoOrderInterface
     */
    protected $order;

    /**
     * @var NostoAccountInterface
     */
    protected $account;

    /**
     * Tests the matched order confirmation API call.
     */
    public function testMatchedOrderConfirmation()
    {
        $service = new NostoOperationOrderConfirmation($this->account);
        $result = $service->send($this->order, 'test123');

        $this->specify('successful matched order confirmation', function () use ($result) {
            $this->assertTrue($result);
        });
    }

    /**
     * Tests the un-matched order confirmation API call.
     */
    public function testUnMatchedOrderConfirmation()
    {
        $service = new NostoOperationOrderConfirmation($this->account);
        $result = $service->send($this->order, null);

        $this->specify('successful un-matched order confirmation', function () use ($result) {
            $this->assertTrue($result);
        });
    }

    /**
     * @inheritdoc
     */
    protected function _before()
    {
        $this->order = new MockNostoOrder();
        $this->account = new MockNostoAccount('platform-00000000');
    }
}
