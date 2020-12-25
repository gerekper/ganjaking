<?php

namespace pCloud;

class Config {
	static $credentialPath = __DIR__ . DIRECTORY_SEPARATOR . "/app.cred";
	static $host = "https://eapi.pcloud.com/";
	static $curllib = "pCloud\Curl";
	static $filePartSize = 10485760;
}