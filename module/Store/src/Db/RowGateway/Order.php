<?php
namespace Store\Db\RowGateway;
use Application\Db\RowGateway\RowGateway;
use Laminas\Db\RowGateway\Exception\InvalidArgumentException;
use Laminas\Db\RowGateway\Exception\RuntimeException;
use Laminas\Json\Json;
class Order extends RowGateway
{
    /**
     * 
     * @var int $id|$this->data['id']
     */
    protected $id;
    /**
     * 
     * @var int $userId|$this->data['userId']
     */
    protected $userId;
    /**
     * 
     * @var string $createdDate|$this->data['createdDate']
     */
    protected $createdDate;
    /**
     * 
     * @var string $processedDate|$this->data['processedDate']
     */
    protected $processedDate;
    /**
     * 
     * @var int|bool $completed|$this->data['completed']
     */
    protected $completed;
    /**
     * 
     * @var [] $orderData|$this->data['orderData'][]
     */
    protected $orderData = [
        'products' => [],
    ];
    /**
     * @var Json $json
     */
    public $json;
    /**
     * 
     * @param string $primaryKeyColumn 
     * @param string $table 
     * @param mixed $adapterOrSql 
     * @return void 
     * @throws InvalidArgumentException 
     * @throws RuntimeException 
     */
    public function __construct($primaryKeyColumn = 'id', $table = 'store_orders', $adapterOrSql = null)
    {
        parent::__construct($primaryKeyColumn, $table, $adapterOrSql);
        $this->json = new Json();
    }

}