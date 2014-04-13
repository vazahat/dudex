<?php

$ivideo = OW::getPluginManager()->getPlugin('ivideo');
$staticDir = OW_DIR_STATIC_PLUGIN . $ivideo->getModuleName() . DS;

UTIL_File::removeDir($staticDir);
UTIL_File::copyDir($ivideo->getStaticDir(), $staticDir);