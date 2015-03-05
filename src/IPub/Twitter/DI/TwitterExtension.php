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
		'debugger' => '%debugMode%',
	];

	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		Utils\Validators::assert($config['appKey'], 'string', 'Application key');
		Utils\Validators::assert($config['appSecret'], 'string', 'Application secret');
		Utils\Validators::assert($config['permission'], 'string', 'Application permission');

		// Create oAuth consumer
		$consumer = new IPub\OAuth\Consumer($config['appKey'], $config['appSecret']);

		$builder->addDefinition($this->prefix('client'))
			->setClass('IPub\Twitter\Client', [$consumer]);

		$builder->addDefinition($this->prefix('config'))
			->setClass('IPub\Twitter\Configuration', [
				$config['appKey'],
				$config['appSecret'],
			])
			->addSetup('$permission', [$config['permission']]);

		$builder->addDefinition($this->prefix('session'))
			->setClass('IPub\Twitter\SessionStorage');

		$builder->addDefinition($this->prefix('signature.hmacsha1twtapi'))
			->setClass('IPub\Twitter\Signature\HMAC_SHA1_TWTAPI')
			->addTag(IPub\OAuth\DI\OAuthExtension::TAG_SIGNATURE_METHOD);

		if ($config['clearAllWithLogout']) {
			$builder->getDefinition('user')
				->addSetup('$sl = ?; ?->onLoggedOut[] = function () use ($sl) { $sl->getService(?)->clearAll(); }', [
					'@container', '@self', $this->prefix('session')
				]);
		}
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