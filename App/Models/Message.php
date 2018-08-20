<?php namespace App\Models;

use App\Core\Model;

/**
 * Class Message
 * @package App\Models
 */
class Message extends Model
{
    /**
     * Table name in DB
     *
     * @var string
     */
    public $table = 'messages';

    /**
     * Timestamp column name
     *
     * @var string
     */
    protected $timestamp = 'createdAt';

    /**
     * Fillable columns
     *
     * @var array
     */
    protected $fillable = [
        'firstName',
        'lastName',
        'dateOfBirth',
        'email',
        'message'
    ];

    /**
     * Count age and add it to model
     *
     * @return Message
     */
    public function addAge(): Message
    {
        foreach ($this->data as $key => $message) {
            //get birth date as DateTime object
            $time = strtotime($message->dateOfBirth);
            $dateOfBirth = new \DateTime(date('Y-m-d', $time));
            //Get current date as DateTime object
            $now = new \DateTime("now");
            //Count difference
            $years = date_diff($dateOfBirth, $now);
            //Add age in years into model array
            $this->data[$key]->age = $years->y;
        }
        return $this;
    }

    /**
     * Get paginated messages with person age
     *
     * @param int $page
     * @return array
     */
    public function getPaginatedMessages($page = 1): array
    {
        $messages = $this->sort('createdAt', 'DESC')
            ->paginate($page)
            ->addAge()
            ->getData();

        return $messages;
    }

}
