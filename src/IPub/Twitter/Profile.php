<?php
/**
 * Profile.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Twitter!
 * @subpackage	common
 * @since		5.0
 *
 * @date		04.03.15
 */

namespace IPub\Twitter;

use Nette;
use Nette\Utils;

use IPub;
use IPub\Twitter\Exceptions;

class Profile extends Nette\Object
{
	/**
	 * @var Client
	 */
	private $twitter;

	/**
	 * @var string
	 */
	private $profileId;

	/**
	 * @var Utils\ArrayHash
	 */
	private $details;

	/**
	 * @param Client $twitter
	 * @param string $profileId
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	public function __construct(Client $twitter, $profileId = NULL)
	{
		$this->twitter = $twitter;

		if (is_numeric($profileId)) {
			throw new Exceptions\InvalidArgumentException("ProfileId must be a screen name of the account you're trying to read or NULL, which means actually logged in user.");
		}

		$this->profileId = $profileId;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		if ($this->profileId === NULL) {
			return $this->twitter->getUser();
		}

		return $this->profileId;
	}

	/**
	 * @param string $key
	 *
	 * @return Utils\ArrayHash|NULL
	 */
	public function getDetails($key = NULL)
	{
		if ($this->details === NULL) {
			try {

				if ($this->profileId !== NULL) {
					if (($result = $this->twitter->get('users/show.json', ['screen_name' => $this->profileId])) && ($result instanceof Utils\ArrayHash)) {
						$this->details = $result;
					}

				} else if ($user = $this->twitter->getUser()) {
					if (($result = $this->twitter->get('users/show.json', ['user_id' => $user])) && ($result instanceof Utils\ArrayHash)) {
						$this->details = $result;
					}

				} else {
					$this->details = new Utils\ArrayHash;
				}

			} catch (\Exception $e) {
				// todo: log?
			}
		}

		if ($key !== NULL) {
			return isset($this->details[$key]) ? $this->details[$key] : NULL;
		}

		return $this->details;
	}
}