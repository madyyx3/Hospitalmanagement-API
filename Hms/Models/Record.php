<?php

namespace Hms\Models;

use Carbon\Carbon;
use Hms\Gateways\RecordGateway;
use JsonSerializable;

class Record implements JsonSerializable
{
    private int $id = 0;
    private int $patientId;
    private string $description;
    private Carbon $created;
    private Carbon $updated;

    public function __construct(int $id = 0)
    {
        $this->id = $id;
    }

    public static function all(): array
    {
        $gateway = new RecordGateway();
        $records = [];

        $tmpRecords = $gateway->all();

        if ($tmpRecords) {
            while ($tmpRecord = $tmpRecords->fetch_assoc()) {
                $record = new Record();
                $record->id = $tmpRecord['id'];
                $record->patientId = $tmpRecord['patient_id'];
                $record->description = $tmpRecord['description'];
                $record->created = Carbon::createFromFormat('Y-m-d H:i:s', $tmpRecord['created_at'])->setTimezone('Europe/Zurich');
                $record->updated = Carbon::parse($tmpRecord['updated_at'])->setTimezone('Europe/Zurich');
          
                $records[] = $record;
            }
        }

        return $records;
    }

    public static function findById(int $id): ?Record
    {
        $gateway = new RecordGateway();

        $tmpRecord = $gateway->findById($id);

        $record = null;

        if ($tmpRecord) {
            $record = new Record();
            $record->id = $tmpRecord['id'];
            $record->patientId = $tmpRecord['patient_id'];
            $record->description = $tmpRecord['description'];
            $record->created = Carbon::createFromFormat('Y-m-d H:i:s', $tmpRecord['created_at'])->setTimezone('Europe/Zurich');
            $record->updated = Carbon::parse($tmpRecord['updated_at'])->setTimezone('Europe/Zurich');
        }

        return $record;
    }

    public static function findByPatientId(int $recordId): array
    {
        $gateway = new RecordGateway();
        $records = [];

        $tmpRecords = $gateway->findByFields([
            'patient_id' => $recordId
        ]);

        if ($tmpRecords) {
            while ($tmpRecord = $tmpRecords->fetch_assoc()) {
                $record = new Record();
                $record->id = $tmpRecord['id'];
                $record->patientId = $tmpRecord['patient_id'];
                $record->description = $tmpRecord['description'];
                $record->created = Carbon::createFromFormat('Y-m-d H:i:s', $tmpRecord['created_at'])->setTimezone('Europe/Zurich');
                $record->updated = Carbon::parse($tmpRecord['updated_at'])->setTimezone('Europe/Zurich');

                $records[] = $record;
            }
        }

        return $records;
    }

    public function save()
    {
        $gateway = new RecordGateway();

        if (!$this->id) {
            $gateway->insert([
                'patient_id' => $this->patientId,
                'description' => $this->description
            ]);
        } else {
            $gateway->update($this->id, [
                'patient_id' => $this->patientId,
                'description' => $this->description
            ]);
        }
    }

    public function delete()
    {
        $gateway = new RecordGateway();
        $gateway->delete($this->id);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    public function setPatientId(int $patientId)
    {
        $this->patientId = $patientId;
    }

    public function getPatient(): ?Patient
    {
        $gateway = new RecordGateway();

        $tmpPatient = $gateway->getRelation($this->patientId, "patient", "1");
        $patient = null;

        if ($tmpPatient  && $tmpPatient->num_rows == 1) {
            $tmpPatient = $tmpPatient->fetch_assoc();
            $patient = new Patient($tmpPatient['id']);
            $patient->setFirstname($tmpPatient['firstname']);
            $patient->setLastname($tmpPatient['lastname']);
            $patient->setStreet($tmpPatient['street']);
            $patient->setZip($tmpPatient['zip']);
            $patient->setPlace($tmpPatient['place']);
        }

        return $patient;
    }

    function getCreated(): Carbon { 
        return $this->created; 
    }

    function getUpdated(): Carbon { 
        return $this->updated; 
    } 

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'patient' => $this->getPatient(),
            'description' => $this->description,
            'created_at' => $this->created->format('Y-m-d\ H:i'),
            'updated_at' => $this->updated->format('Y-m-d\ H:i')
        ];
    }
}