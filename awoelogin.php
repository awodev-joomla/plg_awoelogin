<?php
/**
 * @plugin Awo Email Login
 * @copyright Copyright (C) 2010 Seyi Awofadeju - All rights reserved.
 * @Website : http://dev.awofadeju.com
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL 
 **/

defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgAuthenticationAwoELogin extends JPlugin {

	function plgAuthenticationAwoELogin(& $subject, $config) {
		parent::__construct($subject, $config);
	}

	function onAuthenticate( $credentials, $options, &$response ) {
		jimport('joomla.user.helper');

		if (empty($credentials['password'])) {
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Empty password not allowed';
			return false;
		}

		$db =& JFactory::getDBO();
		$sql = 'SELECT `id`, `password`, `gid` FROM `#__users` WHERE email='.$db->Quote( $credentials['username'] );
		$db->setQuery( $sql );
		$result = $db->loadObject();


		if($result) {
			$parts	= explode( ':', $result->password );
			$crypt	= $parts[0];
			$salt	= @$parts[1];
			$testcrypt = JUserHelper::getCryptedPassword($credentials['password'], $salt);

			if ($crypt == $testcrypt) {
				$user = JUser::getInstance($result->id); // Bring this in line with the rest of the system
				$response->username = $user->username;
				$response->email = $user->email;
				$response->fullname = $user->name;
				$response->status = JAUTHENTICATE_STATUS_SUCCESS;
				$response->error_message = '';
			} else {
				$response->status = JAUTHENTICATE_STATUS_FAILURE;
				$response->error_message = 'Invalid password';
			}
		}
		else {
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'User does not exist';
		}
	}
}
