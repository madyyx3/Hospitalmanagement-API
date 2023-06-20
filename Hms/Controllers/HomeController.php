<?php

namespace Hms\Controllers;

//use App\Models\Category;
use Hms\Models\Patient;
use Hms\Models\Appointment;
use Hms\Models\Record;

class HomeController extends DefaultController
{
    public function index()
    {
        /*return $this->render("index.html.twig", [
            'categories' => Category::all()
        ]);*/

        return $this->render("home.html.twig", [
            'patients' => Patient::all(),
            'appointments' => Appointment::all(),
            'records' => Record::all()
        ]);
    }
}