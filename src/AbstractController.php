<?php

abstract class AbstractController
{
	/**
	 * @var array
	 */
	protected $conf;

	/**
	 * AbstractController constructor.
	 *
	 * @param array $conf
	 */
	public function __construct(array $conf) {
		$this->conf = $conf;
	}

	/**
	 * @param array $request
	 *
	 * @return boolean
	 */
	abstract public function mainAction(array $request);

	/**
	 * @param array $request
	 * @param string $name
	 * @param string $defaultValue
	 *
	 * @return string
	 */
	protected function getTextFromRequest(array $request, $name, $defaultValue = '') {
		if (!array_key_exists($name, $request)
			|| !is_string($text = strval($request[$name]))) {
			$text = $defaultValue;
		}

		return $text;
	}

	/**
	 * @param string $fileName
	 * @param array $data
	 *
	 * @return boolean
	 */
	protected function renderHtml($fileName, array $data = []) {
		extract($data + [
			'base_url' => $this->conf['base_url'],
		]);
		ob_start();
		include 'src/page' . ucfirst($fileName) . '.php';
		$contents = ob_get_clean();

		header('Content-type: text/html; charset=utf-8');
		echo $contents;

		return true;
	}

	/**
	 * @param array $data
	 *
	 * @return boolean
	 */
	protected function renderJson($data) {
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($data);

		return true;
	}

	/**
	 * @return boolean
	 */
	protected function notFound() {
		header('HTTP/1.0 404 Not Found');

		return true;
	}

	/**
	 * @param string $url
	 *
	 * @return boolean
	 */
	protected function redirect($url) {
		header('Location: ' . $url);
		echo '<!DOCTYPE html><html><body><a href="' . $url . '">' . htmlspecialchars($url) . '</a></body></html>';

		return true;
	}
}
