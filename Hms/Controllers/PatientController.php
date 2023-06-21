<?php

namespace Hms\Controllers;

use Hms\Models\Patient;

class PatientController extends DefaultController
{
    public function index()
    {
        $patients = Patient::all();

        echo json_encode($patients, JSON_UNESCAPED_UNICODE);
    }

    public function save(array $data)
    {
        $this->validate($data, [
            'firstname' => 'required',
            'lastname' => 'required',
            'street' => 'required',
            'zip' => 'required|alphanumeric',
            'place' => 'required'
        ]);

        $patient = new Patient();
        $patient->setFirstname($data['firstname']);
        $patient->setLastname($data['lastname']);
        $patient->setStreet($data['street']);
        $patient->setZip($data['zip']);
        $patient->setPlace($data['place']);
        $patient->save();

        $patients = Patient::all();
        $patient_last = end($patients);

        echo json_encode($patient_last);
    }

    public function update(array $data)
    {
        $patient = Patient::findById($data['id']);
        $patient->setFirstname($data['firstname']);
        $patient->setLastname($data['lastname']);
        $patient->setStreet($data['street']);
        $patient->setZip($data['zip']);
        $patient->setPlace($data['place']);
        $patient->save();

        echo json_encode($patient);
    }

    public function delete(int $id)
    {
        $patient = Patient::findById($id);
        $patient->delete();

        echo json_encode([
            "id" => $id
        ]);
    }
}