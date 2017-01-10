<?php

namespace Craft;

class Lastfmscrobblegrab_UpdateController extends BaseController
{

	protected $allowAnonymous = true;

	public function actionUpdateScrobble()
	{

		$limit = craft()->request->getParam('limit');
		$update = craft()->lastfmscrobblegrab_functions->scrobbleGrab($limit);

		if(craft()->request->isAjaxRequest()){
			return $this->returnJson(['success' => true, 'data' => $update]);
		}

	}
}
