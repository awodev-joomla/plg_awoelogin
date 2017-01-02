<?php
/**
 * @plugin Awo Email Login
 * @copyright Copyright (C) 2010 Seyi Awofadeju - All rights reserved.
 * @Website : http://dev.awofadeju.com
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL 
 **/

defined('_JEXEC') or die;
class plgSystemAwoELogin extends JPlugin {

	public function onAfterRoute() {
		$app = JFactory::getApplication();
		
		if (!$app->isSite()) return;
		
		$task = $app->input->get('task');
		$post = $app->input->post->getArray();
		
		if($task != 'reset.confirm') return;
		if(!isset($post['jform']['username']) || !isset($post['jform']['token'])) return;
		
		if(!JPluginHelper::isEnabled('authentication', 'awoelogin')) return;

		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
			->select('username')
			->from($db->quoteName('#__users'))
			->where($db->quoteName('email') . ' = ' . $db->quote($post['jform']['username']));
		// Get the user object.
		$db->setQuery($query);
		$username = $db->loadResult();
		
		if(!empty($username)) {
			$post['jform']['username'] = $username;
			$app->input->set('jform', $post['jform']);
			
		}
	}

}


