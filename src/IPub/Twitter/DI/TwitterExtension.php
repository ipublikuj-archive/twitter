<?php
/**
 * TwitterExtension.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Twitter!
 * @subpackage	DI
 * @since		5.0
 *
 * @date		24.05.13
 */

namespace IPub\Twitter\DI;

use Nette;
use Nette\DI;
use Nette\Utils;
use Nette\PhpGenerator as Code;

use Tracy;

use IPub;
use IPub\Twitter;

class TwitterExtension extends DI\CompilerExtension
{
	/**
	 * Extension default configuration
	 *
	 * @var array
	 */
	protected $defaults = [
		'appKey' => NULL,
		'appSecret' => NULL,
		'permission' => 'read',          // read/write/delete
		'clearAllWithLogout' => TRUE,
		'curlOptions' => [],
		'debugger' => '%debugMode%',
	];

	public function __construct()
	{
		// Apply default curl options from api
		$this->defaults['curlOptions'] = IPub\Twitter\Api\CurlClient::$defaultCurlOptions;
	}

	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		Utils\Validators::assert($config['appKey'], 'string', 'Application key');
		Utils\Validators::assert($config['appSecret'], 'string', 'Application secret');
		Utils\Validators::assert($config['permission'], 'string', 'Application permission');

		$builder->addDefinition($this->prefix('client'))
			->setClass('IPub\Twitter\Client');

		$builder->addDefinition($this->prefix('config'))
			->setClass('IPub\Twitter\Configuration', [
				$config['appKey'],
				$config['appSecret'],
			])
			->addSetup('$permission', [$config['permission']]);

		foreach ($config['curlOptions'] as $option => $value) {
			if (defined($option)) {
				unset($config['curlOptions'][$option]);
				$config['curlOptions'][constant($option)] = $value;
			}
		}

		$httpClient = $builder->addDefinition($this->prefix('httpClient'))
			->setClass('IPub\Twitter\Api\CurlClient')
			->addSetup('$service->curlOptions = ?;', [$config['curlOptions']]);

		$builder->addDefinition($this->prefix('session'))
			->setClass('IPub\Twitter\SessionStorage');

		if ($config['clearAllWithLogout']) {
			$builder->getDefinition('user')
				->addSetup('$sl = ?; ?->onLoggedOut[] = function () use ($sl) { $sl->getService(?)->clearAll(); }', array(
					'@container', '@self', $this->prefix('session')
				));
		}

/*
		$api = $builder->addDefinition($this->prefix('api'))
				->setClass('IPub\Extensions\Social\Twitter\TwitterOAuth', array(
					$config['consumerKey'],
					$config['consumerSecret']
				))
				->setInject(FALSE);

		if (isset($config['accessToken']) && isset($config['accessTokenSecret'])) {
			$api->addSetup('setOAuthToken', array(
				$config['accessToken'],
				$config['accessTokenSecret']
			));
		}

		$builder->addDefinition($this->prefix('authenticator.storage'))
			->setClass('IPub\Extensions\Social\Twitter\SessionStorage')
			->setFactory(get_called_class() . '::createSessionStorage', array('@session', $config['authenticator.sessionNamespace']));
*/
	}

	/**
	 * @param Nette\Configurator $config
	 * @param string $extensionName
	 */
	public static function register(Nette\Configurator $config, $extensionName = 'twitter')
	{
		$config->onCompile[] = function (Nette\Configurator $config, Nette\DI\Compiler $compiler) use ($extensionName) {
			$compiler->addExtension($extensionName, new TwitterExtension());
		};
	}
}