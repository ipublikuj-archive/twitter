<?php
/**
 * HMAC_SHA1_TWTAPI.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Twitter!
 * @subpackage	Signature
 * @since		5.0
 *
 * @date		02.03.15
 */

namespace IPub\Twitter\Signature;

use IPub;
use IPub\OAuth;
use IPub\OAuth\Utils;

/**
 * The HMAC-SHA1 signature method uses the HMAC-SHA1 signature algorithm as defined in [RFC2104]
 * where the Signature Base String is the text and the key is the concatenated values (each first
 * encoded per parameter Encoding) of the Consumer Secret and Token Secret, separated by an '&'
 * character (ASCII code 38) even if empty.
 *
 * @package		iPublikuj:OAuth!
 * @subpackage	Signature
 */
class HMAC_SHA1_TWTAPI extends OAuth\Signature\HMAC_SHA1
{
	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return "HMAC-SHA1-TWTAPI";
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildSignature($baseString, OAuth\Consumer $consumer, OAuth\Token $token = NULL)
	{
		return Utils\Url::urlEncodeRFC3986(parent::buildSignature($baseString, $consumer, $token));
	}
}