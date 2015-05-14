<?php
/**
 *
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Bouteillier Nicolas <nicolas@kaizendo.fr>
 *
 */
class Twig_Extensions_Extension_imgAlt extends Twig_Extension
{
    /**
     * Returns a list of filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('imgalt', array($this, 'twig_imgalt_filter')),
        );
    }

    public function twig_imgalt_filter($imgName, $minimalLenght = 4)
    {
        /* to keep only the filename */
        $imgNameFiltered = pathinfo($imgName, PATHINFO_FILENAME);

        $keyWords = explode('-', $imgNameFiltered);
        $keyWordsLongEnough = [];

        /* clean up keywords, ignore less than $minimalLenght letters and numbers */
        foreach ($keyWords as $keyWord) {
            if (strlen($keyWord) > $minimalLenght && !is_numeric($keyWord)) {
                $keyWordsLongEnough[] = $keyWord;
            }
        }

        return implode(', ', $keyWordsLongEnough);
    }

    public function getName()
    {
        return 'imgalt';
    }

}