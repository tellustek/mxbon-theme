<?php
/**
 * @package   awf
 * @copyright Copyright (c)2014-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or later
 */

namespace Awf\Hal;

/**
 * Implementation of the Hypertext Application Language links in PHP. This is
 * actually a collection of links.
 *
 * @see http://stateless.co/hal_specification.html
 */
class Links
{
	/**
	 * The collection of links, sorted by relation
	 *
	 * @var array
	 */
	private $_links = array();

	/**
	 * Add a single link to the links collection
	 *
	 * @param   string   $rel        The relation of the link to the document. See RFC 5988
	 *                               http://tools.ietf.org/html/rfc5988#section-6.2.2 A document
	 *                               MUST always have a "self" link.
	 * @param   Link     $link       The actual link object
	 * @param   boolean  $overwrite  When false and a link of $rel relation exists, an array of
	 *                               links is created. Otherwise the existing link is overwriten
	 *                               with the new one
	 *
	 * @return  boolean  True if the link was added to the collection
	 */
	public function addLink($rel, Link $link, $overwrite = true)
	{
		if (!$link->check())
		{
			return false;
		}

		if (!array_key_exists($rel, $this->_links) || $overwrite)
		{
			$this->_links[$rel] = $link;
		}
		else
		{
			if (!is_array($this->_links[$rel]))
			{
				$this->_links[$rel] = array($this->_links[$rel]);
			}

			$this->_links[$rel][] = $link;
		}
	}

	/**
	 * Add multiple links to the links collection
	 *
	 * @param   string   $rel        The relation of the links to the document. See RFC 5988.
	 * @param   array    $links      An array of FOFHalLink objects
	 * @param   boolean  $overwrite  When false and a link of $rel relation exists, an array
	 *                               of links is created. Otherwise the existing link is
	 *                               overwriten with the new one
	 *
	 * @return  boolean  True if the link was added to the collection
	 */
	public function addLinks($rel, array $links, $overwrite = true)
	{
		if (empty($links))
		{
			return false;
		}

		$localOverwrite = $overwrite;

		foreach ($links as $link)
		{
			if ($link instanceof Link)
			{
				$this->addLink($rel, $link, $localOverwrite);
			}

			// After the first time we call this with overwrite on we have to
			// turn it off so that the other links are added to the set instead
			// of overwriting the first item that's already added.
			if ($localOverwrite)
			{
				$localOverwrite = false;
			}
		}
	}

	/**
	 * Returns the collection of links
	 *
	 * @param   string  $rel  Optional; the relation to return the links for
	 *
	 * @return  array|Link
	 */
	public function getLinks($rel = null)
	{
		if (empty($rel))
		{
			return $this->_links;
		}
		elseif (isset($this->_links[$rel]))
		{
			return $this->_links[$rel];
		}
		else
		{
			return array();
		}
	}
}
