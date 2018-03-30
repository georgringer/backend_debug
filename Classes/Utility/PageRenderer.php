<?php

namespace GeorgRinger\BackendDebug\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3
 * project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as
 * published by
 *  the Free Software Foundation; either version 3 of the
 * License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be
 * useful,
 *  but WITHOUT ANY WARRANTY; without even the implied
 * warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See
 * the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the
 * script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;


class PageRenderer
{

    /**
     * Adds JavaCript and CSS to the Backend
     *
     * @param $params
     * @param \TYPO3\CMS\Core\Page\PageRenderer $parent
     */
    public function setup(&$params, \TYPO3\CMS\Core\Page\PageRenderer &$parent)
    {

        $params['jsInline']['BackendDebug'] = [
          'code' => $this->getContextCode(),
          'section' => 1,
          'compress' => FALSE,
          'forceOnTop' => TRUE,
        ];

        $parent->addCssInlineBlock('BackendDebug', $this->getCssCode());
    }

    /**
     * JavaScript Inline Code
     *
     * @return string
     */
    protected function getContextCode()
    {
        $context = strtolower(GeneralUtility::getApplicationContext());
        $debug = $GLOBALS['TYPO3_CONF_VARS']['BE']['debug'] ? 'debug' : 'nodebug';
        $code = <<<EOT
      var $ = TYPO3.jQuery;
        $(document).ready(function(){
          $('body').addClass('$context').addClass('$debug');
        });
EOT;

        return $code;
    }

    /**
     * CSS Inline Code
     *
     * @return string
     */
    protected function getCssCode()
    {
        return <<<EOT

        body.debug .t3-page-ce-header-icons-left a:after,
        body.debug .t3js-entity td.col-icon a.t3js-contextmenutrigger:after {
            content: "[ID: " attr(data-uid) "]";
            padding-left: 5px;
        }


EOT;

    }
}
