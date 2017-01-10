<?php
namespace Craft;

class LastfmscrobblegrabPlugin extends BasePlugin
{

	function getName()
	{
		return Craft::t('Last FM Scrobble Grab');
	}

	function getVersion()
	{
		return '1.0';
	}

	function getDeveloper()
	{
		return 'John Mackenzie';
	}

	function getDeveloperUrl()
	{
		return 'http://www.johnmackenzie.co.uk';
	}

	protected function defineSettings()
	{
		return array(
			'apiKey' => array(AttributeType::String, 'required' => true),
			'username' => array(AttributeType::String),
			'requeryFrequency' => array(AttributeType::String, 'default' => '3000'),
			'showCurrentlyPlaying' => array(AttributeType::Bool),
			'showRecentlyPlayed' => array(AttributeType::Bool),
			'limit' => array(AttributeType::String, 'default' => '3'),
			'showAlbumThumbnail' => array(AttributeType::Bool),
			'showArtist' => array(AttributeType::Bool),
			'showAlbum' => array(AttributeType::Bool),
			'showTrack' => array(AttributeType::Bool)
		);
	}


	public function getSettingsHtml()
	{
		return craft()->templates->render('lastfmscrobblegrab/_settings', array(
			'settings' => $this->getSettings()
		));
	}

	/**
	 * Registers the Twig extension.
	 *
	 * @return FacebookFeedTwigExtension
	 */
	public function addTwigExtension()
	{
		Craft::import('plugins.lastfmscrobblegrab.twigextensions.LastfmscrobblegrabTwigExtension');
		return new LastfmscrobblegrabTwigExtension();
	}

}
