<?php

/*
 *
 *  example.php
 *
 *  Copyright 2021 Philippe Paquet
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

require_once('include/warc.inc.php');

// Create the warc object
$warc = new warc();

// Open example.warc.gz
if (FALSE === $warc->open('example.warc.gz')) {
	echo $warc->error() . PHP_EOL;
	exit();
}

// Read records
while (FALSE !== ($record = $warc->read())) {
	var_dump($record);

	// Check that the content of the record is of type application/http;msgtype=request
	if ('application/http;msgtype=request' == $record['header']['Content-Type']) {

		// Dump the content of the record (raw http request)
		var_dump($record['content']);
	}

	// Check that the content of the record is of type application/http;msgtype=request
	if ('application/http;msgtype=response' == $record['header']['Content-Type']) {

		// Dump the content of the record (raw http response)
		var_dump($record['content']);
	}
}

// Close example.warc.gz
if (FALSE === $warc->close()) {
	echo $warc->error() . PHP_EOL;
	exit();
}
