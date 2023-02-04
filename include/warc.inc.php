<?php

/*
 *
 *  warc.inc.php
 *
 *  Version 1.1
 *
 *  Copyright 2019-2023 Philippe Paquet
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */





//
// warc
//
// function __construct()
//
// function open(string $filepath)
// function close()
// function read()
// function error()
//

class warc
{
	//
	// Variables
	//

	private $handle;
	private $error;

	//
	// __construct
	//

	function __construct()
	{
		$this->handle = FALSE;
		$this->error = '';
	}

	//
	// close
	//

	function close()
	{
		if (FALSE === gzclose($this->handle)) {
			$this->error = 'Error closing file';
			return FALSE;
		} else {
			$this->error = '';
			$this->handle = FALSE;
			return TRUE;
		}
	}

	//
	// error
	//

	function error()
	{
		return $this->error;
	}

	//
	// open
	//

	function open(string $filepath)
	{
		$this->handle = gzopen($filepath, 'r');
		if (FALSE === $this->handle) {
			$this->error = 'Error opening file';
			return FALSE;
		} else {
			$this->error = '';
			return TRUE;
		}
	}

	//
	// read
	//

	function read()
	{
		if (FALSE !== $this->handle) {

			$header = array();

			$line = gzgets($this->handle);
			if (FALSE === $line) {
				$this->error = 'Read error';
				return FALSE;
			}

			while ("\r\n" == $line) {
				$line = gzgets($this->handle);
				if (FALSE === $line) {
					$this->error = 'Read error';
					return FALSE;
				}
			}

			while ($line != "\r\n") {
				$parts = explode(': ', $line, 2);
				switch (trim($parts[0])) {
					case 'WARC/1.0':
					case 'WARC/1.1':
						$header['Version'] = trim($parts[0]);
						break;
					default:
						$header[trim($parts[0])] = trim($parts[1]);
						break;
				}
				$line = gzgets($this->handle);
				if (FALSE === $line) {
					$this->error = 'Read error';
					return FALSE;
				}
			}

			if (TRUE == array_key_exists('Content-Length', $header)) {

				$content = gzread($this->handle, $header['Content-Length']);
				if (FALSE === $content) {
					$this->error = 'Read error';
					return FALSE;
				}

				$line = gzgets($this->handle);
				if (FALSE === $line) {
					$this->error = 'Read error';
					return FALSE;
				}

				$line = gzgets($this->handle);
				if (FALSE === $line) {
					$this->error = 'Read error';
					return FALSE;
				}

				return array('header' => $header, 'content' => $content);

			} else {

				$this->error = 'Content-Length missing from header';
				return FALSE;
			}

		} else {

			$this->error = 'File not open';
			return FALSE;
		}
	}
}
