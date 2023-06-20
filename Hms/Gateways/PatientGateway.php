<?php

namespace Hms\Gateways;

class PatientGateway extends BasicTableGateway {

    protected $table = "patient";
    protected $fields = ['id' => 'i', 'firstname' => 's', 'lastname' => 's', 'street' => 's', 'zip' => 'i', 'place' => 's'];
}