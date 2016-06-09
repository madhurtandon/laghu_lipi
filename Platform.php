<?php

/**
 * @author tushar.kant@kayako.com <tushar.kant@kayako.com>
 *
 * Class Platform
 */
class Platform
{
	// const DOMAIN = 'https://platform.kayako.com/api/index.php?/v1/';
	// const DOMAIN = 'http://platform.kayakodev.net/api/index.php?/v1/';
	// const DOMAIN = 'http://platform.kayakostage.internal/api/index.php?/v1/';
	// const DOMAIN = 'http://platform/api/index.php?/v1/'; // this is for vagrant

	// Pass in the KEY and SECRET stored in apikeys table on Platform
	const KEY    = '';
	const SECRET = '';

	/** @var array */
	protected $headers;

	/** @var string */
	protected $url;

	/** @var string */
	protected $method;

	/** @var string */
	protected $body;

	static protected $signedHeaders = [
		'content-disposition',
		'content-md5',
		'if-none-match',
		'if-unmodified-since',
		'x-api-key',
		'x-http-method-override',
		'x-nonce',
		'x-portal',
		'x-session-id',
		'x-timestamp',
		'x-token',
	];

	const METHOD_GET    = 'GET';
	const METHOD_POST   = 'POST';
	const METHOD_PUT    = 'PUT';
	const METHOD_DELETE = 'DELETE';

	private $foreground_colors = [];
	private $background_colors = [];

	/**
	 * Color class constructor
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 */
	public function __construct()
	{
		// Set up shell colors
		$this->foreground_colors['black']        = '0;30';
		$this->foreground_colors['dark_gray']    = '1;30';
		$this->foreground_colors['blue']         = '0;34';
		$this->foreground_colors['light_blue']   = '1;34';
		$this->foreground_colors['green']        = '0;32';
		$this->foreground_colors['light_green']  = '1;32';
		$this->foreground_colors['cyan']         = '0;36';
		$this->foreground_colors['light_cyan']   = '1;36';
		$this->foreground_colors['red']          = '0;31';
		$this->foreground_colors['light_red']    = '1;31';
		$this->foreground_colors['purple']       = '0;35';
		$this->foreground_colors['light_purple'] = '1;35';
		$this->foreground_colors['brown']        = '0;33';
		$this->foreground_colors['yellow']       = '1;33';
		$this->foreground_colors['light_gray']   = '0;37';
		$this->foreground_colors['white']        = '1;37';

		$this->background_colors['black']      = '40';
		$this->background_colors['red']        = '41';
		$this->background_colors['green']      = '42';
		$this->background_colors['yellow']     = '43';
		$this->background_colors['blue']       = '44';
		$this->background_colors['magenta']    = '45';
		$this->background_colors['cyan']       = '46';
		$this->background_colors['light_gray'] = '47';
	}

	/**
	 * Returns colored string
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param $string
	 * @param $foreground_color
	 * @param $background_color
	 *
	 * @return string
	 */
	public function getColoredString($string, $foreground_color = null, $background_color = null)
	{
		$colored_string = "";

		// Check if given foreground color found
		if (isset($this->foreground_colors[$foreground_color])) {
			$colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . "m";
		}
		// Check if given background color found
		if (isset($this->background_colors[$background_color])) {
			$colored_string .= "\033[" . $this->background_colors[$background_color] . "m";
		}

		// Add string and end coloring
		$colored_string .= $string . "\033[0m" . "\n";

		return $colored_string;
	}

	/**
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * Returns all foreground color names
	 */
	public function getForegroundColors()
	{
		return array_keys($this->foreground_colors);
	}

	/**
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * Returns all background color names
	 */
	public function getBackgroundColors()
	{
		return array_keys($this->background_colors);
	}

	/**
	 * @author Amarjeet Kaur <amarjeet.kaur@kayako.com>
	 *
	 * @param string $url
	 * @param int    $method
	 *
	 * @return Platform
	 */
	public function SetUrlMethod($url, $method)
	{
		$this->SetURL($url)
			 ->SetMethod($method);

		return $this;
	}

	/**
	 * @author Amarjeet Kaur <amarjeet.kaur@kayako.com>
	 *
	 * @param int $method
	 *
	 * @return SWIFT_APIClient
	 */
	static public function IsValidMethod($method)
	{
		return in_array($method, [self::METHOD_GET, self::METHOD_POST, self::METHOD_PUT, self::METHOD_DELETE]);
	}

	/**
	 * @author Amarjeet Kaur <amarjeet.kaur@kayako.com>
	 *
	 * @param string $url
	 *
	 * @return SWIFT_APIClient
	 */
	protected function SetURL($url)
	{
		$this->url = $url;

		return $this;
	}

	/**
	 * @author Amarjeet Kaur <amarjeet.kaur@kayako.com>
	 *
	 * @param string $method
	 *
	 * @return SWIFT_APIClient
	 */
	protected function SetMethod($method)
	{
		if (!self::IsValidMethod($method)) {
			var_dump('Not a valid method... ');
			exit();
		}

		$this->method = $method;

		return $this;
	}

	/**
	 * @author Parminder Singh
	 *
	 * @param string $key
	 * @param string $secret
	 * @param string $data (OPTIONAL)
	 *
	 * @return self
	 */
	public function SetKAuth($key, $secret, $data = '')
	{
		if (empty($key) || empty($secret)) {
			echo $this->getColoredString("Key aur secret to daal de bhai...!\n", 'red');
			exit();
		}

		$this->headers['accept']       = '*/*';
		$this->headers['content-type'] = 'application/x-www-form-urlencoded';

		$this->headers['X-API-Key']   = $key;
		$this->headers['X-Timestamp'] = time();

		$this->headers['X-API-Signature'] = base64_encode(hash_hmac('sha256', $this->GetRequestData($data), $secret, true));

		return $this;
	}

	/**
	 * @author Amarjeet Kaur <amarjeet.kaur@kayako.com>
	 * @return string
	 */
	public function GetURL()
	{
		return $this->url;
	}

	/**
	 * @author Amarjeet Kaur <amarjeet.kaur@kayako.com>
	 * @return string
	 */
	public function GetMethod()
	{
		return $this->method;
	}

	/**
	 * @author Parminder Singh <parminder.singh@kayako.com>
	 *
	 * @return array
	 */
	public function GetHeaders()
	{
		return $this->headers;
	}

