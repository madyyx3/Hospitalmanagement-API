<?php

namespace Hms\Controllers;

use Hms\Models\Record;

class RecordController extends DefaultController
{
    public function index(int $patientId = 0)
    {
        $records = Record::all();

        if ($patientId > 0) {
            $records = Record::findByPatientId($patientId);
        }

        echo json_encode($records, JSON_UNESCAPED_UNICODE);
    }

    public function save(array $data)
    {
        $this->validate($data, [
            'patient' => 'greater_zero',
            'description' => 'required'
        ]);

        $record = new Record();
        $record->setPatientId($data['patient']);
        $record->setDescription($data['description']);
        $record->save();

        // 2te variante, wenn insert -> return insert_id (funktion von php für db)
        //$id = $record->save();
        //$record = Record::findById($id);
        // get last record
        $records = Record::all();
        $record_last = end($records); // aber da basictablegateway 0 returnt 

        //$record_id = Record::findById($record->getId());

        echo json_encode($record_last);
    }

    public function update(array $data)
    {
        $record = Record::findById($data['id']);
        $record->setPatientId($data['patient']);
        $record->setDescription($data['description']);
        $record->save();

        $records = Record::all();
        $record_last = end($records); 

        echo json_encode($record_last);
    }

    public function delete(int $id)
    {
        $record = Record::findById($id);
        $record->delete();

        echo json_encode([
            "id" => $id
        ]);
    }
}