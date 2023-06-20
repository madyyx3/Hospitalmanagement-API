<?php

namespace Hms\Models;

use Hms\Gateways\PatientGateway;
use JsonSerializable;

class Patient implements JsonSerializable
{
    private int $id = 0;
    private string $firstname;
    private string $lastname;
    private string $street;
    private int $zip;
    private string $place;

    public function __construct(int $id = 0)
    {
        $this->id = $id;
    }

    public static function all(): array
    {
        $gateway = new PatientGateway();
        $patients = [];

        $tmpPatients = $gateway->all();

        if ($tmpPatients) {
            while ($tmpPatient = $tmpPatients->fetch_assoc()) {
                $patient = new Patient();
                $patient->id = $tmpPatient['id'];
                $patient->firstname = $tmpPatient['firstname'];
                $patient->lastname = $tmpPatient['lastname'];
                $patient->street = $tmpPatient['street'];
                $patient->zip = $tmpPatient['zip'];
                $patient->place = $tmpPatient['place'];

                $patients[] = $patient;
            }
        }

        return $patients;
    }

    public static function findById(int $id): ?Patient
    {
        $gateway = new PatientGateway();

        $tmpPatient = $gateway->findById($id);

        $patient = null;

        if ($tmpPatient) {
            $patient = new Patient();
            $patient->id = $tmpPatient['id'];
            $patient->firstname = $tmpPatient['firstname'];
            $patient->lastname = $tmpPatient['lastname'];
            $patient->street = $tmpPatient['street'];
            $patient->zip = $tmpPatient['zip'];
            $patient->place = $tmpPatient['place'];
        }

        return $patient;
    }

    public function save()
    {
        $gateway = new PatientGateway();

        if (!$this->id) {
            $gateway->insert([
                'firstname' => $this->firstname,
                'lastname' => $this->lastname,
                'street' => $this->street,
                'zip' => $this->zip,
                'place' => $this->place
            ]);
        } else {
            $gateway->update($this->id, [
                'firstname' => $this->firstname,
                'lastname' => $this->lastname,
                'street' => $this->street,
                'zip' => $this->zip,
                'place' => $this->place
            ]);
        }
    }

    public function delete()
    {
        $gateway = new PatientGateway();
        $gateway->delete($this->id);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname)
    {
        $this->firstname = $firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname)
    {
        $this->lastname = $lastname;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street)
    {
        $this->street = $street;
    }

    public function getZip(): int { 
        return $this->zip; 
    } 

    public function setZip(int $zip) {  
        $this->zip = $zip;
    }

    public function getPlace(): string { 
        return $this->place; 
    } 

    function setPlace(string $place) {  
        $this->place = $place; 
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'street' => $this->street,
            'zip' => $this->zip,
            'place' => $this->place
        ];
    }
}