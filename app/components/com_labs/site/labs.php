<?php namespace Hubzero\Labs;

defined('JPATH_BASE') or die();

class HttpError extends \Exception
{
	private $httpCode;
	public function __construct($httpCode, $msg) {
		$this->httpCode = $httpCode;
		parent::__construct($msg);
	}
	public function getHttpCode() { return $this->httpCode; }
}

class BadRequestError extends HttpError
{
	public function __construct($msg = 'Bad Request') {
		parent::__construct(400, $msg);
	}
}

class NotFoundError extends HttpError
{
	public function __construct($msg = 'Not Found') {
		parent::__construct(404, $msg);
	}
}

class InternalServerError extends HttpError
{
	public function __construct($msg = 'Internal Server Error') {
		parent::__construct(500, $msg);
	}
}

class ServiceUnavailableError extends HttpError
{
	public function __construct($msg = 'Service Unavailable') {
		parent::__construct(503, $msg);
	}
}

class ForbiddenError extends HttpError
{
	public function __construct($msg = 'Forbidden') {
		parent::__construct(403, $msg);
	}
}

class DBO extends \PDO {
	private $prefix;

	public function __construct() {
		$conf = require JPATH_BASE.'/app/config/database.php';
		try {
			parent::__construct('mysql:host='.$conf['host'].';dbname='.$conf['db'], $conf['user'], $conf['password']);
			$attrs = [
				\PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
				\PDO::ATTR_AUTOCOMMIT         => false
			];
			array_walk($attrs, function($v, $k) {
				$this->setAttribute($k, $v);
			});
			$this->prefix = $conf['dbprefix'];
		}
		catch (\Exception $ex) {
			throw new InternalServerError('Failed to establish database connection');
		}
	}

	public function prepare($sql, $opts = []) {
		return parent::prepare(str_replace('#__', $this->prefix, $sql), $opts);
	}
}

class Controller {
	private $resp, $dbh, $user, $profile, $standalone = false;

	public function __construct() {
		$this->user = \JFactory::getUser();
		\JFactory::getDocument()->setTitle('Labs');
		$tasks = array_flip([
			'index', 'run', 'meta', 'admin', 'command', 'join'
		]);
		$task = 'index';
		$urlRest = '';
		if (preg_match('#^/labs/([-_a-z]+)(?:/(.*?))?$#', $_SERVER[isset($_SERVER['REDIRECT_SCRIPT_URL']) ? 'REDIRECT_SCRIPT_URL' : 'SCRIPT_URL'], $ma)) {
			if (!isset($tasks[$ma[1]])) {
				throw new NotFoundError;
			}
			$task = $ma[1];
			if (isset($ma[2])) {
				$urlRest = $ma[2];
			}
		}
		$this->dbh = new DBO;
		$this->$task(explode('/', $urlRest));
	}

	public function __destruct() {
		if ($this->resp[0] !== 'text/html') {
			exit();
		}
	}

	public function index() {
		header('Location: /resources/tools');
		exit();
	}

