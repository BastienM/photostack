<?

namespace Application\Model;

class Imuploadeds
{
    public $id;
    public $url;
    public $name;
    public $uploaded;
    public $owner;
    
    public function exchangeArray($data)
    {
        $this->id            = (isset($data['id'])) ? $data['id'] : null;
        $this->url           = (isset($data['url'])) ? $data['url'] : null;
        $this->name          = (isset($data['name'])) ? $data['name'] : null;
        $this->uploaded      = (isset($data['uploaded'])) ? $data['uploaded'] : null;
        $this->owner         = (isset($data['owner'])) ? $data['owner'] : null;
    }
}