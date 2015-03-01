<?php
/**
 * HttpClient.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Twitter!
 * @subpackage	common
 * @since		5.0
 *
 * @date		01.03.15
 */

namespace IPub\Twitter;

use Nette;

use IPub;
use IPub\Twitter\Exceptions;

interface HttpClient
{
	/**
	 * @param Api\Request $request
	 *
	 * @return Api\Response
	 *
	 * @throws Exceptions\ApiException
	 */
	function makeRequest(Api\Request $request);
}