	public function command($rest) {
		$app = array_shift($rest);

		// get exported attribute keys
		$sth = $this->dbh->prepare(
			'SELECT l.id AS lab_id, title, login_scope, login_id, group_concat(ar.attribute separator \',\') AS attrs
			FROM #__labs l
			LEFT JOIN #__lab_attribute_release ar ON ar.lab_id = l.id
			WHERE l.published AND l.name = :name');
		$sth->execute([':name' => $app]);
		$attrList = $sth->fetch(\PDO::FETCH_ASSOC);

		// bail if not even a valid lab name
		if (!$attrList['lab_id']) {
			throw new NotFoundError;
		}

		if ($this->user->guest) {
			\JFactory::getApplication()->redirect('/login?return='.base64_encode(isset($_SERVER['REDIRECT_SCRIPT_URL']) ? $_SERVER['REDIRECT_SCRIPT_URL'] : $_SERVER['REDIRECT_URL']));
		}

		/// @TODO check app manager group
		if ($this->user->username != 'snyder13') {
			\JError::raiseError(403, 'Forbidden');
		}

		// get a proxy for the lab request
		$sth = $this->dbh->prepare('SELECT host, port, forward, shared_key FROM #__labs_proxy WHERE published AND lab_id = :lab_id ORDER BY ordering LIMIT 1');
		$sth->execute([':lab_id' => $attrList['lab_id']]);
		$proxy = $sth->fetch();
		// no proxy no serve-y
		if (!$proxy) {
			throw new ServiceUnavailableError;
		}

		// post attribute map to proxy
		$ch = curl_init();
		$copts = [
			\CURLOPT_URL            => 'http://'.$proxy['host'].':'.$proxy['port'].'/command/'.$rest[0],
			\CURLOPT_RETURNTRANSFER => true,
			\CURLOPT_HTTPHEADER     => [
				'X-HubPub-Shared-Key: '.$proxy['shared_key']
			]
		];
		curl_setopt_array($ch, $copts);
		$res = curl_exec($ch);

		$this->resp = ['text/plain', $res];
	}

	public function join($rest) {
		error_log('ocs join');
		$app = array_shift($rest);
		$sth = $this->dbh->prepare('SELECT transport_key, add_group FROM #__labs WHERE name = :app');
		$sth->execute([':app' => $app]);
		if (!($lab = $sth->fetch())) {
			error_log('lab not found: '.$app);
			throw new NotFoundError;
		}
		if (!isset($_POST['key']) || $_POST['key'] !== $lab['transport_key']) {
			error_log('key mismatch');
			throw new ForbiddenError;
		}
		$sth = $this->dbh->prepare('SELECT id FROM #__users WHERE username = :name LIMIT 1');
		$sth->execute([ ':name' => $_POST['username'] ]);
		$uid = null;
		if (($row = $sth->fetch())) {
			error_log('user exists');
			$uid = $row['id'];
		}
		else {
			error_log('create user');
			$usersConfig = Component::params('com_users');
			$newUsertype = $usersConfig->get('new_usertype');
			if (!$newUsertype) {
				$db = App::get('db');
				$query = $db->getQuery(true)
					->select('id')
					->from('#__usergroups')
					->where('title = "Registered"');
				$db->setQuery($query);
				$newUsertype = $db->loadResult();
			}
			error_log('new user type: '.$newUsertype);

                	$user = User::getInstance();
	                $user->set('id', 0);
        	        $user->set('accessgroups', [$newUsertype]);
                	$user->set('registerDate', Date::toSql());

	                $user->set('name', preg_replace('/\s+/', ' ', implode(' ', [$_POST['firstName'], $_POST['middleName'], $_POST['lastName']])));
                	$user->set('username', $_POST['username']);
                	$user->set('email', $_POST['email']);
		
			$user->set('givenName', $_POST['firstName']);
			$user->set('middleName', $_POST['middleName']);
			$user->set('surname', $_POST['lastName']);
			$user->set('activation', -rand(1, pow(2, 31)-1));
			$user->set('access', 1);
			$user->set('password', $_POST['password']);
			error_log('start save');
			$result = $user->save();
			error_log('end save');

			$user->set('password_clear', '');
			$user->set('password', '');

			if ($result) {
				error_log('change pw');
				$result = \Hubzero\User\Password::changePassword($user->get('id'), $_POST['password']);
				error_log('done with changing pw');

				// Set password back here in case anything else down the line is looking for it
				$user->set('password', $_POST['password']);
				$user->save();
			}
			else {
				error_log('bad result after save');
			}
			if ($result) {
				$uid = $user->get('id');
				error_log('ok, uid '.$uid);
			}
			else {
				error_log('bad result after changing pw');
			}
		}
		if ($lab['add_group'] && $uid) {
			error_log('join group, uid: '.$uid.', gid: '.$lab['add_group']);
			if (($group = \Hubzero\User\Group::getInstance($lab['add_group']))) {
				if (!in_array($uid, $group->get('members'))) {
					error_log('confirm group add');
					$group->add('members', [$uid]);
					$group->update();
				}
				else {
					error_log('already in group');
				}
			}
			else {
				error_log('failed to get group instance '.$lab['add_group']);
			}
			
		}
		else {
			error_log('not adding group: uid '.$uid.', gid '.$lab['add_group']);
		}
		$this->resp = ['text/plain', 'ok'];
	}

	public function admin($rest) {
		$app = array_shift($rest);

		// get exported attribute keys
		$sth = $this->dbh->prepare(
			'SELECT count(l.id) AS validName, title, admin_group, login_scope, login_id, group_concat(ar.attribute separator \',\') AS attrs
			FROM #__labs l
			LEFT JOIN #__lab_attribute_release ar ON ar.lab_id = l.id
			WHERE l.published AND l.name = :name');
		$sth->execute([':name' => $app]);
		$attrList = $sth->fetch(\PDO::FETCH_ASSOC);

		// bail if not even a valid lab name
		if (!$attrList['validName']) {
			throw new NotFoundError;
		}

		if ($this->user->guest) {
			\JFactory::getApplication()->redirect('/login?return='.base64_encode(isset($_SERVER['REDIRECT_SCRIPT_URL']) ? $_SERVER['REDIRECT_SCRIPT_URL'] : $_SERVER['REDIRECT_URL']));
		}

		/// @TODO check app manager group
		if (!$attrList['admin_group']) {
			throw new ForbiddenError;
		}
		$this->assertGroup($attrList['admin_group']);

		$this->resp = ['text/html', self::view('admin', [ 'user' => $this->user ])];
	}

	public function run($rest) {
		if (!$rest) {
			throw new NotFoundError;
		}
		$app = array_shift($rest);
		// dumb hack -- I registered the wrong domain
		if ($app == 'ocs') {
			$app = 'ojs';
		}

		// get exported attribute keys
		$sth = $this->dbh->prepare(
			'SELECT l.id AS lab_id, view, title, login_scope, access_group, login_id, group_concat(ar.attribute separator \',\') AS attrs
			FROM #__labs l
			LEFT JOIN #__lab_attribute_release ar ON ar.lab_id = l.id
			WHERE l.published AND l.name = :name');
		$sth->execute([':name' => $app]);
		$attrList = $sth->fetch(\PDO::FETCH_ASSOC);

		// bail if not even a valid lab name
		if (!$attrList['lab_id']) {
			throw new NotFoundError;
		}

		if (!is_null($attrList['login_scope']) && $this->user->guest) {
			\JFactory::getApplication()->redirect('/login?return='.base64_encode(isset($_SERVER['REDIRECT_SCRIPT_URL']) ? $_SERVER['REDIRECT_SCRIPT_URL'] : $_SERVER['REDIRECT_URL']));
		}

		if (!is_null($attrList['access_group'])) {
			$this->assertGroup($attrList['access_group']);
		}

		// get a proxy for the lab request
		$sth = $this->dbh->prepare('SELECT host, forward, shared_key FROM #__labs_proxy WHERE published AND lab_id = :lab_id ORDER BY ordering LIMIT 1');
		$sth->execute([':lab_id' => $attrList['lab_id']]);
		$proxy = $sth->fetch();
		// no proxy no serve-y
		if (!$proxy) {
			throw new ServiceUnavailableError;
		}

		// resolve released attributes
		$attrs = [];
		foreach (explode(',', $attrList['attrs']) as $attr) {
			$attrs[$attr] = $this->attributeRelease($attr);
		}

		$cookieKey = sha1('hp-'.$app);
		if (isset($_COOKIE[$cookieKey])) {
			$sessKey = $_COOKIE[$cookieKey];
		}
		else {
			$sessKey = base64_encode(openssl_random_pseudo_bytes(32));
			///@FIXME
			setcookie($cookieKey, $sessKey, 0, '/', null, true, true);
		}

		// post attribute map to proxy
		$ch = curl_init();
		$qs = [
			'session'   => $sessKey,
			'attrs'     => json_encode($attrs),
			'app'       => $app,
			'forward'   => $proxy['forward']
		];
		$copts = [
			\CURLOPT_URL            => 'https://'.$proxy['host'].'/hubpub/set-attributes',
			\CURLOPT_POST           => count($qs),
			\CURLOPT_POSTFIELDS     => http_build_query($qs),
			\CURLOPT_RETURNTRANSFER => true,
			\CURLOPT_HTTPHEADER     => [
				'X-HubPub-Shared-Key: '.$proxy['shared_key']
			]
		];
		curl_setopt_array($ch, $copts);
		$res = curl_exec($ch);

		// not rcvd successfully
		if (!($res = json_decode($res, true)) || !isset($res['result']) || $res['result'] !== 'success') {
			if (isset($res['error'])) {
				error_log('hubpub proxy error: '.$res['error'].", context: ".self::logFriendlyCurlOpts($copts));
			}
			else {
				error_log(print_r($res, 1));
			}
			die('https://'.$proxy['host'].'/hubpub/set-attributes');
			throw new ServiceUnavailableError;
		}

		$query = isset($_SERVER['REDIRECT_QUERY_STRING']) ? $_SERVER['REDIRECT_QUERY_STRING'] : $_SERVER['QUERY_STRING'];
		$this->standalone = $attrList['view'] === 'fullscreen';
		$this->resp = ['text/html', self::view($attrList['view'], [
			// @TODO
			'connect' => 'https://'.$proxy['host'].'/hubpub/session?path='.urlencode('/'.implode('/', $rest).($query ? '?'.$query : '')).'&session='.urlencode($sessKey),
			'session' => $sessKey,
			'title'   => $attrList['title']
		])];
	}

	public function responseIsStandalone() {
		return $this->standalone;
	}

	private static function logFriendlyCurlOpts($copts) {
		// rename numeric options. skip where null
		$logKeys = [
			\CURLOPT_URL            => 'url',
			\CURLOPT_POSTFIELDS     => 'post_fields',
			\CURLOPT_HTTPHEADER     => 'headers',
			\CURLOPT_POST           => NULL,
			\CURLOPT_RETURNTRANSFER => NULL
		];
		$logOpts = [];
		foreach ($copts as $k=>$v) {
			if (array_key_exists($k, $logKeys)) {
				if (!$logKeys[$k]) {
					continue;
				}
				$k = $logKeys[$k];
			}
			// not specifically named or skipped, show the numeric code
			else {
				$k = 'curlopt_code_'.$k;
			}

			// unpack post field url- and json-encoding
			if ($k == 'post_fields') {
				parse_str($v, $v);
				if ($v['attrs']) {
					$v['attrs'] = json_decode($v['attrs']);
				}
			}
			$logOpts[$k] = $v;
		}
		// rm shared key header if present
		$logOpts['headers'] = array_values(array_filter($logOpts['headers'], function($v) { return strpos($v, 'X-HubPub-Shared-Key') !== 0; }));
		return json_encode($logOpts);
	}

	public function __toString() {
		header('Content-type: '.$this->resp[0]);
		return $this->resp[1];
	}

	private function assertGroup($gid) {
		if (!$this->user->guest) {
			$sth = $this->dbh->prepare('SELECT 1 FROM #__xgroups_managers WHERE gidNumber = :gid AND uidNumber = :uid UNION SELECT 1 FROM #__xgroups_members WHERE gidNumber = :gid AND uidNumber = :uid');
			$sth->execute([':gid' => $gid, ':uid' => $this->user->id]);
			$res = $sth->fetchAll();
			if (!$res) {
				$sth = $this->dbh->prepare('SELECT description FROM #__xgroups WHERE gidNumber = :gid');
				$sth->execute([':gid' => $gid]);
				$row = $sth->fetch();
				throw new ForbiddenError($row ? 'Access is restricted to the '.$row['description'].' group' : 'Access is restricted to a group');
			}
		}
	}

	private function attributeRelease($key) {
		if ($this->user->guest) {
			return NULL;
		}
		switch ($key) {
			case 'username':
				return $this->user->username;
			case '':
				return null;
			case 'firstName':
				return $this->getProfile('givenName');
			case 'lastName':
				return $this->getProfile('surname');
			case 'email':
				return $this->user->get('email');
			case 'affiliation':
				return $this->getProfile('organization');
			default:
				throw new InternalServerError('Undefined attribute release: '.$key);
		}
	}

	private function getProfile($key = NULL) {
		if (!$this->profile) {
			$sth = $this->dbh->prepare('SELECT * FROM #__xprofiles WHERE uidNumber = :user_id');
			$sth->execute([':user_id' => $this->user->id]);
			$this->profile = $sth->fetch();
		}
		return $key ? $this->profile[$key] : $this->profile;
	}

	private static function view($name, $ctx = []) {
		extract($ctx);
		$h = function($str) { return htmlentities($str); };
		$a = function($str) { return str_replace('"', '&quot;', $str); };
		ob_start();
		require __DIR__.'/../views/'.$name.'.html.php';
		return ob_get_clean();
	}
}

try {
	if ((!isset($_SERVER['REDIRECT_HTTPS']) || $_SERVER['REDIRECT_HTTPS'] !== 'on') && (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on')) {
		\JFactory::getApplication()->redirect(str_replace('http://', 'https://', isset($_SERVER['REDIRECT_SCRIPT_URI']) ? $_SERVER['REDIRECT_SCRIPT_URI'] : $_SERVER['SCRIPT_URI']));
	}
	$cont = new Controller;
	echo $cont;
	if ($cont->responseIsStandalone()) {
		exit();
	}
}
catch (HttpError $ex) {
	header('content-type: text/plain');
	echo $ex->getMessage();
	echo $ex->getTraceAsString();
	exit();
	\JError::raiseError($ex->getHttpCode(), $ex->getMessage());
}
