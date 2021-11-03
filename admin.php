<?php
class plugins_vat_admin extends plugins_vat_db
{
    public $edit, $action, $tabs, $search, $plugin, $controller;
    protected $message, $template, $header, $data, $modelLanguage, $collectionLanguage, $order, $upload, $config, $modelPlugins, $routingUrl, $makeFiles, $finder, $plugins, $progress;
    public $id_vat, $content, $pages, $iso, $ajax, $tableaction, $tableform, $offset, $vatData;

    public $tableconfig = array(
        'all' => array(
            'id_vat',
            'percent_vat' => array('title' => 'name'),
            //'price_tr' => array('type' => 'price','input' => null),
            'date_register'
        )
    );
    /**
     * frontend_controller_home constructor.
     */
    public function __construct($t = null){
        $this->template = $t ? $t : new backend_model_template;
        $this->message = new component_core_message($this->template);
        $this->header = new http_header();
        $this->data = new backend_model_data($this);
        $formClean = new form_inputEscape();
        //$this->modelLanguage = new backend_model_language($this->template);
        //$this->collectionLanguage = new component_collections_language();
        $this->modelPlugins = new backend_model_plugins();
        $this->routingUrl = new component_routing_url();
        $this->finder = new file_finder();
        // --- GET
        if(http_request::isGet('controller')) $this->controller = $formClean->simpleClean($_GET['controller']);
        if (http_request::isGet('edit')) $this->edit = $formClean->numeric($_GET['edit']);
        if (http_request::isGet('action')) $this->action = $formClean->simpleClean($_GET['action']);
        elseif (http_request::isPost('action')) $this->action = $formClean->simpleClean($_POST['action']);
        if (http_request::isGet('tabs')) $this->tabs = $formClean->simpleClean($_GET['tabs']);
        if (http_request::isGet('ajax')) $this->ajax = $formClean->simpleClean($_GET['ajax']);
        if (http_request::isGet('offset')) $this->offset = intval($formClean->simpleClean($_GET['offset']));

        if (http_request::isGet('tableaction')) {
            $this->tableaction = $formClean->simpleClean($_GET['tableaction']);
            $this->tableform = new backend_controller_tableform($this,$this->template);
        }

        // --- Search
        if (http_request::isGet('search')) {
            $this->search = $formClean->arrayClean($_GET['search']);
            $this->search = array_filter($this->search, function ($value) { return $value !== ''; });
        }

        // --- ADD or EDIT
        if (http_request::isGet('id')) $this->id_vat = $formClean->simpleClean($_GET['id']);
        elseif (http_request::isPost('id')) $this->id_vat = $formClean->simpleClean($_POST['id']);

        if (http_request::isPost('vatData')) $this->vatData = $formClean->arrayClean($_POST['vatData']);
        // --- Recursive Actions
        if (http_request::isGet('vat'))  $this->pages = $formClean->arrayClean($_GET['vat']);
        # ORDER PAGE
        if (http_request::isPost('vat')) $this->order = $formClean->arrayClean($_POST['vat']);
        if (http_request::isGet('plugin')) $this->plugin = $formClean->simpleClean($_GET['plugin']);
        # JSON LINK (TinyMCE)
        //if (http_request::isGet('iso')) $this->iso = $formClean->simpleClean($_GET['iso']);
    }
    /**
     * Assign data to the defined variable or return the data
     * @param string $type
     * @param string|int|null $id
     * @param string $context
     * @param boolean $assign
     * @param boolean $pagination
     * @return mixed
     */
    private function getItems($type, $id = null, $context = null, $assign = true, $pagination = false) {
        return $this->data->getItems($type, $id, $context, $assign, $pagination);
    }
    /**
     * Method to override the name of the plugin in the admin menu
     * @return string
     */
    public function getExtensionName()
    {
        return $this->template->getConfigVars('vat_plugin');
    }
    /**
     * @param $ajax
     * @return mixed
     * @throws Exception
     */
    public function tableSearch($ajax = false)
    {
        $results = $this->getItems('pages', NULL, 'all',false,true);
        $params = array();

        if($ajax) {
            $params['section'] = 'pages';
            $params['idcolumn'] = 'id_vat';
            $params['activation'] = false;
            $params['sortable'] = true;
            $params['checkbox'] = true;
            $params['edit'] = true;
            $params['dlt'] = true;
            $params['readonly'] = array();
            $params['cClass'] = 'plugins_vat_admin';
        }

        $this->data->getScheme(array('mc_vat'),array('id_vat','percent_vat','date_register'),$this->tableconfig['all']);

        return array(
            'data' => $results,
            'var' => 'pages',
            'tpl' => 'index.tpl',
            'params' => $params
        );
    }

    /**
     * Update data
     * @param $data
     * @throws Exception
     */
    private function add($data)
    {
        switch ($data['type']) {
            case 'page':
                parent::insert(
                    array(
                        'context' => $data['context'],
                        'type' => $data['type']
                    ),
                    $data['data']
                );
                break;
        }
    }

    /**
     * Mise a jour des données
     * @param $data
     * @throws Exception
     */
    private function upd($data)
    {
        switch ($data['type']) {
            /*case 'order':
                $p = $this->order;
                for ($i = 0; $i < count($p); $i++) {
                    parent::update(
                        array(
                            'type'=>$data['type']
                        ),array(
                            'id_cs'       => $p[$i],
                            'order_cs'    => $i + (isset($this->offset) ? ($this->offset + 1) : 0)
                        )
                    );
                }
                break;*/
            case 'page':
                parent::update(
                    array(
                        'context' => $data['context'],
                        'type' => $data['type']
                    ),
                    $data['data']
                );
                break;
        }
    }
    /**
     * Insertion de données
     * @param $data
     * @throws Exception
     */
    private function del($data)
    {
        switch($data['type']){
            case 'delPages':
                parent::delete(
                    array(
                        'type' => $data['type']
                    ),
                    $data['data']
                );
                $this->message->json_post_response(true,'delete',$data['data']);
                break;
        }
    }

    /**
     * @throws Exception
     */
    public function run(){
        if(isset($this->tableaction)) {
            $this->tableform->run();
        }
        elseif(isset($this->action)) {
            switch ($this->action) {
                case 'add':
                    if(isset($this->vatData)){
                        $newdata = array();
                        $newdata['percent_vat'] = (!empty($this->vatData['percent_vat'])) ? number_format(str_replace(",", ".", $this->vatData['percent_vat']), 0, '.', '') : NULL;
                        // Add data
                        $this->add(array(
                            'type' => 'page',
                            'data' => $newdata
                        ));
                        $this->message->json_post_response(true, 'add_redirect');
                    }else{
                        $this->template->display('add.tpl');
                    }
                    break;
                case 'edit':
                    if(isset($this->vatData)){
                        $newdata = array();
                        $newdata['id_vat'] = $this->id_vat;
                        $newdata['percent_vat'] = (!empty($this->vatData['percent_vat'])) ? number_format(str_replace(",", ".", $this->vatData['percent_vat']), 0, '.', '') : NULL;
                        // Update data
                        $this->upd(array(
                            'type' => 'page',
                            'data' => $newdata
                        ));
                        $this->message->json_post_response(true, 'update', $this->vatData);
                    }else{
                        $this->getItems('page',array('id_vat'=>$this->edit),'one',true,true);
                        $this->template->display('edit.tpl');
                    }
                    break;
            }
        }else{
            $this->getItems('pages',NULL,'all',true,true);
            $this->data->getScheme(array('mc_vat'),array('id_vat','percent_vat','date_register'),$this->tableconfig['all']);
            $this->template->display('index.tpl');
        }
    }
}