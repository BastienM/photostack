<?

namespace Application\Model;

class Users
{
    private $pseudo;
    private $password;
    private $mail;
    private $age
    
    public function exchangeArray($data)
    {
        $this->pseudo   = (isset($data['pseudo'])) ? $data['pseudo'] : null;
        $this->password = (isset($data['password'])) ? $data['password'] : null;
        $this->mail     = (isset($data['mail'])) ? $data['mail'] : null;
        $this->age      = (isset($data['age'])) ? $data['age'] : null;
    }
}