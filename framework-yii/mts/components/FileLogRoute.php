<?php


class FileLogRoute extends CFileLogRoute
{
    protected function formatLogMessage($message,$level,$category,$time)
    {
        return @date('Y/m/d H:i:s',$time).' '.$time." [$level] [$category] $message\n";
    }
}