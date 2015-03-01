<?php
/**
 * Twitter OAuth2 session storage interface
 *   - iInterface of storage for save information while authentization
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Framework!
 * @subpackage	Social extensions
 * @since		5.0
 *
 * @date		24.05.13
 */

namespace IPub\Extensions\Social\Twitter;

interface IStorage
{
	/**
	 * Application authorizated
	 */
	public function isAuthorized();

	public function setAuthorized();

	/**
	 * oAuth Token
	 */
	public function getOAuthTokenKey();

	public function getOAuthTokenSecret();

	public function setOAuthTokens($key, $secret);

	/**
	 * Clean data in storage
	 */
	public function clean();
}
