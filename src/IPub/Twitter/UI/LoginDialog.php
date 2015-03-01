<?php
/**
 * LoginDialog.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Twitter!
 * @subpackage	UI
 * @since		5.0
 *
 * @date		01.03.15
 */

namespace IPub\Twitter\UI;

use Nette;
use Nette\Application;
use Nette\Http;

use IPub;
use IPub\Twitter;
use IPub\Twitter\Exceptions;

/**
 * Component that you can connect to presenter
 * and use as public mediator for Twitter OAuth redirects communication
 *
 * @package		iPublikuj:Twitter!
 * @subpackage	UI
 *
 * @method onResponse(LoginDialog $dialog)
 */
class LoginDialog extends Application\UI\Control
{
	/**
	 * @var array of function(LoginDialog $dialog)
	 */
	public $onResponse = [];

	/**
	 * @var Twitter\Client
	 */
	protected $client;

	/**
	 * @var Twitter\Configuration
	 */
	protected $config;

	/**
	 * @var Twitter\SessionStorage
	 */
	protected $session;

	/**
	 * @var Http\UrlScript
	 */
	protected $currentUrl;

	/**
	 * @param Twitter\Client $twitter
	 */
	public function __construct(Twitter\Client $twitter)
	{
		$this->client = $twitter;
		$this->config = $twitter->getConfig();
		$this->session = $twitter->getSession();
		$this->currentUrl = $twitter->getCurrentUrl();

		parent::__construct();

		$this->monitor('Nette\Application\IPresenter');
	}

	/**
	 * @return Twitter\Client
	 */
	public function getClient()
	{
		return $this->client;
	}

	/**
	 * @param Nette\ComponentModel\Container $obj
	 */
	protected function attached($obj)
	{
		parent::attached($obj);

		if ($obj instanceof Nette\Application\IPresenter) {
			$this->currentUrl = new Http\UrlScript($this->link('//response!'));
		}
	}

	public function handleCallback()
	{

	}

	/**
	 * Checks, if there is a user in storage and if not, it redirects to login dialog.
	 * If the user is already in session storage, it will behave, as if were redirected from twitter right now,
	 * this means, it will directly call onResponse event.
	 *
	 * @throws Nette\Application\AbortException
	 */
	public function handleOpen()
	{
		if (!$this->client->getUser()) { // no user
			$this->open();
		}

		$this->onResponse($this);
		$this->presenter->redirect('this');
	}

	/**
	 * @throws Nette\Application\AbortException
	 * @throws Exceptions\RequestFailedException
	 */
	public function open()
	{
		if ($this->client->obtainRequestToken((string) $this->currentUrl)) {
			$this->presenter->redirectUrl($this->getUrl());

		} else {
			throw new Exceptions\RequestFailedException('User could not be authenticated.', 'twitter');
		}
	}

	/**
	 * @return array
	 */
	public function getQueryParams()
	{
		// CSRF
		$this->client->getSession()->establishCSRFTokenState();

		$params = [
			'oauth_token' => $this->session->request_token,
			'perms' => $this->config->permission
		];

		return $params;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return (string) $this->config->createUrl('oauth', 'authorize', $this->getQueryParams());
	}

	public function handleResponse()
	{
		$this->client->getUser(); // check the received parameters and save user
		$this->onResponse($this);
		$this->presenter->redirect('this', ['state' => NULL, 'code' => NULL]);
	}
}