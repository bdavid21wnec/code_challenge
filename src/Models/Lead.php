<?php
namespace Src\Models;

use Src\Contracts\iModel;

class Lead implements iModel {

    private $id;

    private $email;

    private $firstName;

    private $lastName;

    private $address;

    private $entryDate;

    public function serialize()
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'address' => $this->address,
            'entryDate' => $this->entryDate->format(DATE_RFC3339),
        ];
    }

    public function setId($id)
    {
        // validate id is alpha numeric
        if (!ctype_alnum($id)) {
            throw new Exception("Invalid id: $id");
        }

        $this->id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setEmail($email)
    {
        // validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid Email: $email");
        }

        $this->email = $email;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setFirstName($firstName)
    {
        // validate is alpha
        if (!ctype_alpha($firstName)) {
            throw new Exception("Invalid First Name: $firstName");
        }

        $this->firstName = $firstName;

        return $this;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setLastName($lastName)
    {
        // validate is alpha
        if (!ctype_alpha($lastName)) {
            throw new Exception("Invalid Last Name: $lastName");
        }

        $this->lastName = $lastName;

        return $this;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setEntryDate($date)
    {
        if (!$date instanceof \DateTime) {
            $this->entryDate = new \DateTime($date);
        } else {
            $this->entryDate = $date;
        }

        return $this;
    }

    public function getEntryDate() : \DateTime
    {
        return $this->entryDate;
    }
}
