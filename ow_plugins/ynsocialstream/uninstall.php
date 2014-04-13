<?php
$dbPrefix = OW_DB_PREFIX;

$sql ="DELETE FROM `{$dbPrefix}base_config` WHERE `key` = 'ynsocialstream'";


OW::getDbo()->query($sql);