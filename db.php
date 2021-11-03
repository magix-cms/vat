<?php
class plugins_vat_db
{
    /**
     * @param $config
     * @param bool $params
     * @return mixed|null
     * @throws Exception
     */
    public function fetchData($config, $params = false)
    {
        if (!is_array($config)) return '$config must be an array';

        $sql = '';
        $dateFormat = new component_format_date();

        if ($config['context'] === 'all') {
            switch ($config['type']) {
                case 'pages':
                    $limit = '';
                    if ($config['offset']) {
                        $limit = ' LIMIT 0, ' . $config['offset'];
                        if (isset($config['page']) && $config['page'] > 1) {
                            $limit = ' LIMIT ' . (($config['page'] - 1) * $config['offset']) . ', ' . $config['offset'];
                        }
                    }

                    $sql = "SELECT p.*
						FROM mc_vat AS p " . $limit;

                    if (isset($config['search'])) {
                        $cond = '';
                        if (is_array($config['search']) && !empty($config['search'])) {
                            $nbc = 1;
                            foreach ($config['search'] as $key => $q) {
                                if ($q !== '') {
                                    $cond .= 'AND ';
                                    $p = 'p' . $nbc;
                                    switch ($key) {
                                        case 'id_vat':
                                            $cond .= 'p.' . $key . ' = :' . $p . ' ';
                                            break;
                                        case 'percent_vat':
                                            $cond .= 'p.' . $key . ' = :' . $p . ' ';
                                            break;
                                        case 'date_register':
                                            $q = $dateFormat->date_to_db_format($q);
                                            $cond .= "p." . $key . " LIKE CONCAT('%', :" . $p . ", '%') ";
                                            break;
                                    }
                                    $params[$p] = $q;
                                    $nbc++;
                                }
                            }

                            $sql = "SELECT p.*
						FROM mc_vat AS p $cond" . $limit;
                        }
                    }
                    break;
                case 'page':
                    $sql = 'SELECT p.*
							FROM mc_vat AS p
							WHERE p.id_vat = :edit';
                    break;
                case 'lastPages':
                    $sql = "SELECT p.*
							FROM mc_vat AS p
							ORDER BY p.id_vat DESC
							LIMIT 1";
                    break;
            }

            return $sql ? component_routing_db::layer()->fetchAll($sql, $params) : null;
        }
		elseif ($config['context'] === 'one') {
            switch ($config['type']) {
                case 'root':
                    $sql = 'SELECT * FROM mc_vat ORDER BY id_vat DESC LIMIT 0,1';
                    break;
                case 'page':
                    $sql = 'SELECT * FROM mc_vat WHERE `id_vat` = :id_vat';
                    break;
                case 'category':
                    $sql = 'SELECT * FROM mc_vat_category 
                            WHERE id_cat = :id';
                    break;
                case 'catProduct':
                    $sql = 'SELECT mv.percent_vat
                            FROM mc_vat_category AS vatcat
                            JOIN mc_catalog AS catalog ON(catalog.id_cat = vatcat.id_cat)
                            JOIN mc_vat mv ON (vatcat.id_vat = mv.id_vat)
                            WHERE catalog.id_product = :id';
                    break;
            }

            return $sql ? component_routing_db::layer()->fetch($sql, $params) : null;
        }
    }
    /**
     * @param $config
     * @param array $params
     * @return bool|string
     */
    public function insert($config,$params = array())
    {
        if (!is_array($config)) return '$config must be an array';

        $sql = '';

        switch ($config['type']) {
            case 'page':
                $sql = "INSERT INTO mc_vat (percent_vat, date_register)
                        VALUE (:percent_vat, NOW())";
                break;
            case 'category':
                $sql = "INSERT INTO mc_vat_category (id_vat, id_cat, date_register)
                        VALUE (:id_vat, :id_cat, NOW())";
                break;
        }

        if($sql === '') return 'Unknown request asked';

        try {
            component_routing_db::layer()->insert($sql,$params);
            return true;
        }
        catch (Exception $e) {
            return 'Exception reçue : '.$e->getMessage();
        }
    }
    /**
     * @param $config
     * @param array $params
     * @return bool|string
     */
    public function update($config,$params = array())
    {
        if (!is_array($config)) return '$config must be an array';

        $sql = '';

        switch ($config['type']) {
            case 'page':
                $sql = 'UPDATE mc_vat 
						SET 
							percent_vat = :percent_vat

                		WHERE id_vat = :id_vat';
                break;
            case 'category':
                $sql = 'UPDATE mc_vat_category 
						SET 
							id_vat = :id_vat

                		WHERE id_cat = :id_cat';
                break;
            /*case 'order':
                $sql = 'UPDATE mc_vat
						SET order_tr = :order_tr
                		WHERE id_vat = :id_vat';
                break;*/
        }

        if($sql === '') return 'Unknown request asked';

        try {
            component_routing_db::layer()->update($sql,$params);
            return true;
        }
        catch (Exception $e) {
            return 'Exception reçue : '.$e->getMessage();
        }
    }
    /**
     * @param $config
     * @param array $params
     * @return bool|string
     */
    public function delete($config, $params = array())
    {
        if (!is_array($config)) return '$config must be an array';
        $sql = '';

        switch ($config['type']) {
            case 'delPages':
                $sql = 'DELETE FROM mc_vat 
						WHERE id_vat IN ('.$params['id'].')';
                $params = array();
                break;
        }

        if($sql === '') return 'Unknown request asked';

        try {
            component_routing_db::layer()->delete($sql,$params);
            return true;
        }
        catch (Exception $e) {
            return 'Exception reçue : '.$e->getMessage();
        }
    }
}