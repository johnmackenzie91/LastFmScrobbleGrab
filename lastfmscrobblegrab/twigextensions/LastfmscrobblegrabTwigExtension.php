<?php
namespace Craft;

use Twig_Extension;

class LastfmscrobblegrabTwigExtension extends Twig_Extension
{
	public function getName()
	{
		return Craft::t('BM Last.Fm Scrobble Grab');
	}

	public function getFunctions()
	{
		return array('getLastFmScrobbleGrab' => new \Twig_Function_Method($this, 'getLastFmScrobbleGrab'));
	}

	public function getLastFmScrobbleGrab($settings = null)
	{
		return $query = craft()->lastfmscrobblegrab_functions->scrobbleGrab($settings);
	}

}
