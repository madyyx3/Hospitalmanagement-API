<?php

namespace Hms\Models;

use Hms\Gateways\AppointmentGateway;
use Carbon\Carbon;
use JsonSerializable;

class Appointment implements JsonSerializable
{
    private int $id = 0;
    private int $patientId;
    private Carbon $start;
    private Carbon $end;

    public function __construct(int $id = 0)
    {
        $this->id = $id;
    }

    public static function all(): array
    {
        $gateway = new AppointmentGateway();
        $appointments = [];

        $tmpAppointments = $gateway->all();

        if ($tmpAppointments) {
            while ($tmpAppointment = $tmpAppointments->fetch_assoc()) {
                $appointment = new Appointment();
                $appointment->id = $tmpAppointment['id'];
                $appointment->patientId = $tmpAppointment['patient_id'];
                $appointment->start = Carbon::createFromFormat('Y-m-d H:i:s', $tmpAppointment['start']);
                $appointment->end = Carbon::createFromFormat('Y-m-d H:i:s', $tmpAppointment['end']);
          
                $appointments[] = $appointment;
            }
        }

        return $appointments;
    }

    public static function findById(int $id): ?Appointment
    {
        $gateway = new AppointmentGateway();

        $tmpAppointment = $gateway->findById($id);

        $appointment = null;

        if ($tmpAppointment) {
            $appointment = new Appointment();
            $appointment->id = $tmpAppointment['id'];
            $appointment->patientId = $tmpAppointment['patient_id'];
            $appointment->start = Carbon::createFromFormat('Y-m-d H:i:s', $tmpAppointment['start']);
            $appointment->end = Carbon::createFromFormat('Y-m-d H:i:s', $tmpAppointment['end']);
        }

        return $appointment;
    }

    public static function findByPatientId(int $appointmentId): array
    {
        $gateway = new AppointmentGateway();
        $appointments = [];

        $tmpAppointments = $gateway->findByFields([
            'patient_id' => $appointmentId
        ]);

        if ($tmpAppointments) {
            while ($tmpAppointment = $tmpAppointments->fetch_assoc()) {
                $appointment = new Appointment();
                $appointment->id = $tmpAppointment['id'];
                $appointment->patientId = $tmpAppointment['patient_id'];
                $appointment->start = Carbon::createFromFormat('Y-m-d H:i:s', $tmpAppointment['start']);
                $appointment->end = Carbon::createFromFormat('Y-m-d H:i:s', $tmpAppointment['end']);

                $appointments[] = $appointment;
            }
        }

        return $appointments;
    }

    public function save()
    {
        $gateway = new AppointmentGateway();

        if (!$this->id) {
            $gateway->insert([
                'patient_id' => $this->patientId,
                'start' => $this->start,
                'end' => $this->end
            ]);
        } else {
            $gateway->update($this->id, [
                'patient_id' => $this->patientId,
                'start' => $this->start,
                'end' => $this->end
            ]);
        }
    }

    public function delete()
    {
        $gateway = new AppointmentGateway();
        $gateway->delete($this->id);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStart(): Carbon
    {
        return $this->start;
    }

    public function setStart(Carbon $start)
    {
        $this->start = $start;
    }

    public function getEnd(): Carbon
    {
        return $this->end;
    }

    public function setEnd(Carbon $end)
    {
        $this->end = $end;
    }

    public function setPatientId(int $patientId)
    {
        $this->patientId = $patientId;
    }

    public function getPatient(): ?Patient
    {
        $gateway = new AppointmentGateway();

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

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'patient' => $this->getPatient(),
            'start' => $this->start->format('Y-m-d\ H:i'),
            'end' => $this->end->format('Y-m-d\ H:i')
        ];
    }
    // 'start' => $this->start->toIso8601String(),
}