<?php

/**
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Jules Pietri <jules@heahprod.com>
 */
class Twig_Extensions_Extension_Link extends Twig_Extension
{
    /**
     * List of filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('link', array($this, 'createLink'), array(
                'pre_escape' => 'html',
                'is_safe' => array('html'),
            )),
            new \Twig_SimpleFilter('linkTo', array($this, 'createNamedLink'), array(
                'pre_escape' => 'html',
                'is_safe' => array('html'),
            )),
            new \Twig_SimpleFilter('mail', array($this, 'createMailTo'), array(
                'pre_escape' => 'html',
                'is_safe' => array('html'),
            )),
            new \Twig_SimpleFilter('mailTo', array($this, 'createNamedMailTo'), array(
                'pre_escape' => 'html',
                'is_safe' => array('html'),
            )),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'link';
    }

    /**
     * Create an html link from an url.
     *
     * @param string $url        input
     * @param array  $attributes html attributes e.g class, target, id
     * @param bool   $withScheme let the scheme explicit or not
     *
     * @return string html formatted link
     *
     * @throws \Twig_Error_Runtime
     */
    public function createLink($url, array $attributes = array(), $withScheme = false)
    {
        if (!is_string($url)) {
            throw new \Twig_Error_Runtime('Invalid argument passed to "link" filter, must be a string.');
        }

        $url = 'http' === substr($url, 0, 4) ? $url : 'http://'.$url;
        $parts = explode('://', $url);
        $link = $withScheme ? $url : $parts[1];
        $attr = '';

        foreach ($attributes as $attribute => $value) {
            $attr .= ' '.$attribute.'="'.$value.'"';
        }

        return '<a href="'.urlencode($url).'"'.$attr.'>'.$link.'</a>';
    }

    /**
     * Create a named html link from an url.
     *
     * @param string $url        input
     * @param string $link       output name of the link
     * @param array  $attributes html attributes e.g class, target, id
     *
     * @return string html output link
     *
     * @throws \Twig_Error_Runtime
     */
    public function createNamedLink($url, $link, array $attributes = array())
    {
        if (!is_string($url) || !is_string($link)) {
            throw new \Twig_Error_Runtime('Invalid argument passed to "link" filter, must be a string.');
        }

        $url = 'http' === substr($url, 0, 4) ? $url : 'http://'.$url;
        $attr = '';

        foreach ($attributes as $attribute => $value) {
            $attr .= ' '.$attribute.'="'.$value.'"';
        }

        return '<a href="'.urlencode($url).'"'.$attr.'>'.$link.'</a>';
    }

    /**
     * Create a mail link from an e-mail address
     *
     * @param string $mail       input e-mail
     * @param array  $attributes html attributes e.g id, class
     * @param string $subject    auto-complete subject of e-mail
     * @param string $body       auto-complete body of e-mail
     *
     * @return string html output mailto link
     *
     * @throws \Twig_Error_Runtime
     */
    public function createMailTo($mail, array $attributes = array(), $subject = '', $body = '')
    {
        if (!is_string($mail)) {
            throw new \Twig_Error_Runtime('Invalid argument passed to "linkTo" filter, must be a string.');
        }

        $attr = '';
        $extra = '';
        $params = array();

        if ($subject) {
            $params[] = 'subject='.rawurlencode($subject);
        }

        if ($body) {
            $params[] =  'body='.rawurlencode($body);
        }

        if ($params) {
            $extra .= '?'.implode('&', $params);
        }

        foreach ($attributes as $attribute => $value) {
            $attr .= ' '.$attribute.'="'.$value.'"';
        }

        return '<a href="mailto:'.$mail.$extra.'"'.$attr.'>'.$mail.'</a>';
    }

    /**
     * Create a named mailto link from an e-mail.
     *
     * @param string $mail       input e-mail
     * @param string $link       input name of the link
     * @param array  $attributes html attributes e.g id, class
     * @param string $subject    auto-complete subject of e-mail
     * @param string $body       auto-complete body of e-mail
     *
     * @return string html output mailto link
     *
     * @throws \Twig_Error_Runtime
     */
    public function createNamedMailTo($mail, $link, array $attributes = array(), $subject = '', $body = '')
    {
        if (!is_string($mail) || !is_string($link)) {
            throw new \Twig_Error_Runtime('Invalid argument passed to "mail" filter, must be a string.');
        }

        $attr = '';
        $extra = '';
        $params = array();

        if ($subject) {
            $params[] = 'subject='.rawurlencode($subject);
        }

        if ($body) {
            $params[] =  'body='.rawurlencode($body);
        }

        if ($params) {
            $extra .= '?'.implode('&', $params);
        }

        foreach ($attributes as $attribute => $value) {
            $attr .= ' '.$attribute.'="'.$value.'"';
        }

        return '<a href="mailto:'.$mail.$extra.'"'.$attr.'>'.$link.'</a>';
    }
}