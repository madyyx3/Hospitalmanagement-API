<?php

namespace Hms\Gateways;

class AppointmentGateway extends BasicTableGateway {

    protected $table = "appointment";
    protected $fields = ['id' => 'i', 'patient_id' => 'i', 'start' => 's', 'end' => 's'];
}