<?php

use ShortUrl\IService as IShortUrlService;
use ShortUrl\InternalException;

class Controller extends AbstractController
{
	/**
	 * @var IShortUrlService
	 */
	protected $shortUrlService;

	/**
	 * Controller constructor.
	 *
	 * @param IShortUrlService $shortUrlService
	 * @param array $conf
	 */
	public function __construct(IShortUrlService $shortUrlService, array $conf) {
		$this->shortUrlService = $shortUrlService;
		parent::__construct($conf);
	}

	/**
	 * {@inheritdoc}
	 */
	public function mainAction(array $request) {
		return $this->renderHtml('main');
	}

	/**
	 * @param array $request
	 *
	 * @return boolean
	 */
	public function generateAction(array $request) {
		$url = $this->getTextFromRequest($request, 'url');

		try {
			$token = $this->shortUrlService->generateToken($url);
			$data = [
				'shortUrl' => $this->conf['base_url'] . $token,
			];
		} catch (InternalException $exception) {
			$data = [
				'error' => 'Internal error: ' . $exception->getMessage() . ' Try again!',
			];
		} catch (\Exception $exception) {
			$data = [
				'error' => 'Error: ' . $exception->getMessage(),
			];
		}

		return $this->renderJson($data);
	}

	/**
	 * @param array $request
	 *
	 * @return boolean
	 */
	public function getAction(array $request) {
		$token = $this->getTextFromRequest($request, 'action');

		try {
			$url = $this->shortUrlService->getUrl($token);
			$response = $this->redirect($url);
		} catch (\Exception $exception) {
			$response = $this->notFound();
		}

		return $response;
	}
}