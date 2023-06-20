let modalInstanceAppointment = null;
let modalElemAppointment = document.querySelector('#appointmentFormModal');

modalElemAppointment.addEventListener('shown.bs.modal', function (){
  modalInstanceAppointment = bootstrap.Modal.getInstance(modalElemAppointment);
});

modalElemAppointment.addEventListener('hidden.bs.modal', function() {
    document.querySelector('#appointmentId').value = 0;
    document.querySelector('#patient').value = 0;
    document.querySelector('#start').value = "";
    document.querySelector('#end').value = "";
});

/* appointments stuff */

function loadAppointments()
{
    fetch("/appointments")
        .then(response => response.json())
        .then(data =>{
            console.log(data); // ausgabe console
            displayAppointments(data);
        });
}

// "Polling" -> methode die regelmässig status von etwas prüft, promise-objekt was gelöst oder abgelehnt wird
function getAppointmentTableShow() {
    return new Promise((resolve, reject) => {
      function checkElement() {
        const tbody = document.querySelector('#appointmentTableShow'); // hier wird element nach id gesucht
        if (tbody) {
          resolve(tbody); // falls gefunden wirds ausgelöst
        } else {
          setTimeout(checkElement, 100); // timeout falls nicht gefunden
        }
      }
      checkElement(); // in diesem fall ist es eine Rekursion und das else dient als "break", indem fall neuer versuch nach timeout
    });
}

function filterAppointments()
{
    let patientId = document.querySelector("#patientFilter").value;
    getAppointmentTableShow().then(tbody => {
        tbody.innerHTML = "";  // leert nur den Inhalt der Tabelle anstatt den ganzen Container
        fetch("/appointments?patient=" + patientId)
            .then(response => response.json())
            .then(data => displayFilteredAppointments(data)); // nachdem leeren, wird hier appointments nach patient geladen
        });
}
  

function saveAppointment()
{
    const appointmentId = document.querySelector('#appointmentId').value;

    if( appointmentId != null && appointmentId > 0 )
    {
        updateAppointment( appointmentId );
    }
    else
    {
        postAppointment();
    }

    return false;
}

function postAppointment()
{
  const patient = document.querySelector("#patient").value;
  const start = document.querySelector("#start").value;
  const end = document.querySelector("#end").value;

  fetch("/appointments", {
      method: 'POST',
      body: JSON.stringify({
          patient: patient,
          start: start,
          end: end
      }),
      headers: {
          'Content-Type': 'application/json; charset=UTF-8'
      }

    })
    .then( response => {
        
        if( response.ok )
        {
            return response.json();
        }
        
        return response.text().then(error => { throw new Error(error) })
        
    } )
    .then(appointment => {
      showNewAppointment(appointment); // um den neuen Termin anzuzeigen
      loadAppointments(); // danach loadAppointments um tabelle zu refreshen ohne web refresh
    })
    .catch(error => {
        const errors = JSON.parse(error.message);

        showErrorsAppointment(errors);
    });
}

function showErrorsAppointment( errors )
{
    if( errors.patient )
    {
        document.querySelector('#error-patient').className = "fw-bold text-danger";
        document.querySelector('#error-patient').innerHTML = errors.patient;
    }
    else
    {
        document.querySelector('#error-patient').className = "d-none fw-bold text-danger";
        document.querySelector('#error-patient').innerHTML = "";
    }

    if( errors.start )
    {
        document.querySelector('#error-start').className = "fw-bold text-danger";
        document.querySelector('#error-start').innerHTML = errors.start;
    }
    else
    {
        document.querySelector('#error-start').className = "d-none fw-bold text-danger";
        document.querySelector('#error-start').innerHTML = "";
    }

    if( errors.end )
    {
        document.querySelector('#error-end').className = "fw-bold text-danger";
        document.querySelector('#error-end').innerHTML = errors.end;
    }
    else
    {
        document.querySelector('#error-end').className = "d-none fw-bold text-danger";
        document.querySelector('#error-end').innerHTML = "";
    }
}

