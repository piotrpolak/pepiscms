<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * {module_name} model
 *
 * @author {author}
 * @date {date}
 */
class {model_class_name} extends Generic_model
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable('{module_databse_table_name}');
        $this->setIdFieldName('id');
//        $this->enableJournaling();

        // Required by saveById method
        $this->setAcceptedPostFields(array({coma_separated_list_of_fields}));
    }

}
