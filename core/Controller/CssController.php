<?php
/**
 * @copyright Copyright (c) 2016, John Molakvoæ (skjnldsv@protonmail.com)
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OC\Core\Controller;

use OC\AppFramework\Utility\TimeFactory;
use OC\CssManager;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\NotFoundResponse;
use OCP\AppFramework\Http\FileDisplayResponse;
use OCP\Files\NotFoundException;
use OCP\IRequest;


class CssController extends Controller {

	/** @var CssManager */
	protected $cssManager;

	/** @var TimeFactory */
	protected $timeFactory;



	/**
	 * @param string $appName
	 * @param IRequest $request
	 * @param CssManager $cssManager
	 * @param TimeFactory $timeFactory
	 */
	public function __construct($appName, IRequest $request, CssManager $cssManager, TimeFactory $timeFactory) {
		parent::__construct($appName, $request);

		$this->cssManager = $cssManager;
		$this->timeFactory = $timeFactory;
	}

	/**
	 * @PublicPage
	 * @NoCSRFRequired
	 *
	 * @param string $fileName css filename with extension
	 * @param string $appName css folder name
	 * @return FileDisplayResponse|NotFoundResponse
	 */
	public function getCss($fileName, $appName) {
		try {
			$cssFile = $this->cssManager->getCss($fileName, $appName);
		} catch(NotFoundException $e) {
			return new NotFoundResponse();
		}

		if ($cssFile !== false) {
			$response = new FileDisplayResponse($cssFile, Http::STATUS_OK, ['Content-Type' => 'text/css']);
			$response->cacheFor(86400);
			$expires = new \DateTime();
			$expires->setTimestamp($this->timeFactory->getTime());
			$expires->add(new \DateInterval('PT24H'));
			$response->addHeader('Expires', $expires->format(\DateTime::RFC2822));
			$response->addHeader('Pragma', 'cache');
			return $response;
		}
		return new NotFoundResponse();
	}
}