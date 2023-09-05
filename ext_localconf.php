<?php

use WapplerSystems\Bigfiledump\Controller\FileDumpController;

$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['dumpFile'] = FileDumpController::class . '::dumpAction';
