<?php

/**
 * Copyright (c) 2013, Kairat Bakytow
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

$plugin = OW::getPluginManager()->getPlugin( 'profileprogressbar' );

$staticDir = OW_DIR_STATIC_PLUGIN . $plugin->getModuleName() . DS;
$staticCssDir = $staticDir  . 'css' . DS;

if ( !file_exists($staticDir) )
{
    mkdir( $staticDir );
    chmod( $staticDir, 0777 );
}

if ( !file_exists($staticCssDir) )
{
    mkdir( $staticCssDir );
    chmod( $staticCssDir, 0777 );
}

$dirIterator = new RecursiveDirectoryIterator( $plugin->getStaticDir() . 'css' . DS );
$interator = new RecursiveIteratorIterator( $dirIterator );

foreach ( $interator as $file ) 
{
    if ( $file->getFilename() == '.' )
    {
        continue;
    }

    if ( !$file->isDir() && pathinfo( $file->getPathname(), PATHINFO_EXTENSION ) == 'css' )
    {
        @copy( $file->getPathname(), $staticCssDir . $file->getFilename() );
    }
}
