<?php

namespace Craft;

class Lastfmscrobblegrab_FunctionsService extends BaseApplicationComponent
{
	private $settings, $result, $currentlyListening;

	/**
	 * Lets get started
	 */
	public function __construct()
	{
		$this->settings = craft()->plugins->getPlugin('lastfmscrobblegrab')->getSettings();
		if ($this->settings['apiKey'] == null) throw new Exception('No Api Key Set');
	}

	/**
	 * Queries API
	 */
	public function queryAPI()
	{
		$url = "http://ws.audioscrobbler.com/2.0/?method=user.getRecentTracks&user=" . $this->settings['username'] .
			"&api_key=" . $this->settings['apiKey'] .
			"&limit=" . $this->settings['limit'] .
			"&format=json";
			if ($this->settings['showAlbumThumbnail']) $url .='&extends=1';
		return $this->result = json_decode(file_get_contents($url));
	}

	/**
	 * Return Markup for frontend, for both AJAX and original request
	 */
	public function getMarkup()
	{
		$htmlMarkup = '';

		if (!craft()->request->isAjaxRequest()) {
			$htmlMarkup .=	craft()->templates->includeCssResource('lastfmscrobblegrab/css/lastfmscrobblegrab.css');
			$htmlMarkup .=	craft()->templates->includeJsResource('lastfmscrobblegrab/js/lastfmscrobblegrab.js');

			$htmlMarkup .=	'<div id="lastfm-scrobble-grab"
							data-scrobble-username="' . $this->settings['username'] .'"
							data-scrobble-requeryFrequency="' . $this->settings['requeryFrequency'] .'"
							data-scrobble-showCurrentlyPlaying="' . $this->settings['showCurrentlyPlaying'] .'"
							data-scrobble-showRecentlyPlayed="' . $this->settings['showRecentlyPlayed'] .'"
							data-scrobble-limit="' . $this->settings['limit'] .'"
							data-scrobble-showAlbumThumbnail="' . $this->settings['showAlbumThumbnail'] .'"
							data-scrobble-showArtist="' . $this->settings['showArtist'] .'"
							data-scrobble-showAlbum="' . $this->settings['showAlbum'] .'"
							data-scrobble-showTrack="' . $this->settings['showTrack'] .'">';

		}

		$htmlMarkup .= $this->renderCurrentlyPlaying();		
		$htmlMarkup .= $this->renderRecentlyPlayed();
		
		if (!craft()->request->isAjaxRequest()) {
			$htmlMarkup .= '</div>';
		}
		return $htmlMarkup;
	}

	/**
	 * Grab plugin settings
	 */
	public function scrobbleGrab($settings)
	{
		if (is_array($settings)) $this->setSettings($settings);
		$this->queryAPI();
		return $this->getMarkup();
	}

	/**
	 * Return markup based on Currently Played settings
	 */
	private function renderCurrentlyPlaying()
	{
		$htmlMarkup = '';
		$firstItem = $this->result->recenttracks->track[0];

		if (isset($firstItem->{'@attr'}) && $this->settings['showCurrentlyPlaying']) {
			$this->currentlyListening = true;

			$htmlMarkup .=	'<div class="scrobble-currently-playing"><p>Currently Playing</p>';
			$htmlMarkup .=	'<div class="scrobble-track currently-playing">';

			$htmlMarkup .=	$this->trackContent($firstItem);

			$htmlMarkup .=	'</div>';
			$htmlMarkup .=	'</div>';

			array_shift($this->result->recenttracks->track);
		}
		return $htmlMarkup;
	}

	/**
	 * Return markup based on Recently Played Played settings
	 */
	private function renderRecentlyPlayed()
	{
		$htmlMarkup = '';

		if ($this->settings['showRecentlyPlayed']) {

		$htmlMarkup = ($this->settings['showCurrentlyPlaying']) ? '<div class="scrobble-recently-played">' : '<div class="scrobble-recently-played-only">';
		$htmlMarkup .= '<p>Recently Played</p>';

			foreach ($this->result->recenttracks->track as $track) {

				$htmlMarkup .= '<div class="scrobble-track">';
				$htmlMarkup .= $this->trackContent($track);
				$htmlMarkup .= '</div>';

			}

		$htmlMarkup .= '</div>';
		}

		return $htmlMarkup;
	}

	/**
	 * Return markup based on Track Content
	 */
	private function trackContent($track)
	{
		$htmlMarkup = '';

		if ($this->settings['showAlbumThumbnail']) {

			$imgSrc = (empty($track->image[1]->{'#text'})) ?  UrlHelper::getResourceURL('lastfmscrobblegrab/images/lastfm-scrobble.png') : $track->image[1]->{'#text'};
			$htmlMarkup .= '<div class="thumbnail-wrapper"><img class="lastfm-scroggle-album-thumbnail" src="' . $imgSrc . '" /></div>';
		}

		$htmlMarkup .= '<div class="artist-info-wrapper"><p class="lastfm-scroggle-artist-info"> ';

		if ($this->settings['showArtist']) {
			$htmlMarkup .= ' ' . $track->artist->{'#text'} . '<br/>';
		}

		if ($this->settings['showTrack']) {
			$htmlMarkup .= '<a target="_blank" href="' . $track->url . '"> ' . $track->name . ' </a><br/>';
		}

		if ($this->settings['showAlbum']) {
			$htmlMarkup .= ' ' . $track->album->{'#text'} . ' ';
		}

		$htmlMarkup .=	'</div>';

		return $htmlMarkup;
	}

	/**
	 * Get plugin settings from Database
	 */
	private function setSettings($settings)
	{
		foreach ($settings as $key => $setting) {

			if (isset($this->settings[$key])) {
				$this->settings[$key] = $setting;
			}
		}
		return;
	}

	/**
	 * Set requery frequency settings
	 */
	private function setRequeryFrequency($requeryFrequency)
	{
		if ($requeryFrequency == null || empty($requeryFrequency)) {
			if ($this->settings['requeryFrequency'] == null || empty($this->settings['requeryFrequency'])) {
				return $this->requeryFrequency = 30000;
			}else{
				return $this->requeryFrequency = intval($this->settings['requeryFrequency']);
			}
		}else{
			return $this->requeryFrequency = $requeryFrequency;
		}
	}
}
