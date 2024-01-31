<?php
class IopLogger
{
	public function log($logData)
	{
        error_log(print_r($logData, true));
	}
}
