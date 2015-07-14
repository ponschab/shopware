<?php

namespace Element\Emotion;

use Element\MultipleElement;

/**
 * Element: CompareColumn
 * Location: Billing address box on account dashboard
 *
 * Available retrievable properties:
 * - address (Element[], please use Account::checkAddress())
 */
class CompareColumn extends MultipleElement implements \HelperSelectorInterface
{
    /**
     * @var array $selector
     */
    protected $selector = array('css' => 'div.compare_article');

    /**
     * Returns an array of all css selectors of the element/page
     * @return string[]
     */
    public function getCssSelectors()
    {
        return [
            'thumbnailImage'    => 'div.picture > a > img',
            'thumbnailLink'     => 'div.picture > a',
            'name'              => 'div.name > h3 > a',
            'detailsButton'     => 'div.name > a.button-right',
            'stars'             => 'div.votes > div.star',
            'description'       => 'div.desc',
            'price'             => 'div.price > p > strong'
        ];
    }

    /**
     * Returns an array of all named selectors of the element/page
     * @return array[]
     */
    public function getNamedSelectors()
    {
        return [
            'details'  => ['de' => 'Zum Produkt',   'en' => 'View product']
        ];
    }

    /**
     * Returns the image source path
     * @return string
     */
    public function getImageProperty()
    {
        $elements = \Helper::findElements($this, ['thumbnailImage']);

        return $elements['thumbnailImage']->getAttribute('src');
    }

    /**
     * Returns the name
     * @return string
     */
    public function getNameProperty()
    {
        $elements = \Helper::findElements($this, ['thumbnailImage', 'thumbnailLink', 'name', 'detailsButton']);

        $names = array(
            'articleThumbnailImageAlt' => $elements['thumbnailImage']->getAttribute('alt'),
            'articleThumbnailLinkTitle' => $elements['thumbnailLink']->getAttribute('title'),
            'articleName' => $elements['name']->getText(),
            'articleTitle' => $elements['name']->getAttribute('title'),
            'articleDetailsButtonTitle' => $elements['detailsButton']->getAttribute('title')
        );

        return \Helper::getUnique($names);
    }

    /**
     * Returns the star ranking
     * @return string
     */
    public function getRankingProperty()
    {
        $elements = \Helper::findElements($this, ['stars']);

        return $elements['stars']->getAttribute('class');
    }

    /**
     * Returns the link to the product
     * @return string
     */
    public function getLinkProperty()
    {
        $elements = \Helper::findElements($this, ['thumbnailLink', 'name', 'detailsButton']);

        $links = array(
            'articleThumbnailLink' => $elements['thumbnailLink']->getAttribute('href'),
            'articleNameLink' => $elements['name']->getAttribute('href'),
            'articleDetailsButtonLink' => $elements['detailsButton']->getAttribute('href')
        );

        return \Helper::getUnique($links);
    }
}