	/**
	 * @author Amarjeet Kaur <amarjeet.kaur@kayako.com>
	 *
	 * @param array $data (OPTIONAL)
	 *
	 * @return array
	 */
	public function Process($data = [])
	{
		$method = $this->GetMethod();

		if ($method == self::METHOD_POST && empty($data)) {
			var_dump('If you are using POST then why the hell are you sending empty data :(');
			exit();
		}

		$curlResource = curl_init();

		curl_setopt($curlResource, CURLOPT_URL, $this->GetURL());
		curl_setopt($curlResource, CURLOPT_TIMEOUT, 120);
		curl_setopt($curlResource, CURLOPT_CONNECTTIMEOUT, 120);
		curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curlResource, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($curlResource, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curlResource, CURLOPT_SSL_VERIFYPEER, 0);
		//		curl_setopt($curlResource, CURLOPT_VERBOSE, true);  // If you want to debug the output

		switch ($method) {
			case self::METHOD_GET:
			case self::METHOD_DELETE:

				break;

			case self::METHOD_PUT:
			case self::METHOD_POST:

				if (!empty($data)) {
					curl_setopt($curlResource, CURLOPT_POST, 1);
					curl_setopt($curlResource, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
				}

				break;

			default:
				break;
		}

		$headers = [];
		if (!empty($this->headers)) {
			ksort($this->headers);
			foreach ($this->headers as $key => $val) {
				$headers[] = strtolower($key) . ': ' . $val;
			}
		}

		curl_setopt($curlResource, CURLOPT_HTTPHEADER, $headers);

		$jsonResponse = curl_exec($curlResource);

		curl_close($curlResource);

		$response = json_decode($jsonResponse, true);

		return $response;
	}

	/**
	 *
	 * @author Parminder Singh <parminder.singh@kayako.com>
	 *
	 * @param string $requestBody (OPTIONAL)
	 *
	 * @return string
	 */
	protected function GetRequestData($requestBody = '')
	{
		// HTTP method (e.g. get)
		$requestData = strtolower($this->GetMethod()) . "\n";

		// Base URL
		$URL = parse_url($this->GetURL(), PHP_URL_QUERY);
		parse_str($URL, $queryParameters);

		$queryString = '';
		if (!empty($queryParameters)) {
			foreach (array_keys($queryParameters) as $query) {
				if (strstr($query, "/")) {
					$queryString = $query;

					unset($queryParameters[$queryString]);
					break;
				}
			}
		}

		if (!empty($queryString)) {
			$requestData .= $queryString . ".json\n";
		}

		// HTTP headers (e.g. Content-Length: ...)
		$headers    = $this->GetHeaders();
		$newHeaders = [];
		if (!empty($headers)) {
			foreach ($headers as $headerName => $values) {
				$headerName = strtolower($headerName);
				if (!in_array($headerName, self::$signedHeaders)) {
					continue;
				}

				$newHeaders[$headerName] = $values;
			}
			ksort($newHeaders);
			foreach ($newHeaders as $headerName => $value) {
				$requestData .= $headerName . ': ' . $value . "\n";
			}
		}

		// Query arguments (e.g. _method=put&batch=true&...)
		$arguments = [];
		foreach ($queryParameters as $argumentName => $value) {
			$arguments[strtolower($argumentName)] = $value;
		}

		ksort($arguments);
		$argumentPairs = [];
		foreach ($arguments as $argumentName => $value) {
			$argumentPairs[] = $argumentName . '=' . urlencode($value);
		}
		$requestData .= implode('&', $argumentPairs) . "\n";

		// And, finally, the request body
		if (!empty($requestBody)) {
			$requestBody = http_build_query($requestBody, '', '&');
		}

		$requestData .= $requestBody;

		return $requestData;
	}

	/**
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param $url
	 * @param $returnData
	 *
	 * @return mixed
	 */
	public function httpGet($url, $returnData = false)
	{
		$url = self::DOMAIN . $url;

		$ApiClient = $this->SetUrlMethod($url, self::METHOD_GET);

		echo $this->getColoredString("\nGET: Requested URL - $url", "purple");

		$ApiClient->SetKAuth(self::KEY, self::SECRET);
		$response = $ApiClient->Process();

		if (!$returnData) {
			echo $this->getColoredString("\nOUTPUT:::: ", "light_purple");
			print_r($response);
//			echo(json_encode($response));
		} else {
			return $response;
		}

		return null;
	}

	/**
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param $url
	 * @param $params
	 *
	 * @return mixed
	 */
	function httpPost($url, $params = [])
	{
		$url = self::DOMAIN . $url;

		echo $this->getColoredString("\nPOST: Requested URL - $url\n", "purple");

		$ApiClient = $this->SetUrlMethod($url, self::METHOD_POST);

		$ApiClient->SetKAuth(self::KEY, self::SECRET, $params);
		$response = $ApiClient->Process($params);
		echo $this->getColoredString("\nParameters:::: \n" . print_r($params, true), "light_purple");
		echo $this->getColoredString("\nOUTPUT:::: \n", "light_purple");

		print_r($response);
	}

	/**
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param       $url
	 * @param array $params
	 *
	 * @return mixed
	 */
	function httpPut($url, $params = [])
	{
		$url = self::DOMAIN . $url;

		echo $this->getColoredString("\nPUT: Requested URL - $url\n", "purple");

		$ApiClient = $this->SetUrlMethod($url, self::METHOD_PUT);

		if (empty($params)) {
			$ApiClient->SetKAuth(self::KEY, self::SECRET);
		} else {
			$ApiClient->SetKAuth(self::KEY, self::SECRET, $params);
		}
		$response = $ApiClient->Process($params);
		echo $this->getColoredString("\nParameters:::: \n" . print_r($params, true), "light_purple");
		echo $this->getColoredString("\nOUTPUT:::: \n", "light_purple");

		print_r($response);
	}

	/**
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param $url
	 *
	 * @return mixed
	 */
	function httpDelete($url)
	{
		$url = self::DOMAIN . $url;

		echo $this->getColoredString("\nDELETE: Requested URL - $url", "purple");

		$ApiClient = $this->SetUrlMethod($url, self::METHOD_DELETE);

		$ApiClient->SetKAuth(self::KEY, self::SECRET);
		$response = $ApiClient->Process();

		echo $this->getColoredString("\nOUTPUT:::: ", "light_purple");

		print_r($response);
	}

	/**
	 *
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param int    $instanceId
	 * @param string $action
	 * @param array  $options
	 *
	 * @return mixed
	 */
	public function InstanceActions($instanceId, $action, $options = [])
	{
		if (isset($instanceId) && is_numeric($instanceId) && isset($action) && !empty($action)) {
			switch ($action) {
				case 'getall':
					return $this->httpGet('instances');
					break;

				case 'get':
					return $this->httpGet('instances/' . $instanceId);
					break;

				case 'enable':
					return $this->httpPut('instances/' . $instanceId . '/enable');
					break;

				case 'disable':
					return $this->httpPut('instances/' . $instanceId . '/disable');
					break;

				case 'upgrade':
					return $this->httpPut('instances/' . $instanceId . '/upgrade');
					break;

				case 'delete':
					return $this->httpDelete('instances/' . $instanceId . '&forcefully=true');
					// return $this->httpDelete('instances/' . $instanceId);
					break;

				case 'rename':
					if (isset($options['newAppDomain'])) {
						return $this->httpPost('instances/' . $instanceId . '/domain', ['app_domain' => $options['newAppDomain']]);
					}
					break;

				case 'credential':
					if (isset($options['username']) && isset($options['password'])) {
						return $this->httpPut('instances/' . $instanceId . '/credential', ['username' => $options['username'], 'password' => $options['password']]);
					}
					break;

				case 'clone':
					return $this->httpPost("instances/$instanceId/copy", ['app_domain'   => $options['app_domain'], 'is_migration' => $options['is_migration'],
																		  'country_code' => $options['country_code']]);
					break;

				case 'update':
					$update_options = ['elastic_search_cluster_id' => $options['elastic_search_cluster_id'],
									   'redis_shard_id'            => $options['redis_shard_id'],
									   'redirect_to_alias_id'      => $options['redirect_to_alias_id']];

					foreach ($update_options as $update_key => $update_value) {
						if (empty($update_value)) {
							unset($update_options[$update_key]);
						}
					}

					return $this->httpPut("instances/$instanceId", $update_options);
					break;

				case 'config':
					return $this->httpPost("instances/$instanceId/config", $options);
					break;

				case 'reindex':
					return $this->httpPost("instances/$instanceId/reindex", ['test' => 'hello']);
					break;

				default:
					die('Please pass proper action to perform instance basic action.');
					break;
			}
		}

		echo $this->getColoredString("\nAction not available!!", "red");
	}

	/**
	 *
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param int    $instanceId
	 * @param string $action
	 * @param int    $aliasID
	 * @param array  $params
	 *
	 * @return mixed
	 */
	public function InstanceAliasActions($instanceId, $action, $aliasID = 1, $params = [])
	{
		if (isset($instanceId) && is_numeric($instanceId) && isset($action) && !empty($action)) {
			$parameters = [];
			if (isset($params['app_domain'])) {
				$parameters = array_merge($parameters, ['app_domain' => $params['app_domain']]);
			}

			if (isset($params['cname'])) {
				$parameters = array_merge($parameters, ['cname' => $params['cname']]);
			}

			if (isset($params['certificate'])) {
				$parameters = array_merge($parameters, ['certificate' => $params['certificate']]);
			}

			if (isset($params['private_key'])) {
				$parameters = array_merge($parameters, ['private_key' => $params['private_key']]);
			}

			switch ($action) {
				case 'getallaliases':
					return $this->httpGet('instances/' . $instanceId . '/aliases');
					break;

				case 'get':
					return $this->httpGet('instances/' . $instanceId . '/aliases/' . $aliasID);
					break;

				case 'create':
					return $this->httpPost('instances/' . $instanceId . '/aliases', $parameters);
					break;

				case 'update':
					return $this->httpPut('instances/' . $instanceId . '/aliases/' . $aliasID, $parameters);
					break;

				case 'delete':
					return $this->httpDelete('instances/' . $instanceId . '/aliases/' . $aliasID);
					break;

				case 'deleteall':
					return $this->httpDelete('instances/' . $instanceId . '/aliases');
					break;

				default:
					die('Please pass proper action to perform instance basic action.');
					break;
			}
		}

		echo $this->getColoredString("\nAction not available!!", "red");
	}

	/**
	 *
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param int   $instanceId
	 * @param array $params
	 *
	 * @return mixed
	 */
	public function UpdateLicense($instanceId, $params)
	{
		$parameters = [];
		if (isset($params['type'])) {
			$parameters = array_merge($parameters, ['type' => $params['type']]);
		}

		if (isset($params['company'])) {
			$parameters = array_merge($parameters, ['company' => $params['company']]);
		}
		if (isset($params['full_name'])) {
			$parameters = array_merge($parameters, ['full_name' => $params['full_name']]);
		}

		if (isset($params['plan'])) {
			$parameters = array_merge($parameters, ['plan' => $params['plan']]);
		}

		if (isset($params['package'])) {
			$parameters = array_merge($parameters, ['package' => $params['package']]);
		}

		if (isset($params['seat_count'])) {
			$parameters = array_merge($parameters, ['seat_count' => $params['seat_count']]);
		}

		if (isset($params['expiry'])) {
			$parameters = array_merge($parameters, ['expiry' => $params['expiry']]);
		}

		if (isset($params['upgrade'])) {
			$parameters = array_merge($parameters, ['upgrade' => $params['upgrade']]);
		}

		if (isset($params['policy'])) {
			$parameters = array_merge($parameters, ['policy' => $params['policy']]);
		}

		if (!empty($parameters)) {
			return $this->httpPut('instances/' . $instanceId . '/license', $parameters);
		}

		echo $this->getColoredString("\nAction not available!!", "red");
	}

	/**
	 * Backup related operations
	 * 1. RetriveAll
	 * 2. RetriveAnBackup
	 * 3. BackupAnInstance
	 * 4. DeleteABackup
	 * 5. DeleteAllBackup
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param int    $instanceId
	 * @param string $action
	 * @param array  $param
	 *
	 * @return mixed
	 */
	public function InstanceBackup(
		$instanceId, $action = 'RetriveAllBackup', $param = ['backupid' => 1, 'backuptype' => 'FULL', 'backupexpiry' => false])
	{
		if (isset($instanceId) && is_numeric($instanceId)) {
			switch ($action) {
				case 'RetriveAllBackup':
					return $this->httpGet('instances/' . $instanceId . '/backups');
					break;

				case 'RetriveAnBackup':
					return $this->httpGet('instances/' . $instanceId . '/backups/' . $param['backupid']);
					break;

				case 'BackupAnInstance':
					if ($param['backupexpiry']) {
						return $this->httpPost('instances/' . $instanceId . '/backups/', ['type' => $param['backuptype'], 'expiry' => $param['backupexpiry']]);
					}

					return $this->httpPost('instances/' . $instanceId . '/backups/', ['type' => $param['backuptype']]);
					break;

				case 'DeleteABackup':
					return $this->httpDelete('instances/' . $instanceId . '/backups/' . $param['backupid']);
					break;

				case 'DeleteAllBackup':
					return $this->httpDelete('instances/' . $instanceId . '/backups');
					break;

				default:
					die('Please pass proper action to perform backup task.');
					break;
			}
		}

		echo $this->getColoredString("\nAction not available!!", "red");
	}

	/**
	 * Backup related operations
	 * 1. RetriveStatus
	 * 2. MoveAnInstance
	 * 3. CancelAnInstance
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param int    $instanceId
	 * @param string $action
	 * @param int    $podId
	 * @param int    $include_patches
	 *
	 * @return mixed
	 */
	public function InstanceMove($instanceId, $action = 'RetriveStatus', $podId = 1, $include_patches = false)
	{
		if (isset($instanceId) && is_numeric($instanceId)) {
			switch ($action) {
				case 'RetriveMoveStatus':
					return $this->httpGet('instances/' . $instanceId . '/move');
					break;

				case 'MoveAnInstance':
					if ($include_patches) {
						return $this->httpPost('instances/' . $instanceId . '/move', ['pod_id' => $podId, 'include_patches' => $include_patches]);
					} else {
						return $this->httpPost('instances/' . $instanceId . '/move', ['pod_id' => $podId]);
					}

					break;

				case 'CancelAnInstance':
					return $this->httpDelete('instances/' . $instanceId . '/move');
					break;

				default:
					die('Please pass proper action to perform backup task.');
					break;
			}
		}

		echo $this->getColoredString("\nAction not available!!", "red");
	}

	/**
	 *
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param int    $instanceId
	 * @param string $action
	 * @param array  $args
	 *
	 * @return mixed
	 */
	public function DomainActions($instanceId, $action = 'RetrieveDomains', $args = [])
	{
		if (isset($instanceId) && is_numeric($instanceId)) {
			switch ($action) {
				case 'RetrieveDomains':
					return $this->httpGet('instances/' . $instanceId . '/domain');
					break;

				case 'DeleteAHistoricalDomain':
					if (isset($args['domain_id'])) {
						return $this->httpDelete('instances/' . $instanceId . '/domain/' . $args['domain_id']);
					}
					break;

				case 'DeleteAllHistoricalDomains':
					return $this->httpDelete('instances/' . $instanceId . '/domains');
					break;
			}
		}
	}

	/**
	 *
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param int    $instanceId
	 * @param string $dump_url
	 *
	 * @return mixed
	 */
	public function ImportDatabase($instanceId, $dump_url)
	{
		return $this->httpPost("instances/$instanceId/import", ['dump_url' => $dump_url]);
	}

	/**
	 *
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param string   $argument
	 * @param int|bool $id
	 *
	 * @return mixed
	 */
	public function Check($argument, $id = false)
	{
		if (!$id) {
			return $this->httpGet($argument);
		}

		return $this->httpGet($argument . '/' . $id);
	}

	/**
	 *
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param string      $appDomain
	 * @param string|bool $masterDomain
	 * @param string|bool $returnData
	 *
	 * @return mixed
	 */
	public function CheckAvailability($appDomain, $masterDomain = false, $returnData = false)
	{
		if (!$masterDomain) {
			return $this->httpGet('instances/available&app_domain=' . $appDomain, $returnData);
		}

		return $this->httpGet('instances/available&app_domain=' . $appDomain . '&master_domain=' . $masterDomain, $returnData);
	}

	/**
	 *
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param string $action
	 * @param string $region_id
	 * @param string $regionName
	 * @param string $countryCode
	 *
	 * @return mixed
	 */
	public function RegionActions($action = 'AddRegion', $region_id = '', $regionName = '', $countryCode = '')
	{
		switch ($action) {
			case 'AddRegion':
				return $this->httpPost('regions', ['name' => $regionName, 'country_code' => $countryCode]);
				break;

			case 'RetrieveAllRegion':
				return $this->httpGet('regions');
				break;

			case 'RetrieveARegion':
				return $this->httpGet('regions/' . $region_id);
				break;

			case 'DeleteARegion':
				return $this->httpDelete('regions/' . $region_id);
				break;
		}

		echo $this->getColoredString("\nAction not available!!", "red");
	}

	/**
	 *
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param string $action
	 * @param string $location_id
	 * @param string $location_name
	 * @param string $region_id
	 *
	 * @return mixed
	 */
	public function LocationActions($action = 'RetrieveAllLocation', $location_id = '', $location_name = '', $region_id = '')
	{
		switch ($action) {
			case 'AddLocation':
				return $this->httpPost('locations', ['name' => $location_name, 'region_id' => $region_id]);
				break;

			case 'RetrieveAllLocation':
				return $this->httpGet('locations');
				break;

			case 'RetrieveALocation':
				return $this->httpGet('locations/' . $location_id);
				break;

			case 'DeleteALocation':
				return $this->httpDelete('locations/' . $location_id);
				break;
		}

		echo $this->getColoredString("\nAction not available!!", "red");
	}

	/**
	 *
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param string $action
	 * @param string $provider_id
	 * @param string $providerName
	 *
	 * @return mixed
	 */
	public function ProviderActions($action = 'AddProvider', $provider_id = '', $providerName = '')
	{
		switch ($action) {
			case 'AddProvider':
				return $this->httpPost('providers', ['name' => $providerName]);
				break;

			case 'RetrieveAllProvider':
				return $this->httpGet('providers');
				break;

			case 'RetrieveAProvider':
				return $this->httpGet('providers/' . $provider_id);
				break;

			case 'DeleteAProvider':
				return $this->httpDelete('providers/' . $provider_id);
				break;
		}

		echo $this->getColoredString("\nAction not available!!", "red");
	}

	/**
	 *
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param string $action
	 * @param string $pod_name
	 * @param string $pod_type
	 * @param string $pod_environment
	 * @param string $region_id
	 * @param string $enable_ondemand_routing
	 * @param string $enable_trial_routing
	 * @param string $seat_cap
	 * @param string $customer_cap
	 * @param string $version
	 * @param string $country_code
	 * @param string $cdn_url
	 * @param string $state
	 *
	 * @return mixed
	 */
	public function PodActions(
		$action = 'RetrieveAllPods', $pod_id = '', $pod_name = '', $pod_type = '', $pod_environment = '', $region_id = '', $enable_ondemand_routing = '',
		$enable_trial_routing = '',
		$seat_cap = '', $customer_cap = '', $version = '', $country_code = '', $cdn_url = '', $state = '', $option = ''
	)
	{
		switch ($action) {
			case 'RetrieveAllPods':
				return $this->httpGet('pods');
				break;

			case 'RetrieveAPod':
				return $this->httpGet("pods/$pod_id");
				break;

			case 'RetrieveAllServersInAPod':
				return $this->httpGet("pods/$pod_id/servers");
				break;

			case 'RetrieveAllInstancesInAPod':
				return $this->httpGet("pods/$pod_id/instances");
				break;

			case 'RetrieveStatisticsOfAllInstancesInAPod':
				return $this->httpGet("pods/$pod_id/statistics");
				break;

			case 'AddPod':
				$add_pod_array = ['name'                    => $pod_name, 'type' => $pod_type, 'environment' => $pod_environment, 'region_id' => $region_id,
								  'enable_ondemand_routing' => $enable_ondemand_routing, 'enable_trial_routing' => $enable_trial_routing,
								  'seat_cap'                => $seat_cap, 'customer_cap' => $customer_cap, 'cdn_url' => $cdn_url];

				foreach ($add_pod_array as $add_pod_key => $add_pod_value) {
					if (empty($add_pod_value)) {
						unset($add_pod_array[$add_pod_key]);
					}
				}

				return $this->httpPost('pods', $add_pod_array);
				break;

			case 'UpdatePod':
				return $this->httpPut('pods/' . $pod_id, ['enable_ondemand_routing' => $enable_ondemand_routing,
														  'enable_trial_routing'    => $enable_trial_routing]);

				break;

			case 'DeletePod':
				return $this->httpDelete('pods/' . $pod_id);

				break;

			case 'DeleteCacheOfAPod':
				return $this->httpDelete("pods/$pod_id/cache");

				break;

			case 'ResetConfigOfAllInstancesOnThisPod':
				return $this->httpPost("pods/$pod_id/config", ['option' => $option]);
				break;

			case 'UpdatePodState':
				return $this->httpPut("pods/$pod_id/maintenance", ['state' => $state]);
				break;

			case 'LeastUsedPod':
				return $this->httpGet('pods/available&type=' . $pod_type . '&version=' . $version . '&country_code=' . $country_code);

				break;

			case 'RetrieveStatsOfPod':
				return $this->httpGet('pods/' . $pod_id . '/statistics');

				break;
		}

		echo $this->getColoredString("\nAction not available!!", "red");
	}

	/**
	 *
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param string $action
	 * @param string $shard_name
	 * @param string $pod_id
	 * @param string $root_username
	 *
	 * @return mixed
	 */
	public function ShardActions($action = 'RetrieveAllShards', $shard_id = '', $shard_name = '', $pod_id = '', $root_username = '')
	{
		switch ($action) {
			case 'AddShard':
				return $this->httpPost('shards', ['name' => $shard_name, 'pod_id' => $pod_id, 'root_username' => $root_username]);
				break;

			case 'RetrieveAllShards':
				return $this->httpGet('shards');
				break;

			case 'RetrieveAShard':
				return $this->httpGet('shards/' . $shard_id);
				break;

			case 'RetrieveAllServersInShard':
				return $this->httpGet('shards/' . $shard_id . '/servers');
				break;

			case 'SyncAShard':
				return $this->httpPut('shards/' . $shard_id . '/sync');
				break;

			case 'DeleteAShard':
				return $this->httpDelete('shards/' . $shard_id);
				break;
		}

		echo $this->getColoredString("\nAction not available!!", "red");
	}

	/**
	 *
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param string $action
	 * @param string $server_id
	 * @param string $server_token
	 * @param string $server_name
	 * @param string $server_type
	 * @param string $pod_id
	 * @param string $location_id
	 * @param string $provider_id
	 * @param string $public_ip
	 * @param string $private_ip
	 * @param string $shard_id
	 * @param string $parent_id
	 * @param string $server_role
	 * @param string $server_service
	 * @param string $server_is_enabled
	 * @param string $hostname
	 *
	 * @return mixed
	 */
	public function ServerActions(
		$action = 'RetrieveAllServer', $server_id = '', $server_token = '', $server_name = '', $server_type = '', $pod_id = '',
		$location_id = '', $provider_id = '', $public_ip = '', $private_ip = '', $shard_id = '', $parent_id = '',
		$server_role = '', $server_service = '', $server_is_enabled = '', $hostname = '')
	{
		switch ($action) {
			case 'AddServer':
				$add_server_array = ['name'        => $server_name, 'type' => $server_type, 'pod_id' => $pod_id, 'location_id' => $location_id,
									 'provider_id' => $provider_id, 'public_ip' => $public_ip, 'private_ip' => $private_ip, 'hostname' => $hostname,
									 'shard_id'    => $shard_id, 'parent_id' => $parent_id, 'role' => $server_role, 'service' => $server_service,
									 'is_enabled'  => $server_is_enabled];

				foreach ($add_server_array as $add_server_key => $add_server_value) {
					if (empty($add_server_value)) {
						unset($add_server_array[$add_server_key]);
					}
				}

				return $this->httpPost('servers', $add_server_array);
				break;

			case 'RetrieveAllServer':
				return $this->httpGet('servers');
				break;

			case 'RetrieveAServer':
				return $this->httpGet('servers/' . $server_id);
				break;


			case 'GetConfigOfAServer':
				return $this->httpGet('servers/' . $server_id . '/config');
				break;

			case 'GetTokenOfAServer':
				return $this->httpPut('servers/' . $server_id . '/token');
				break;

			case 'ProvisionAServer':
				return $this->httpPost('servers/provision', ['token' => $server_token]);
				break;

			case 'EnableAServer':
				return $this->httpPut('servers/' . $server_id . '/enable');
				break;

			case 'DisableAServer':
				return $this->httpPut('servers/' . $server_id . '/disable');
				break;

			case 'SyncAServer':
				return $this->httpPut('servers/' . $server_id . '/sync');
				break;

			case 'DeleteAServer':
				return $this->httpDelete('servers/' . $server_id);
				break;
		}

		echo $this->getColoredString("\nAction not available!!", "red");
	}

	/**
	 *
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param $industry
	 * @param $app_domain
	 * @param $version
	 * @param $company
	 * @param $email
	 * @param $password
	 * @param $full_name
	 * @param $plan
	 * @param $package
	 * @param $expiry
	 * @param $seat_count
	 * @param $type               (OPTIONAL)
	 * @param $master_domain      (OPTIONAL)
	 * @param $country_code       (OPTIONAL)
	 * @param $parent_instance_id (OPTIONAL)
	 *
	 * @return mixed
	 */
	public function CreateInstance(
		$industry, $app_domain, $version, $company, $email, $password, $full_name, $plan, $package, $expiry, $seat_count,
		$type = 'TRIAL', $master_domain = 'kayako.com', $country_code = 'US', $parent_instance_id = false)
	{
		$add_instance_array = ['version'      => $version, 'industry' => $industry,
							   'country_code' => $country_code, 'type' => $type, 'app_domain' => $app_domain,
							   'company'      => $company, 'email' => $email, 'password' => $password,
							   'full_name'    => $full_name, 'master_domain' => $master_domain, 'parent_instance_id' => $parent_instance_id,
							   'license'      => ['plan' => $plan, 'package' => $package, 'expiry' => $expiry, 'seat_count' => $seat_count]];

		foreach ($add_instance_array as $add_instance_key => $add_instance_value) {
			if (empty($add_instance_value)) {
				unset($add_instance_array[$add_instance_key]);
			}
		}

		if ($type == 'SANDBOX' && !$parent_instance_id) {
			die("\nparent_instance_id is must if type is SANDBOX\n");
		}

		return $this->httpPost('instances', $add_instance_array);
	}

	/**
	 *
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param string $action
	 * @param string $service_type (elasticsearch|redis)
	 * @param array  $args         (OPTIONAL)
	 *
	 * @return mixed
	 */
	public function ServiceActions($action = 'RetrieveAll', $service_type = 'elasticsearch', $args = [])
	{
		switch ($action) {
			case 'RetrieveAll':
				return $this->httpGet("$service_type");
				break;

			case 'RetrieveAService':
				return $this->httpGet($service_type . '/' . $args['service_id']);
				break;

			case 'AddANewService':
				return $this->httpPost("$service_type", ['name' => $args['service_name'], 'pod_id' => $args['pod_id'], 'port' => $args['port']]);
				break;

			case 'UpdateAService':
				$service_update_array = ['name' => isset($args['service_name']) ? $args['service_name'] : '',
										 'port' => isset($args['port']) ? $args['port'] : ''];

				foreach ($service_update_array as $service_key => $service_value) {
					if (empty($service_value)) {
						unset($service_update_array[$service_key]);
					}
				}

				return $this->httpPut("$service_type/" . $args['service_id'], $service_update_array);
				break;

			case 'EnableAService':
				return $this->httpPut("$service_type/" . $args['service_id'] . "/enable");
				break;

			case 'DisableAService':
				return $this->httpPut("$service_type/" . $args['service_id'] . "/disable");
				break;

			case 'DeleteAService':
				return $this->httpDelete("$service_type/" . $args['service_id']);
				break;

			default:
				break;
		}
	}

	/**
	 *
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param string $action
	 * @param string $service_type (elasticsearch|redis)
	 * @param array  $args         (OPTIONAL)
	 *
	 * @return mixed
	 */
	public function ServiceMembershipActions($action = 'RetrieveAll', $service_type = 'elasticsearch', $args = [])
	{
		switch ($action) {
			case 'RetrieveAll':
				return $this->httpGet("$service_type/" . $args['service_id'] . "/memberships");
				break;

			case 'AddANewMember':
				return $this->httpPost($service_type . '/' . $args['service_id'] . '/memberships', ['server_id' => $args['server_id']]);
				break;

			case 'DeleteAMember':
				return $this->httpDelete("$service_type/" . $args['service_id'] . "/memberships/" . $args['server_id']);
				break;

			case 'DeleteAllMember':
				return $this->httpDelete("$service_type/" . $args['service_id'] . "/memberships");
				break;

			default:
				break;
		}
	}

	/**
	 *
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 *
	 * @param $plan_name
	 * @param $package_link
	 *
	 * @return mixed
	 */
	public function SyncBuild($plan_name, $package_link)
	{
		return $this->httpPost('synchroniser/legacy', ['plan_name' => $plan_name, 'package_link' => $package_link]);
	}

	/**
	 * Waits for input.
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 */
	public function GetInput()
	{
		echo $this->getColoredString("\nType 'Yes' to continue: ", "light_green");
		$handle = fopen("php://stdin", "r");
		$line   = fgets($handle);
		if (strtolower(trim($line)) != 'yes') {
			echo $this->getColoredString("\nABORTING!", "red", "black");
			exit;
		}

		echo $this->getColoredString("\nDhaynawaad, continuing...", "light_cyan", "black");
	}

	/**
	 * Gets List of servers from platform servers table
	 *
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 */
	public function GetServersList()
	{
		return $this->httpGet('servers=&fields=type,public_ip,private_ip,hostname,is_enabled,is_provisioned,pod&limit=99999');
	}
}

// Get the object of class platform
$Platform = new Platform();

/**
 * Single colon represents the argument is required
 * Double colon represents the argument is optional
 * Shorthand operators are just single characters, pass them using single "-"
 * longopts are consits of a word and to pass them we need to use "--" instead of "-"
 */

$shortopts = "";
$shortopts .= "m:";

$longopts = [
	"instanceid::", "option::", "checkid::", "action::", "newAppDomain::", "full_name::", "plan::", "seat_count::", "package::", "expiry::", "backupid::", "backuptype::", "backupexpiry::",
	"pod_id::", "app_domain::", "master_domain::", "alias_id::", "cname::", "certificate::", "private_key::", "username::", "password::", "type::", "company::", "region_name::",
	"country_code::", "provider_name::", "pod_name::", "pod_type::", "pod_environment::", "region_id::", "enable_ondemand_routing::", "enable_trial_routing::", "seat_cap::",
	"customer_cap::", "version::", "shard_id::", "shard_name::", "root_username::", "server_id::", "server_name::", "server_type::", "location_id::", "provider_id::", "hostname::",
	"public_ip::", "private_ip::", "parent_id::", "server_role::", "server_service::", "server_is_enabled::", "server_token::", "location_name::", "upgrade::", "industry::", "email::",
	"parent_instance_id::", "plan_name::", "package_link::", "build_id::", "is_migration::", "service_type::", "service_id::", "port::", "service_name::", "redirect_to_alias_id::", "redis_shard_id::",
	"elastic_search_cluster_id::", "state::"
];


$options = getopt($shortopts, $longopts);
//echo "\nParamerter passed\n";
//print_r($options);
//echo "\n";

switch ($options["m"]) {
	case 'InstanceActions':
		if (isset($options['newAppDomain'])) {
			echo $Platform->InstanceActions($options['instanceid'], $options['action'], ['newAppDomain' => $options['newAppDomain']]);
		} else if (isset($options['username']) && isset($options['password'])) {
			echo $Platform->InstanceActions($options['instanceid'], $options['action'], ['username' => $options['username'], 'password' => $options['password']]);
		} else if (isset($options['app_domain']) && isset($options['is_migration']) && isset($options['country_code'])) {
			echo $Platform->InstanceActions($options['instanceid'], $options['action'], ['app_domain'   => $options['app_domain'], 'is_migration' => $options['is_migration'],
																						 'country_code' => $options['country_code']]);
		} else if (isset($options['option'])) {
			echo $Platform->InstanceActions($options['instanceid'], $options['action'], ['option' => $options['option']]);
		} else {
			echo $Platform->InstanceActions($options['instanceid'], $options['action'], ['elastic_search_cluster_id' => isset($options['elastic_search_cluster_id']) ? $options['elastic_search_cluster_id'] : '',
																						 'redis_shard_id'            => isset($options['redis_shard_id']) ? $options['redis_shard_id'] : '',
																						 'redirect_to_alias_id'      => isset($options['redirect_to_alias_id']) ? $options['redirect_to_alias_id'] : '']);
		}
		break;

	case 'UpdateLicense':
		echo $Platform->UpdateLicense($options["instanceid"], $options);
		break;

	case 'InstanceBackup':
		echo $Platform->InstanceBackup($options["instanceid"], $options['action'], ['backupid'     => isset($options['backupid']) ? $options['backupid'] : 1,
																					'backuptype'   => isset($options['backuptype']) ? $options['backuptype'] : 'FULL',
																					'backupexpiry' => isset($options['backupexpiry']) ? $options['backupexpiry'] : false]);
		break;

	case 'InstanceMove':
		echo $Platform->InstanceMove($options["instanceid"], $options['action'], (isset($options['pod_id']) ? $options['pod_id'] : 1), (isset($options['include_patches']) ? $options['include_patches'] : false));
		break;

	case 'Check':
		echo $Platform->Check($options['option'], isset($options['checkid']) ? $options['checkid'] : null);
		break;

	case 'CheckAvailability':
		if (isset($options['master_domain'])) {
			echo $Platform->CheckAvailability($options['app_domain'], $options['master_domain']);
		} else {
			echo $Platform->CheckAvailability($options['app_domain']);
		}
		break;

	case 'InstanceAlias':
		echo $Platform->InstanceAliasActions($options["instanceid"], $options['action'], isset($options['alias_id']) ? $options['alias_id'] : 1, $options);
		break;

	case 'RegionActions':
		switch ($options['action']) {
			case 'AddRegion':
				if (isset($options['region_name']) && isset($options['country_code'])) {
					echo $Platform->RegionActions($options['action'], 0, $options['region_name'], $options['country_code']);
				} else {
					die('Bhai saari fields daalni padegi plzz pass kar do.. https://kayako.atlassian.net/wiki/display/OPS/Regions#Regions-Addanewregion');
				}
				break;

			case 'RetrieveAllRegion':
				echo $Platform->RegionActions($options['action']);
				break;

			case 'RetrieveARegion':
				if ($options['region_id']) {
					echo $Platform->RegionActions($options['action'], $options['region_id']);
				} else {
					die('Bhai saab region_id to pass kar do!! https://kayako.atlassian.net/wiki/display/OPS/Regions#Regions-Retrievearegion');
				}
				break;

			case 'DeleteARegion':
				if ($options['region_id']) {
					echo $Platform->RegionActions($options['action'], $options['region_id']);
				} else {
					die('Bhai saab region_id to pass kar do!! https://kayako.atlassian.net/wiki/display/OPS/Regions#Regions-Deletearegion');
				}
				break;
		}

		break;

	case 'ProviderActions':
		switch ($options['action']) {
			case "AddProvider":
				if (isset($options['provider_name'])) {
					echo $Platform->ProviderActions($options['action'], 0, $options['provider_name']);
				} else {
					die('Bhai saab provider_name to pass kar do!! https://kayako.atlassian.net/wiki/display/OPS/Providers#Providers-Addanewprovider');
				}
				break;

			case "RetrieveAllProvider":
				echo $Platform->ProviderActions($options['action']);
				break;

			case "RetrieveAProvider":
				if ($options['provider_id']) {
					echo $Platform->ProviderActions($options['action'], $options['provider_id']);
				} else {
					die('Bhai saab provider_id to pass kar do!! https://kayako.atlassian.net/wiki/display/OPS/Providers#Providers-Retrieveaprovider');
				}
				break;

			case "DeleteAProvider":
				if ($options['provider_id']) {
					echo $Platform->ProviderActions($options['action'], $options['provider_id']);
				} else {
					die('Bhai saab provider_id to pass kar do!! https://kayako.atlassian.net/wiki/display/OPS/Providers#Providers-Deleteaprovider');
				}
				break;

			default:
				die('Action pass karna bhool gye???');
				break;
		}

		break;

	case 'LocationActions':
		switch ($options['action']) {
			case "AddLocation":
				if (isset($options['location_name']) && isset($options['region_id'])) {
					echo $Platform->LocationActions($options['action'], 0, $options['location_name'], $options['region_id']);
				} else {
					die('Bhai saab location_name & region_id to pass kar do!! https://kayako.atlassian.net/wiki/display/OPS/Locations#Locations-Addanewlocation');
				}
				break;

			case "RetrieveAllLocation":
				echo $Platform->LocationActions($options['action']);
				break;

			case "RetrieveALocation":
				if ($options['location_id']) {
					echo $Platform->LocationActions($options['action'], $options['location_id']);
				} else {
					die('Bhai saab location_id to pass kar do!! https://kayako.atlassian.net/wiki/display/OPS/Locations#Locations-Retrievealocation');
				}
				break;

			case "DeleteALocation":
				if ($options['location_id']) {
					echo $Platform->LocationActions($options['action'], $options['location_id']);
				} else {
					die('Bhai saab location_id to pass kar do!! https://kayako.atlassian.net/wiki/display/OPS/Locations#Locations-Deletealocation');
				}
				break;

			default:
				die('Action pass karna bhool gye???');
				break;
		}

		break;

	case 'PodActions':
		switch ($options['action']) {
			case "AddPod":
				if (isset($options['pod_name']) && isset($options['pod_type']) && isset($options['pod_environment']) && isset($options['region_id'])) {
					echo $Platform->PodActions("AddPod", 0, $options['pod_name'], $options['pod_type'], $options['pod_environment'], $options['region_id'],
											   isset($options['enable_ondemand_routing']) ? $options['enable_ondemand_routing'] : '',
											   isset($options['enable_trial_routing']) ? $options['enable_trial_routing'] : '',
											   isset($options['seat_cap']) ? $options['seat_cap'] : '',
											   isset($options['customer_cap']) ? $options['customer_cap'] : '',
											   isset($options['version']) ? $options['version'] : '',
											   isset($options['country_code']) ? $options['country_code'] : '');
				} else {
					die('Bhai mandatory values pass karni hi padegi..kar do fatafat.. => https://kayako.atlassian.net/wiki/display/OPS/Pods#Pods-Addanewpod');
				}

				break;

			case "UpdatePod":
				if (isset($options['pod_id']) && isset($options['enable_ondemand_routing']) && isset($options['enable_trial_routing'])) {
					echo $Platform->PodActions("UpdatePod", $options['pod_id'], '', '', '', '',
											   $options['enable_ondemand_routing'], $options['enable_trial_routing']);
				} else {
					die('Bhai pod_id, enable_ondemand_routing & enable_trial_routing to daal do.. https://kayako.atlassian.net/wiki/display/OPS/Pods#Pods-Updateapod');
				}

				break;

			case "DeletePod":
				if (isset($options['pod_id'])) {
					echo $Platform->PodActions("DeletePod", $options['pod_id']);
				} else {
					die('Bhai pod_id to daal do.. https://kayako.atlassian.net/wiki/display/OPS/Pods#Pods-Deleteapod');
				}

				break;

			case "LeastUsedPod":
				if (isset($options['pod_type']) && isset($options['version']) && isset($options['country_code'])) {
					echo $Platform->PodActions("LeastUsedPod", 0, '', $options['pod_type'], '', '', '', '', '', '', $options['version'], $options['country_code']);
				} else {
					die('Bhai type, version & country_code to daal do.. https://kayako.atlassian.net/wiki/display/OPS/Pods#Pods-Retrieveleastusedpod');
				}

				break;

			case "RetrieveStatsOfPod":
				if (isset($options['pod_id'])) {
					echo $Platform->PodActions("RetrieveStatsOfPod", $options['pod_id']);
				} else {
					die('Bhai pod_id to daal do.. https://kayako.atlassian.net/wiki/display/OPS/Pods#Pods-Retrievestatisticsofallinstancesinapod');
				}

				break;

			case 'UpdatePodState':
				if (isset($options['pod_id']) && isset($options['state'])) {
					echo $Platform->PodActions('UpdatePodState', $options['pod_id'], 'name', 'type', 'env','region', 'on','trial','seat','cus','ver','cc', 'cdn', $options['state']);
				} else {
					die('Bhai pod_id aur state to daal do.. https://kayako.atlassian.net/wiki/display/MICROSERVICES/Pods#Pods-Updatepodstate');
				}
				break;

			default:
				die('Action pass karna bhool gye???');
				break;
		}
		break;

	case "ShardActions":
		switch ($options['action']) {
			case "AddShard":
				if (isset($options['shard_name']) && isset($options['pod_id']) && isset($options['root_username'])) {
					echo $Platform->ShardActions("AddShard", 0, $options['shard_name'], $options['pod_id'], $options['root_username']);
				} else {
					die('Bhai shard_name, pod_id & root_username to pass kar do - https://kayako.atlassian.net/wiki/display/OPS/Shards#Shards-Addanewshard');
				}
				break;

			case "RetrieveAllShards":
				echo $Platform->ShardActions("RetrieveAllShards");
				break;

			case "RetrieveAShard":
				if (isset($options['shard_id'])) {
					echo $Platform->ShardActions("RetrieveAShard", $options['shard_id']);
				} else {
					die('Bhai shard_id pass kar do - https://kayako.atlassian.net/wiki/display/OPS/Shards#Shards-Retrieveanexistingshard');
				}
				break;

			case "RetrieveAllServersInShard":
				if (isset($options['shard_id'])) {
					echo $Platform->ShardActions("RetrieveAllServersInShard", $options['shard_id']);
				} else {
					die('Bhai shard_id pass kar do - https://kayako.atlassian.net/wiki/display/OPS/Shards#Shards-Retrieveallserversinashard');
				}
				break;

			case "SyncAShard":
				if (isset($options['shard_id'])) {
					echo $Platform->ShardActions("SyncAShard", $options['shard_id']);
				} else {
					die('Bhai shard_id pass kar do - https://kayako.atlassian.net/wiki/display/OPS/Shards#Shards-Syncashard');
				}
				break;

			case "DeleteAShard":
				if (isset($options['shard_id'])) {
					echo $Platform->ShardActions("DeleteAShard", $options['shard_id']);
				} else {
					die('Bhai shard_id pass kar do - https://kayako.atlassian.net/wiki/display/OPS/Shards#Shards-Deleteashard');
				}
				break;
		}
		break;

	case "ServerActions":
		switch ($options['action']) {
			case "AddServer":
				if (isset($options['server_name']) && isset($options['server_type']) && isset($options['pod_id']) &&
					isset($options['location_id']) && isset($options['provider_id']) && isset($options['public_ip'])
				) {
					if ($options['server_type'] == 'DATABASE' && $options['server_role'] == 'SLAVE' && !isset($options['parent_id'])) {
						die('parent_id is needed if server type is database and role is slave');
					} else {
						echo $Platform->ServerActions($options['action'], 0, '', $options['server_name'], $options['server_type'], $options['pod_id'],
													  $options['location_id'], $options['provider_id'], $options['public_ip'],
													  isset($options['private_ip']) ? $options['private_ip'] : '',
													  isset($options['shard_id']) ? $options['shard_id'] : '',
													  isset($options['parent_id']) ? $options['parent_id'] : '',
													  isset($options['server_role']) ? $options['server_role'] : '',
													  isset($options['server_service']) ? $options['server_service'] : '',
													  isset($options['server_is_enabled']) ? $options['server_is_enabled'] : '',
													  isset($options['hostname']) ? $options['hostname'] : '');
					}
				} else {
					die('Bhai mandatory values pass karni hi padegi - https://kayako.atlassian.net/wiki/display/OPS/Servers#Servers-Addanewserver');
				}
				break;

			case "RetrieveAllServer":
				echo $Platform->ServerActions($options['action']);
				break;

			case "RetrieveAServer":
				if (isset($options['server_id'])) {
					echo $Platform->ServerActions($options['action'], $options['server_id']);
				} else {
					die('Bhai server_id pass kar do - https://kayako.atlassian.net/wiki/display/OPS/Servers#Servers-Retrieveaserver');
				}
				break;

			case "GetConfigOfAServer":
				if (isset($options['server_id'])) {
					echo $Platform->ServerActions($options['action'], $options['server_id']);
				} else {
					die('Bhai server_id pass kar do - https://kayako.atlassian.net/wiki/display/OPS/Servers#Servers-Getconfigofaserver');
				}
				break;

			case "GetTokenOfAServer":
				if (isset($options['server_id'])) {
					echo $Platform->ServerActions($options['action'], $options['server_id']);
				} else {
					die('Bhai server_id pass kar do - https://kayako.atlassian.net/wiki/display/OPS/Servers#Servers-Gettokenofaserver');
				}
				break;

			case "ProvisionAServer":
				if (isset($options['server_token'])) {
					echo $Platform->ServerActions($options['action'], 0, $options['server_token']);
				} else {
					die('Bhai server_token pass kar do - https://kayako.atlassian.net/wiki/display/OPS/Servers#Servers-Provisionaserver');
				}
				break;

			case "EnableAServer":
				if (isset($options['server_id'])) {
					echo $Platform->ServerActions($options['action'], $options['server_id']);
				} else {
					die('Bhai server_id pass kar do - https://kayako.atlassian.net/wiki/display/OPS/Servers#Servers-Enableaserver');
				}
				break;

			case "DisableAServer":
				if (isset($options['server_id'])) {
					echo $Platform->ServerActions($options['action'], $options['server_id']);
				} else {
					die('Bhai server_id pass kar do - https://kayako.atlassian.net/wiki/display/OPS/Servers#Servers-Disableaserver');
				}
				break;

			case "SyncAServer":
				if (isset($options['server_id'])) {
					echo $Platform->ServerActions($options['action'], $options['server_id']);
				} else {
					die('Bhai server_id pass kar do - https://kayako.atlassian.net/wiki/display/OPS/Servers#Servers-Syncaserver');
				}
				break;

			case "DeleteAServer":
				if (isset($options['server_id'])) {
					echo $Platform->ServerActions($options['action'], $options['server_id']);
				} else {
					die('Bhai server_id pass kar do - https://kayako.atlassian.net/wiki/display/OPS/Servers#Servers-Delete/Terminateaserver');
				}
				break;

			case "ServerList":
				echo $Platform->GetServersList();
				break;
		}
		break;

	case "CreateInstance":
		echo $Platform->CreateInstance($options['industry'], $options['app_domain'], $options['version'], $options['company'], $options['email'],
									   $options['password'], $options['full_name'], $options['plan'], $options['package'], $options['expiry'], $options['seat_count'],
									   $options['type'], $options['master_domain'], $options['country_code'], $options['parent_instance_id']);
		break;

	case "ReInstallInstance":
		$instance = $Platform->CheckAvailability($options['app_domain'], $options['master_domain'], true);
		echo $Platform->getColoredString("Instance details: ", "green");
		$instance = $instance['data'];
		print_r($instance);
		echo $Platform->getColoredString("\nDelete karne jaa ra hu instance...", "yellow");
		$Platform->InstanceActions($instance['id'], 'delete');
		echo $Platform->getColoredString("\nKripiya dharyapurvak intezar kare aur logs check kare, till the delete completes then enter 'Yes', takes max 2-3mins", "light_green");
		$Platform->GetInput();
		$Platform->CreateInstance($instance['industry'], $instance['app_domain'], $instance['version'], $instance['company'], $instance['email'],
								  '', $instance['full_name'], $instance['plan'], $instance['package'], strtotime($instance['expiry']), $instance['seat_count'],
								  $instance['type'], $instance['master_domain'], $instance['pod']['region']['country_code']);
		break;

	case "Synchroniser":
		echo $Platform->SyncBuild($options['plan_name'], $options['package_link']);

		break;

	case "ServiceActions":
		switch ($options['action']) {
			case "RetrieveAll":
				echo $Platform->ServiceActions($options['action'], $options['service_type']);
				break;

			case "RetrieveAService":
				echo $Platform->ServiceActions($options['action'], $options['service_type'], ['service_id' => $options['service_id']]);
				break;

			case "AddANewService":
				echo $Platform->ServiceActions($options['action'], $options['service_type'], ['service_name' => $options['service_name'], 'pod_id' => $options['pod_id'], 'port' => $options['port']]);
				break;

			case "UpdateAService":
				$service_update_array = ['service_name' => isset($options['service_name']) ? $options['service_name'] : '',
										 'port'         => isset($options['port']) ? $options['port'] : ''];

				foreach ($service_update_array as $service_key => $service_value) {
					if (empty($service_value)) {
						unset($service_update_array[$service_key]);
					}
				}

				echo $Platform->ServiceActions($options['action'], $options['service_type'], $service_update_array);
				break;

			case "EnableAService":
				echo $Platform->ServiceActions($options['action'], $options['service_type'], ['service_id' => $options['service_id']]);
				break;

			case "DisableAService":
				echo $Platform->ServiceActions($options['action'], $options['service_type'], ['service_id' => $options['service_id']]);
				break;

			case "DeleteAService":
				echo $Platform->ServiceActions($options['action'], $options['service_type'], ['service_id' => $options['service_id']]);
				break;
		}
		break;

	case "ServiceMembershipActions":
		switch ($options['action']) {
			case "RetrieveAll":
				echo $Platform->ServiceMembershipActions($options['action'], $options['service_type'], ['service_id' => $options['service_id']]);
				break;

			case "AddANewMember":
				echo $Platform->ServiceMembershipActions($options['action'], $options['service_type'], ['service_id' => $options['service_id'], 'server_id' => $options['server_id']]);
				break;

			case "DeleteAMember":
				echo $Platform->ServiceMembershipActions($options['action'], $options['service_type'], ['service_id' => $options['service_id'], 'server_id' => $options['server_id']]);
				break;

			case "DeleteAllMember":
				echo $Platform->ServiceMembershipActions($options['action'], $options['service_type'], ['service_id' => $options['service_id']]);
				break;
		}
		break;

	default:
		echo $Platform->getColoredString("Method does not exists..", 'red');
		break;
}
