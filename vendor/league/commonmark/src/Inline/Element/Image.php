<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Inline\Element;

use League\CommonMark\Util\ArrayCollection;

class Image extends AbstractWebResource
{
    /**
     * @param string $url
     * @param ArrayCollection|string $label
     * @param string $title
     */
    public function __construct($url, $label = '', $title = '')
    {
        parent::__construct($url);

        if (is_string($label)) {
            $this->children = new ArrayCollection(array(new Text($label)));
        } else {
            $this->children = $label;
        }

        if (!empty($title)) {
            $this->data['title'] = $title;
        }
    }
}
