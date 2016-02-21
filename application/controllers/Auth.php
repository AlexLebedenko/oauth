<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('server', NULL, 'serverLib');
	}

	public function token()
	{
		//var_dump($this->serverLib->server->handleTokenRequest(OAuth2\Request::createFromGlobals()));
		//exit;
		var_dump($_POST);
		// Handle a request for an OAuth2.0 Access Token and send the response to the client
		$this->serverLib->server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
	}

	public function resource()
	{
		// Handle a request to a resource and authenticate the access token
		if (!$this->serverLib->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
			$this->serverLib->server->getResponse()->send();
			die;
		}
		echo json_encode(array('success' => true, 'message' => 'You accessed my APIs!'));
	}

	public function authorize()
	{
		$request = OAuth2\Request::createFromGlobals();
		$response = new OAuth2\Response();

		// validate the authorize request
		if (!$this->serverLib->server->validateAuthorizeRequest($request, $response)) {
			$response->send();
			die;
		}
		// display an authorization form
		/*if (empty($_POST)) {
			exit('
				<form method="post">
  					<label>Do You Authorize TestClient?</label><br />
  					<input type="submit" name="authorized" value="yes">
  					<input type="submit" name="authorized" value="no">
				</form>');
		}*/

		// print the authorization code if the user has authorized your client
		//$is_authorized = ($_POST['authorized'] === 'yes');

		$this->serverLib->server->handleAuthorizeRequest($request, $response, TRUE);
		//if ($is_authorized) {
			// this is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
			$code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);
			exit("SUCCESS! Authorization Code: $code");
		//}
		$response->send();
	}
}
