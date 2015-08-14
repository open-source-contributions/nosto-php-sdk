<?php

require_once(dirname(__FILE__) . '/../_support/NostoProduct.php');

class ServiceRecrawlTest extends \Codeception\TestCase\Test
{
	use \Codeception\Specify;

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * Tests that product re-crawl API requests cannot be made without an API token.
     */
    public function testSendingProductReCrawlWithoutApiToken()
    {
		$account = new NostoAccount('platform-00000000');
        $product = new NostoProduct();

        $this->setExpectedException('NostoException');

        $service = new NostoServiceRecrawl($account);
        $service->addProduct($product);
        $service->send();
    }

	/**
	 * Tests that product re-crawl API requests can be made.
	 */
	public function testSendingProductReCrawl()
    {
		$account = new NostoAccount('platform-00000000');
		$product = new NostoProduct();
		$token = new NostoApiToken('products', '01098d0fc84ded7c4226820d5d1207c69243cbb3637dc4bc2a216dafcf09d783');
		$account->addApiToken($token);

        $service = new NostoServiceRecrawl($account);
        $service->addProduct($product);
        $result = $service->send();

		$this->specify('successful product re-crawl', function() use ($result) {
			$this->assertTrue($result);
		});
    }
}
