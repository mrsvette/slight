<?php
namespace ExtensionsModel;

require_once __DIR__ . '/../../../models/base.php';

class ClientOrderModel extends \Model\BaseModel
{
    const STATUS_PENDING_SETUP = "pending_setup";
    const STATUS_FAILED_SETUP = "failed_setup";
    const STATUS_ACTIVE = "active";
    const STATUS_CANCELED = "canceled";
    const STATUS_SUSPENDED = "suspended";
    const INVOICE_OPTION_NO_INVOICE = "no-invoice";
    const INVOICE_OPTION_ISSUE_INVOICE = "issue-invoice";

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ext_client_order';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['client_id', 'required'],
        ];
    }
}
