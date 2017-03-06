<?php
/**
 * @Copyright
 * @package     Piwik Opt-out for Joomla! 3.x
 * @author      Christian Baur <c.baur@i-san.de>
 * @version     1.0.0 - 2017-03-06
 *
 * @license     GNU/GPL
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');

class PlgContentPiwikOptout extends JPlugin
{

	public function __construct(&$subject, $config)
	{
		if(JFactory::getApplication()->isAdmin())
		{
			return;
		}

		parent::__construct($subject, $config);

		$version = new JVersion();
		$joomla_main_version = substr($version->RELEASE, 0, strpos($version->RELEASE, '.'));

		if($joomla_main_version != '3')
		{
			throw new Exception('Joomla 3.x is required to run this plugin. Your current version is '.$version->RELEASE, 404);
		}
	}

	/**
	 * Entry point of the plugin in core content trigger onContentPrepare
	 *
	 * @param $context
	 * @param $article
	 * @param $params
	 * @param $limitstart
	 */
	public function onContentPrepare($context, &$article, &$params, $limitstart)
	{
		if(stripos($article->text, '{piwikoptout}') === false)
		{
			return;
		}


		if(preg_match_all('@{piwikoptout}@Us', $article->text, $matches, PREG_PATTERN_ORDER) > 0)
		{


			foreach($matches[0] as $match)
			{
				$html = $this->createHtmlOutput();



				$article->text = preg_replace('@(<p>)?{piwikoptout}(</p>)?@s', $html, $article->text);
			}
		}
	}






	/**
	 * Creates the HTML output of the piwik opt-out iframe
	 *
	 * @return string
	 */
	private function createHtmlOutput()
	{
		$piwikUrl = $this->params->get('piwik_url');
		$html = '';
		if(isset($piwikUrl)) {
			$html .= '<iframe style="border: 0; height: 200px; width: 600px;" src="'.$piwikUrl.'index.php?module=CoreAdminHome&action=optOut&language=de"></iframe>';
		}

		return $html;
	}
}
