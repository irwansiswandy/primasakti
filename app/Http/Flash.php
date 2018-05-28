<?php

namespace App\Http;

use Session;

class Flash
{
	private function create($session_key, $type, $title, $message)
	{
		return Session::flash($session_key, [
			'type' => $type,
			'title' => $title,
			'message' => $message
		]);
	}

	public function message($title, $message)
	{
		return Session::flash('flash_message', [
			'title' => $title,
			'message' => $message
		]);
	}

	public function info_info($title, $message)
	{
		return $this->create(
			'flash_message_with_confirm_button',
			'info',
			$title,
			$message
		);
	}

	public function info_error($title, $message)
	{
		return $this->create(
			'flash_message_with_confirm_button',
			'error',
			$title,
			$message
		);
	}

	public function success($title, $message)
	{
		return $this->create(
			'flash_message',
			'success',
			$title,
			$message
		);
	}

	public function error($title, $message)
	{
		return $this->create(
			'flash_message',
			'error',
			$title,
			$message
		);
	}
}