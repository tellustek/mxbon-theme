<?php
/**
 * @package   awf
 * @copyright Copyright (c)2014-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or later
 */

namespace Awf\Platform\Joomla\Update;

use Awf\Download\Download;
use SimpleXMLElement;
use Exception;

/**
 * A helper class to read and parse "extension" update XML files over the web
 */
class Extension
{
	/**
	 * Reads an "extension" XML update source and returns all listed update entries.
	 *
	 * @param   string  $url  The extension XML update source URL to read from
	 *
	 * @return  array  An array of update entries
	 */
	public function getUpdatesFromExtension($url)
	{
		// Initialise
		$ret = array();

		// Get and parse the XML source
		$downloader = new Download();
		$xmlSource = $downloader->getFromURL($url);

		try
		{
			$xml = new SimpleXMLElement($xmlSource, LIBXML_NONET);
		}
		catch(Exception $e)
		{
			return $ret;
		}

		// Sanity check
		if (($xml->getName() != 'updates'))
		{
			unset($xml);

			return $ret;
		}

		// Let's populate the list of updates
		/** @var SimpleXMLElement $update */
		foreach ($xml->children() as $update)
		{
			// Sanity check
			if ($update->getName() != 'update')
			{
				continue;
			}

			$entry = array(
				'infourl'			=> array('title' => '', 'url' => ''),
				'downloads'			=> array(),
				'tags'				=> array(),
				'targetplatform'	=> array(),
			);

			$properties = get_object_vars($update);

			foreach ($properties as $nodeName => $nodeContent)
			{
				switch ($nodeName)
				{
					default:
						$entry[$nodeName] = $nodeContent;
						break;

					case 'infourl':
					case 'downloads':
					case 'tags':
					case 'targetplatform':
						break;
				}
			}

			$infourlNode = $update->xpath('infourl');
			$entry['infourl']['title'] = (string)$infourlNode[0]['title'];
			$entry['infourl']['url'] = (string)$infourlNode[0];

			$downloadNodes = $update->xpath('downloads/downloadurl');
			foreach ($downloadNodes as $downloadNode)
			{
				$entry['downloads'][] = array(
					'type'		=> (string)$downloadNode['type'],
					'format'	=> (string)$downloadNode['format'],
					'url'		=> (string)$downloadNode,
				);
			}

			$tagNodes = $update->xpath('tags/tag');
			foreach ($tagNodes as $tagNode)
			{
				$entry['tags'][] = (string)$tagNode;
			}

			/** @var SimpleXMLElement $targetPlatformNode */
			$targetPlatformNode = $update->xpath('targetplatform');

			/** @var \SimpleXMLElement $targetPlatformXMLNode */
			$targetPlatformXMLNode              = $targetPlatformNode[0];
			$entry['targetplatform']['name']    = (string)$targetPlatformXMLNode['name'];
			$entry['targetplatform']['version'] = (string)$targetPlatformXMLNode['version'];
			$client = $targetPlatformXMLNode->xpath('client');
			$entry['targetplatform']['client'] = (is_array($client) && count($client)) ? (string)$client[0] : '';
			$folder = $targetPlatformXMLNode->xpath('folder');
			$entry['targetplatform']['folder'] = is_array($folder) && count($folder) ? (string)$folder[0] : '';

			$ret[] = $entry;
		}

		unset($xml);

		return $ret;
	}
}
