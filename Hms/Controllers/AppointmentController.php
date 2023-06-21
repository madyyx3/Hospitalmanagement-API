<?php

namespace Hms\Controllers;

use Hms\Models\Appointment;
use Carbon\Carbon;

// createFromFormat('format', $data) -> wenn ich fix definiertes Format habe, wäre es besser das zu verwenden um mehr Kontrolle darüber zu haben
// parse($data) -> wenn die Datum/Zeit Daten variieren wäre das der bessere Fall

class AppointmentController extends DefaultController
{
    public function index(int $patientId = 0)
    {
        $appointments = Appointment::all();

        if ($patientId > 0) {
            $appointments = Appointment::findByPatientId($patientId);
        }

        echo json_encode($appointments, JSON_UNESCAPED_UNICODE);
    }

    public function save(array $data)
    {
        $this->validate($data, [
            'patient' => 'greater_zero',
            'start' => 'required',
            'end' => 'required'
        ]);

        $appointment = new Appointment();
        $appointment->setPatientId($data['patient']);
        $appointment->setStart(Carbon::parse($data['start']));
        $appointment->setEnd(Carbon::parse($data['end']));
        $appointment->save();

        $appointments = Appointment::all();
        $appointment_last = end($appointments);

        echo json_encode($appointment_last);
    }

    public function update(array $data)
    {
        $appointment = Appointment::findById($data['id']);
        $appointment->setPatientId($data['patient']);
        $appointment->setStart(Carbon::parse($data['start']));
        $appointment->setEnd(Carbon::parse($data['end']));
        $appointment->save();

        echo json_encode($appointment);
    }

    public function delete(int $id)
    {
        $appointment = Appointment::findById($id);
        $appointment->delete();

        echo json_encode([
            "id" => $id
        ]);
    }
}