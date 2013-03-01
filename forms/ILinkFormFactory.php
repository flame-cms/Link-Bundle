<?php
/**
 * ILinkFormFactory.php
 *
 * @author  Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date    01.03.13
 */

namespace Flame\CMS\LinkBundle\Forms;

interface ILinkFormFactory
{

	/**
	 * @param array $default
	 * @return LinkForm
	 */
	public function create(array $default = array());

}
