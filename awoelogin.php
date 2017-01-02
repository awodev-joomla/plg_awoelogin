<?php
/**
 * @plugin Awo Email Login
 * @copyright Copyright (C) 2010 Seyi Awofadeju - All rights reserved.
 * @Website : http://dev.awofadeju.com
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL 
 **/

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgAuthenticationAwoELogin extends JPlugin {

	function onUserAuthenticate($credentials, $options, &$response) {
		jimport('joomla.user.helper');

		$response->type = 'AwoEmailLogin';
		if (empty($credentials['password'])) {
			$response->status = JAuthentication::STATUS_FAILURE;
			$response->error_message = JText::_('JGLOBAL_AUTH_EMPTY_PASS_NOT_ALLOWED');
			return false;
		}

		// Get a database object
		$db		= JFactory::getDbo();
		$sql	= $db->getQuery(true);

		$sql->select('id, password');
		$sql->from('#__users');
		$sql->where('email='.$db->Quote($credentials['username']));
		$db->setQuery($sql);
		$result = $db->loadObject();

		if ($result) {
			if(method_exists('JUserHelper','verifyPassword')) {
				$match = JUserHelper::verifyPassword($credentials['password'], $result->password, $result->id);
			}
			else {
				$parts	= explode(':', $result->password);
				$crypt	= $parts[0];
				$salt	= @$parts[1];
				$testcrypt = JUserHelper::getCryptedPassword($credentials['password'], $salt);
				$match = $crypt == $testcrypt ? true : false;
			}

			if ($match === true) {
				$user = JUser::getInstance($result->id);
				$response->username = $user->username;
				$response->email = $user->email;
				$response->fullname = $user->name;
				$response->language = JFactory::getApplication()->isAdmin() ? $user->getParam('admin_language') : $user->getParam('language');
				$response->status = JAuthentication::STATUS_SUCCESS;
				$response->error_message = '';
			} else {
				$response->status = JAuthentication::STATUS_FAILURE;
				$response->error_message = JText::_('JGLOBAL_AUTH_INVALID_PASS');
			}
		} else {
			$response->status = JAuthentication::STATUS_FAILURE;
			$response->error_message = JText::_('JGLOBAL_AUTH_NO_USER');
		}
				
	}
}
