<?php


class HttpRequestTest extends \Codeception\TestCase\Test
{
	/**
	 * This must be a port that is not listening
	 * wherever the tests are run
	 */
	const CURL_TEST_PORT = 9900;

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	/**
	 * Tests setting query params to the request.
	 */
	public function testHttpRequestQueryParams()
	{
		$request = new NostoHttpRequest();
		$request->setQueryParams(array(
			'param1' => 'first',
			'param2' => 'second',
		));
		$params = $request->getQueryParams();
		$this->assertArrayHasKey('param1', $params);
		$this->assertContains('first', $params);
		$this->assertArrayHasKey('param2', $params);
		$this->assertContains('second', $params);
	}

	/**
	 * Tests setting the basic auth type.
	 */
	public function testHttpRequestAuthBasic()
	{
		$request = new NostoHttpRequest();
		$request->setAuthBasic('test', 'test');
		$headers = $request->getHeaders();
		$this->assertContains(
			'Authorization: Basic '.base64_encode(implode(':', array('test', 'test')))
			, $headers
		);
	}

	/**
	 * Tests setting the bearer auth type.
	 */
	public function testHttpRequestAuthBearer()
	{
		$request = new NostoHttpRequest();
		$request->setAuthBearer('test');
		$headers = $request->getHeaders();
		$this->assertContains('Authorization: Bearer test', $headers);
	}

	/**
	 * Tests setting an invalid auth type.
	 */
	public function testHttpRequestAuthInvalid()
	{
		$this->setExpectedException('NostoException');
		$request = new NostoHttpRequest();
		$request->setAuth('test', 'test');
	}

	/**
	 * Tests the "buildUri" helper method.
	 */
	public function testHttpRequestBuildUri()
	{
		$uri = NostoHttpRequest::buildUri(
			sprintf(
				'http://localhost:%d?param1={p1}&param2={p2}',
				self::CURL_TEST_PORT
			),
			array(
				'{p1}' => 'first',
				'{p2}' => 'second'
			)
		);
		$this->assertEquals(
			sprintf(
				'http://localhost:%d?param1=first&param2=second',
				self::CURL_TEST_PORT
			),
			$uri
		);
	}

	/**
	 * Tests the "buildUrl" helper method.
	 */
	public function testHttpRequestBuildUrl()
	{
		$url_parts = NostoHttpRequest::parseUrl(
			sprintf(
				'http://localhost:%d/tmp/?param1=first&param2=second#fragment1=test',
				self::CURL_TEST_PORT
			)
		);
		$url = NostoHttpRequest::buildUrl($url_parts);
		$this->assertEquals(
			sprintf(
				'http://localhost:%d/tmp/?param1=first&param2=second#fragment1=test',
				self::CURL_TEST_PORT
			),
			$url
		);
	}

	/**
	 * Tests the "parseQueryString" helper method.
	 */
	public function testHttpRequestParseQueryString()
	{
		$query_string_parts = NostoHttpRequest::parseQueryString('param1=first&param2=second');
		$this->assertArrayHasKey('param1', $query_string_parts);
		$this->assertContains('first', $query_string_parts);
		$this->assertArrayHasKey('param2', $query_string_parts);
		$this->assertContains('second', $query_string_parts);
	}

	/**
	 * Tests the "replaceQueryParamInUrl" helper method.
	 */
	public function testHttpRequestReplaceQueryParamInUrl()
	{
		$url = NostoHttpRequest::replaceQueryParamInUrl(
			'param1',
			'replaced_first',
			sprintf(
				'http://localhost:%d/tmp/?param1=first&param2=second',
				self::CURL_TEST_PORT
			)
		);
		$this->assertEquals(
			sprintf(
				'http://localhost:%d/tmp/?param1=replaced_first&param2=second',
				self::CURL_TEST_PORT
			),
			$url
		);
	}

	/**
	 * Tests the "replaceQueryParam" helper method.
	 */
	public function testHttpRequestReplaceQueryParam()
	{
		$query_string = NostoHttpRequest::replaceQueryParam(
			'param2',
			'replaced_second',
			'param1=first&param2=second'
		);
		$this->assertEquals(
			'param1=first&param2=replaced_second',
			$query_string
		);
	}

	/**
	 * Tests the http request response result.
	 */
	public function testHttpRequestResponseResult()
	{
		$response = new NostoHttpResponse(array(), json_encode(array('test' => true)));
		$this->assertEquals('{"test":true}', $response->getResult());
		$result = $response->getJsonResult(true);
		$this->assertArrayHasKey('test', $result);
		$this->assertContains(true, $result);
	}

	/**
	 * Tests the http request response error message.
	 */
	public function testHttpRequestResponseErrorMessage()
	{
		$response = new NostoHttpResponse(array(), '', 'error');
		$this->assertEquals('error', $response->getMessage());
	}

	/**
	 * Tests the http request curl adapter.
	 */
	public function testHttpRequestCurlAdapter()
	{
		$request = new NostoHttpRequest(new NostoHttpRequestAdapterCurl(NostoHttpRequest::$userAgent));
		$request->setUrl('http://localhost:3000');
		$response = $request->get();
		$this->assertEquals(404, $response->getCode());
		$response = $request->post('test');
		$this->assertEquals(404, $response->getCode());
		$request->setUrl(
			sprintf(
				'http://localhost:%d',
				self::CURL_TEST_PORT
			)
		);
		$response = $request->get();
		$this->assertEquals(
			sprintf(
				'Failed to connect to localhost port %d: Connection refused',
				self::CURL_TEST_PORT
			),
			$response->getMessage()
		);
	}

	/**
	 * Tests the http request socket adapter.
	 */
	public function testHttpRequestSocketAdapter()
	{
		$request = new NostoHttpRequest(new NostoHttpRequestAdapterSocket(NostoHttpRequest::$userAgent));
		$request->setUrl('http://localhost:3000');
		$response = $request->get();
		$this->assertEquals(404, $response->getCode());
		$response = $request->post('test');
		$this->assertEquals(404, $response->getCode());
	}
}
