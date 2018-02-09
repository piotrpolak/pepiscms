<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * {module_name} controller
 *
 * @author {author}
 * @date {date}
 * @classTemplateVersion 20130529
 */
class {module_class_name} extends ModuleController
{
    public function __construct()
    {
        parent::__construct();

        /* Getting module name from class name */
        $module_name = strtolower(__CLASS__);

        /* Loading module language, model, libraries, helpers */
        $this->load->language($module_name);
        $this->load->model('{model_class_name}');

        /* Setting meta data */
        $this->document->setTitle('Module {module_class_name}');
        $this->document->setDescription('Module {module_class_name}');
    }

    public function index()
    {
        $items = $this->{model_class_name}->getAll();
        if (!$items) {
            show_404();
        }

        $this->assign('items', $items);
        $this->display();
    }

    public function item()
    {
        $id = $this->input->getParam('id');
        if (!$id) {
            show_404();
        }
        $item = $this->{model_class_name}->getById($id);
        if (!$item) {
            show_404();
        }

        $this->assign('item', $item);
        $this->display();
    }
}
