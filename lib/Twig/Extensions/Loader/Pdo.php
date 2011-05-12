<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Twig_Extensions_Loader_Pdo implements Twig_LoaderInterface
{
    protected $db;
    protected $options;
    protected $cache = array();
    protected $cacheLoaded = false;

    /**
     * Constructor.
     *
     * @param  PDO   $db      A PDO instance
     * @param  array $options An associative array of DB options
     *
     * @throws Twig_Error_Loader When "db_table" option is not provided
     */
    public function __construct(PDO $db, array $options)
    {
        if (!array_key_exists('db_table', $options)) {
            throw new Twig_Error_Loader('You must provide the "db_table" option for a Pdo loader.');
        }

        $this->db = $db;
        $this->options = array_merge(array(
            'db_name_col' => 'tpl_name',
            'db_data_col' => 'tpl_data',
            'db_time_col' => 'tpl_time',
        ), $options);
    }

    /**
     * {@inheritDoc}
     */
    public function getSource($name)
    {
        $template = $this->findTemplate($name);

        return $template['data'];
    }

    /**
     * {@inheritDoc}
     */
    public function getCacheKey($name)
    {
        $template = $this->findTemplate($name);

        return $template['data'];
    }

    /**
     * {@inheritDoc}
     */
    public function isFresh($name, $time)
    {
        $template = $this->findTemplate($name);

        return $template['time'] < $time;
    }

    protected function findTemplate($name)
    {
        $this->loadCache();

        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        throw new Twig_Error_Loader(sprintf('Template "%s" is not defined.', $name));
    }

    protected function loadCache()
    {
        if (true === $this->cacheLoaded) {
            return;
        }

        // get table/column
        $dbTable  = $this->options['db_table'];
        $dbNameCol = $this->options['db_name_col'];
        $dbDataCol = $this->options['db_data_col'];
        $dbTimeCol = $this->options['db_time_col'];

        $sql = 'SELECT '.$dbNameCol.', '.$dbDataCol.', '.$dbTimeCol.' FROM '.$dbTable;

        try {
            $stmt = $this->db->query($sql);
            foreach ($stmt->fetchAll(PDO::FETCH_NUM) as $template) {
                $this->cache[$template[0]] = array(
                    'data' => $template[1],
                    'time' => $template[2],
                );
            }

            $this->cacheLoaded = true;
        } catch (PDOException $e) {
            throw new Twig_Error_Loader(sprintf('PDOException was thrown when trying to load templates: %s', $e->getMessage()));
        }
    }
}
