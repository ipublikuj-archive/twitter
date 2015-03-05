<?php
/**
 * Test: IPub\Twitter\Configuration
 * @testCase
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Twitter!
 * @subpackage	Tests
 * @since		5.0
 *
 * @date		05.03.15
 */

namespace IPubTests\Twitter;

use Nette;

use Tester;
use Tester\Assert;

use IPub;
use IPub\Twitter;

require_once __DIR__ . '/../bootstrap.php';

class ConfigurationTest extends Tester\TestCase
{
	/**
	 * @var Twitter\Configuration
	 */
	private $config;

	protected function setUp()
	{
		$this->config = new Twitter\Configuration('123', 'abc');
	}

	public function testCreateUrl()
	{
		Assert::match('https://api.twitter.com/1.1/account/verify_credentials.json', (string) $this->config->createUrl('api', 'account/verify_credentials.json'));

		Assert::match('https://api.twitter.com/oauth/access_token?oauth_consumer_key=123&oauth_signature_method=HMAC-SHA1', (string) $this->config->createUrl('oauth', 'access_token', array(
			'oauth_consumer_key' => $this->config->appKey,
			'oauth_signature_method' => 'HMAC-SHA1'
		)));

		Assert::match('https://api.twitter.com/oauth/request_token?oauth_consumer_key=123&oauth_signature_method=HMAC-SHA1', (string) $this->config->createUrl('oauth', 'request_token', array(
			'oauth_consumer_key' => $this->config->appKey,
			'oauth_signature_method' => 'HMAC-SHA1'
		)));
	}
}

\run(new ConfigurationTest());