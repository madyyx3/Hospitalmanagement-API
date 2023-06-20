<?php

namespace Hms\Gateways;

class RecordGateway extends BasicTableGateway {

    protected $table = "record";
    protected $fields = ['id' => 'i', 'patient_id' => 'i', 'description' => 's' ];
}