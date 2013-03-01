<?php
/**
 * LinkManager.php
 *
 * @author  Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date    01.03.13
 */

namespace Flame\CMS\LinkBundle\Model;

class LinkManager extends \Flame\Model\Manager
{

	/** @var LinkFacade */
	private $linkFacade;

	/**
	 * @param LinkFacade $linkFacade
	 */
	public function injectLinkFacade(LinkFacade $linkFacade)
	{
		$this->linkFacade = $linkFacade;
	}

	/**
	 * @param $data
	 * @return Link
	 * @throws \Nette\InvalidArgumentException
	 */
	public function update($data)
	{
		$values = $this->validateInput($data, array('name', 'url', 'description', 'public'));

		if($id = $this->getId($data)){
			if($link = $this->linkFacade->getOne($id)){
				return $this->edit($link, $values);
			}else{
				throw new \Nette\InvalidArgumentException('Link with ID "' . $link . '" does not exist');
			}

		}else{
			return $this->create($values);
		}

	}

	/**
	 * @param $values
	 * @return Link
	 */
	protected function create($values)
	{
		$link = new \Flame\CMS\LinkBundle\Model\Link($values->name, $this->treatUrl($values->url));
		$link->setDescription($values->description)
			->setPublic($values->public);
		$this->linkFacade->save($link);
		return $link;
	}

	/**
	 * @param Link $link
	 * @param $values
	 * @return Link
	 */
	protected function edit(Link $link, $values)
	{
		$link->setName($values->name)
			->setDescription($values->description)
			->setUrl($this->treatUrl($values->url))
			->setPublic($values->public);
		$this->linkFacade->save($link);
		return $link;
	}

	/**
	 * @param $url
	 * @return string
	 */
	protected function treatUrl($url)
	{
		if(substr($url, 0, 4) !== 'http'){
			$url = 'http://' . $url;
		}

		return $url;
	}

}
