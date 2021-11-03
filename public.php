<?php
/**
 * Class plugins_attribute_public
 */
class plugins_vat_public extends plugins_vat_db
{
    /**
     * @var object
     */
    protected $template, $data, $modelCatalog;

    /**
     * @var int $id
     */
    protected $id, $cart, $settingComp,$settings;
    public $contentData;

    /**
     * frontend_controller_home constructor.
     * @param stdClass $t
     */
    public function __construct($t = null)
    {
        $this->template = $t instanceof frontend_model_template ? $t : new frontend_model_template();
        $formClean = new form_inputEscape();
        $this->data = new frontend_model_data($this, $this->template);
        $this->settingComp = new component_collections_setting();
        $this->settings = $this->settingComp->getSetting();

        if (http_request::isGet('id')) $this->id = $formClean->numeric($_GET['id']);
        if (http_request::isPost('contentData')) $this->contentData = $formClean->arrayClean($_POST['contentData']);
    }

    /**
     * Assign data to the defined variable or return the data
     * @param string $type
     * @param string|int|null $id
     * @param string $context
     * @param boolean $assign
     * @return mixed
     */
    private function getItems($type, $id = null, $context = null, $assign = true)
    {
        return $this->data->getItems($type, $id, $context, $assign);
    }
    /**
     * @param $row
     * @return array
     */
    private function setItemData($row)
    {
        $data = array();
        if ($row != null) {
            $data['id'] = $row['id_tr'];
            $data['name'] = $row['name_tr'];
            $data['postcode'] = $row['postcode_tr'];
            $data['price'] = $row['price_tr'];
        }
        return $data;
    }

    /**
     * @return array|null
     */
    public function getBuildList(){
        $collection = $this->getItems('pages',NULL, 'all', false);
        if($collection != null) {
            $newarr = array();
            foreach ($collection as &$item) {
                $newarr[] = $this->setItemData($item);
            }
            return $newarr;
        }else{
            return null;
        }
    }

    // ---- Cartpay

    /**
     * @param $params
     * @return mixed
     */
    public function impact_product_vat_rate($params){
        $catproduct = $this->getItems('catProduct',array('id'=>$params['id_product']), 'one', false);

        if($catproduct != null){
            $vat_rate =  $catproduct['percent_vat'];
        }else{
            $vat_rate = $this->settings['vat_rate']['value'];
        }

        return $vat_rate;

    }
    // ---- End Cartpay
}