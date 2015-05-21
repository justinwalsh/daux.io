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

class Link extends AbstractWebResource
{
    /**
     * @param string $url
     * @param ArrayCollection|string|null $label
     * @param string $title
     */
    public function __construct($url, $label = null, $title = '')
    {
        parent::__construct($url);

        if ($label === null) {
            $label = $url;
        }

        if (is_string($label)) {
            $this->children = new ArrayCollection(array(new Text($label)));
        } elseif (is_null($label)) {
            $this->children = new ArrayCollection();
        } else {
            $this->children = $label;
        }

        if (!empty($title)) {
            $this->data['title'] = $title;
        }
    }
}
