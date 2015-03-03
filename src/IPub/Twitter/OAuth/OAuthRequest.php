<?php
/*
  The MIT License

  Copyright (c) 2007 Andy Smith

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files (the "Software"), to deal
  in the Software without restriction, including without limitation the rights
  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the Software is
  furnished to do so, subject to the following conditions:

  The above copyright notice and this permission notice shall be included in
  all copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
  THE SOFTWARE.

  Generic exception class
 */

namespace IPub\Extensions\Social\Twitter\Api;

class OAuthRequest
{

	protected $parameters;
	protected $http_method;
	protected $http_url;
	// for debug purposes
	public $base_string;
	public static $version = '1.0';
	public static $POST_INPUT = 'php://input';

	function __construct($http_method, $http_url, $parameters = NULL)
	{
		$parameters = ($parameters) ? $parameters : array();
		$parameters = array_merge(OAuthUtil::parse_parameters(parse_url($http_url, PHP_URL_QUERY)), $parameters);
		$this->parameters = $parameters;
		$this->http_method = $http_method;
		$this->http_url = $http_url;
	}

	/**
	 * attempt to build up a request from what was passed to the server
	 */
	public static function from_request($http_method = NULL, $http_url = NULL, $parameters = NULL)
	{
		$scheme = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on") ? 'http' : 'https';
		$http_url = ($http_url) ? $http_url : $scheme .
			'://' . $_SERVER['HTTP_HOST'] .
			':' .
			$_SERVER['SERVER_PORT'] .
			$_SERVER['REQUEST_URI'];
		$http_method = ($http_method) ? $http_method : $_SERVER['REQUEST_METHOD'];

		// We weren't handed any parameters, so let's find the ones relevant to
		// this request.
		// If you run XML-RPC or similar you should use this to provide your own
		// parsed parameter-list
		if (!$parameters) {
			// Find request headers
			$request_headers = OAuthUtil::get_headers();

			// Parse the query-string to find GET parameters
			$parameters = OAuthUtil::parse_parameters($_SERVER['QUERY_STRING']);

			// It's a POST request of the proper content-type, so parse POST
			// parameters and add those overriding any duplicates from GET
			if ($http_method == "POST" && isset($request_headers['Content-Type']) && strstr($request_headers['Content-Type'], 'application/x-www-form-urlencoded')
			) {
				$post_data = OAuthUtil::parse_parameters(
					file_get_contents(self::$POST_INPUT)
				);
				$parameters = array_merge($parameters, $post_data);
			}

			// We have a Authorization-header with OAuth data. Parse the header
			// and add those overriding any duplicates from GET or POST
			if (isset($request_headers['Authorization']) && substr($request_headers['Authorization'], 0, 6) == 'OAuth ') {
				$header_parameters = OAuthUtil::split_header(
					$request_headers['Authorization']
				);
				$parameters = array_merge($parameters, $header_parameters);
			}
		}

		return new OAuthRequest($http_method, $http_url, $parameters);
	}

	/**
	 * pretty much a helper function to set up the request
	 */
	public static function from_consumer_and_token($consumer, $token, $http_method, $http_url, $parameters = NULL)
	{

		return new OAuthRequest($http_method, $http_url, $parameters);
	}

	public function set_parameter($name, $value, $allow_duplicates = TRUE)
	{
		if ($allow_duplicates && isset($this->parameters[$name])) {
			// We have already added parameter(s) with this name, so add to the list
			if (is_scalar($this->parameters[$name])) {
				// This is the first duplicate, so transform scalar (string)
				// into an array so we can add the duplicates
				$this->parameters[$name] = array($this->parameters[$name]);
			}

			$this->parameters[$name][] = $value;
		} else {
			$this->parameters[$name] = $value;
		}
	}

	public function get_parameter($name)
	{
		return isset($this->parameters[$name]) ? $this->parameters[$name] : NULL;
	}

	public function get_parameters()
	{
		return $this->parameters;
	}

	public function unset_parameter($name)
	{
		unset($this->parameters[$name]);
	}

	/**
	 * builds the data one would send in a POST request
	 */
	public function to_postdata()
	{
		return OAuthUtil::build_http_query($this->parameters);
	}

	/**
	 * builds the Authorization: header
	 */
	public function to_header($realm = NULL)
	{
		$first = TRUE;
		if ($realm) {
			$out = 'Authorization: OAuth realm="' . OAuthUtil::urlencode_rfc3986($realm) . '"';
			$first = FALSE;
		}
		else
			$out = 'Authorization: OAuth';

		$total = array();
		foreach ($this->parameters as $k => $v) {
			if (substr($k, 0, 5) != "oauth")
				continue;
			if (is_array($v)) {
				throw new OAuthException('Arrays not supported in headers');
			}
			$out .= ($first) ? ' ' : ',';
			$out .= OAuthUtil::urlencode_rfc3986($k) .
				'="' .
				OAuthUtil::urlencode_rfc3986($v) .
				'"';
			$first = FALSE;
		}
		return $out;
	}
}