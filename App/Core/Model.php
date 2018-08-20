<?php namespace App\Core;


use App\Config;
use PDO;

/**
 * Class Model
 * @package App\Core
 */
class Model
{
    /**
     * @var null|PDO
     */
    public $db = null;

    /**
     * Number of records to take from DB
     *
     * @var string
     */
    protected $limit = '';

    /**
     * @var int|null
     */
    protected $perPage = null;

    /**
     * Number of records to skip
     *
     * @var int
     */
    protected $skip = 0;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Total number of records in DB
     *
     * @var int
     */
    public $count = 0;

    /**
     * Sort order used to get data from DB
     *
     * @var array
     */
    private $sort = ['column' => 'id', 'order' => 'asc'];

    /**
     * Timestamp column name. Set to false if no timestamp needed
     *
     * @var string|bool
     */
    protected $timestamp = false;

    /**
     * Models table name in DB
     *
     * @var string
     */
    protected $table = '';

    /**
     * Models fillable columns.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Model constructor.
     */
    public function __construct()
    {
        if ($this->db === null) {
            $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_DATABASE . ';charset=utf8';
            $this->db = new PDO($dsn, Config::DB_USERNAME, Config::DB_PASSWORD);
            // Throw an Exception when an error occurs
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    /**
     * Get all rows from db or paginate if pagination is set from paginate method
     * and order all rows in order that is set from sort method
     *
     * @return Model
     */
    public function all(): Model
    {
        //set total count of rows.
        $this->setCount();

        //prepare query
        $query = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY {$this->sort['column']} {$this->sort['order']} LIMIT :skip, :perPage");
        //bind query values for prepared query
        $query->bindValue(':perPage', $this->perPage ?: $this->count, PDO::PARAM_INT);
        $query->bindValue(':skip', $this->skip, PDO::PARAM_INT);
        //execure query
        $query->execute();
        //bind all query data
        $this->data = $query->fetchAll(PDO::FETCH_OBJ);

        return $this;
    }

    /**
     * Set pagination values and get paginated data from db
     *
     * @param int $page
     * @param int $perPage
     * @return Model
     */
    public function paginate(int $page = 1, int $perPage = 10): Model
    {
        $this->skip = --$page * $perPage;
        $this->perPage = $perPage;
        return $this->all();
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Return number or pages
     *
     * @return int
     */
    public function getPages(): int
    {
        return $this->perPage ? (int)ceil($this->count / $this->perPage) : 1;
    }

    /**
     *  Get total count of db rows and set them into variable
     */
    private function setCount(): void
    {
        $query = $this->db->prepare("SELECT count(*) as count FROM {$this->table}");
        $query->execute();
        $count = $query->fetchAll(PDO::FETCH_COLUMN);
        $this->count = $count[0] ?? 0;
    }

    /**
     * Set sorting variables to use them then getting data from db
     *
     * @param string $column
     * @param string $order
     * @return Model
     */
    public function sort(string $column, string $order): Model
    {
        $this->sort = ['column' => $column, 'order' => $order];
        return $this;
    }

    /**
     * Insert new record into db
     *
     * @param array $data
     * @return Model
     * @throws \Exception
     */
    public function create(array $data): Model
    {
        //generate sql from fillable fields of model
        $sql = $this->prepageInsertSql();

        $query = $this->db->prepare($sql);

        //If model needs timestamps insert them into data
        $data = $this->prepareTimestamps($data);

        //execute query and bind data
        $query->execute($data);

        //get inserted record from db and set it to data variable
        $this->data = [];
        $this->data[] = $this->getLastInserted();

        return $this;
    }

    /**
     * Get first record
     *
     * @return mixed|null
     */
    public function first()
    {
        return $this->data[0] ?? null;
    }

    /**
     * Get last inserted record
     *
     * @return mixed
     */
    private function getLastInserted()
    {
        //get last inserted id
        $id = $this->db->lastInsertId();

        //get record from db by last inserted id
        $query = $this->db->prepare("SELECT * FROM {$this->table} WHERE id=?");
        $query->execute([$id]);

        return $query->fetchObject();
    }

    /**
     * Each Model has to have set fillable parameter.
     * Insert sql is generated by fillable parameter.
     *
     * @return string
     * @throws \Exception
     */
    private function prepageInsertSql(): string
    {
        //throw error if no fillable parameter is set
        if (empty($this->fillable)) {
            throw new \Exception("No fillable columns are set");
        }

        //build columns and binding strings
        $columns = '';
        $bindings = '';
        foreach ($this->fillable as $column) {
            $columns .= $column . ", ";
            $bindings .= ':' . $column . ', ';
        }

        //if model needs timestamp add it to strings
        if ($this->timestamp) {
            $columns .= $this->timestamp;
            $bindings .= ':' . $this->timestamp;
        }
        //remove trailing commas
        $columns = rtrim($columns, ', ');
        $bindings = rtrim($bindings, ', ');


        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($bindings)";

        return $sql;
    }

    /**
     * Add current timestamp into data if Model requires timestamp
     *
     * @param array $data
     * @return array
     */
    private function prepareTimestamps(array $data): array
    {
        if (!$this->timestamp) {
            return $data;
        }

        if (isset($data[$this->timestamp]) && $data[$this->timestamp] != '') {
            return $data;
        }

        $data[$this->timestamp] = (new \DateTime("now"))->format("Y-m-d H:i:s");

        return $data;
    }


}