function updateAppointment( id )
{
  const patient = document.querySelector("#patient").value;
  const start = document.querySelector("#start").value;
  const end = document.querySelector("#end").value;

  fetch("/appointments", {
      method: 'PUT',
      body: JSON.stringify({
          id: id,
          patient: patient,
          start: start,
          end: end
      }),
      headers: {
          'Content-Type': 'application/json; charset=UTF-8'
      }

  })
    .then( response => response.json() )
    .then( data => {
        updateRowAppointment(data);
        modalInstanceAppointment.hide();
    } );
}

function deleteAppointment( id )
{
    fetch("/appointments", {
        method: 'DELETE',
        body: JSON.stringify({
            id: id
        }),
        headers: {
            'Content-Type': 'application/json; charset=UTF-8'
        }

    })
    .then( response => response.json() )
    .then( data => removeAppointment( data.id ));
}

function displayFilteredAppointments(appointments)
{
    appointments.forEach( appointment => {
        showNewAppointment(appointment);
    });
}

function displayAppointments(appointments)
{
    let tbody = document.querySelector('#appointmentTableShow');
    tbody.innerHTML = "";

    appointments.forEach( appointment => {
        showNewAppointment(appointment);
    });

    document.querySelector('#appointmentShow').className = "";
}

async function showNewAppointment( appointment )
{
    let tbody = await getAppointmentTableShow();
    //let tbody = document.querySelector('#appointmentTableShow');

    let tr = document.createElement("tr");
    tr.id = "appointment_" + appointment.id;

    displayAppointment(tr, appointment);

    tbody.appendChild(tr);
}

function displayAppointment( tr, appointment)
{
    let tdPatient = document.createElement("td");
    tdPatient.setAttribute("scope", "row");
    tdPatient.className = "col-2";
    tdPatient.textContent = appointment.patient.firstname + " " + appointment.patient.lastname;

    let tdStart = document.createElement("td");
    tdStart.className = "col-1";
    tdStart.textContent = appointment.start;

    let tdEnd = document.createElement("td");
    tdEnd.className = "col-1";
    tdEnd.textContent = appointment.end;

    let bearbeiten = document.createElement('i');
    bearbeiten.className = "bi bi-pencil-fill";
    bearbeiten.style.cursor = "pointer";

    let editLink = document.createElement('a');
    editLink.className = "d-inline-block";
    editLink.onclick = function() {
        fillAppointmentEditForm( appointment );
    }
    editLink.dataset.bsToggle = "modal";
    editLink.dataset.bsTarget = "#appointmentFormModal";
    editLink.appendChild(bearbeiten);

    let löschen = document.createElement('i');
    löschen.className = "bi bi-trash";
    löschen.style.cursor = "pointer";

    let deleteLink = document.createElement('a');
    deleteLink.className = "d-inline-block";
    deleteLink.onclick = function() {
        deleteAppointment( appointment.id );
    }
    deleteLink.appendChild(löschen);

    tr.appendChild(tdPatient);
    tr.appendChild(tdStart);
    tr.appendChild(tdEnd);
    tr.appendChild(editLink);
    tr.appendChild(deleteLink);
}

function updateRowAppointment( appointment )
{

    const row = document.querySelector("#appointment_" + appointment.id);
    row.innerHTML = "";

    displayAppointment( row, appointment);

}

function removeAppointment( id )
{
    document.querySelector("#appointment_" + id).remove();
  
}

function fillAppointmentEditForm( appointment )
{
    document.querySelector('#appointmentId').value = appointment.id;
    document.querySelector('#patient').value = appointment.patient.id;

    // Konvertierung in ISO-8601-Format
   // const startISO = appointment.start.replace(' ', 'T');
   // const endISO = appointment.end.replace(' ', 'T');

    document.querySelector('#start').value = appointment.start;
    document.querySelector('#end').value = appointment.end;
}