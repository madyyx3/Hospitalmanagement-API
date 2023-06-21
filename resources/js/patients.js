let modalInstance = null;
let modalElemPatient = document.querySelector('#patientFormModal');

modalElemPatient.addEventListener('shown.bs.modal', function (){
  modalInstance = bootstrap.Modal.getInstance(modalElemPatient);
});

modalElemPatient.addEventListener('hidden.bs.modal', function() {
    document.querySelector('#patientId').value = 0;
    document.querySelector('#firstname').value = "";
    document.querySelector('#lastname').value = "";
    document.querySelector('#street').value = "";
    document.querySelector('#zip').value = "";
    document.querySelector('#place').value = "";
});

/* patients stuff */

function loadPatients()
{
    fetch("/patients")
        .then(response => response.json())
        .then(data => displayPatients(data));
}

function savePatient()
{
    const patientId = document.querySelector('#patientId').value;

    if( patientId != null && patientId > 0 )
    {
        updatePatient( patientId );
    }
    else
    {
        postPatient();
    }

    return false;
}

function postPatient()
{
  const firstname = document.querySelector("#firstname").value;
  const lastname = document.querySelector("#lastname").value;
  const street = document.querySelector("#street").value;
  const zip = document.querySelector("#zip").value;
  const place = document.querySelector("#place").value;

  fetch("/patients", {
      method: 'POST',
      body: JSON.stringify({
          firstname: firstname,
          lastname: lastname,
          street: street,
          zip: zip,
          place: place
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
    .then(patient => {
      showNewPatient(patient); // um den neuen Patient anzuzeigen
      showNewPatientInFilter(patient); // nachdem der neue patient angezeigt wird, wirds im filter hinzugefügt
      showNewPatientInFilterRecord(patient);
      modalInstance.hide(); // danach loadPatients um tabelle zu refreshen ohne web refresh
    })
    .catch(error => {
        alert('Wrong input! please try again.');
        const errors = JSON.parse(error.message);
        showErrorsPatient(errors);
    });
}

function showErrorsPatient( errors )
{
    if( errors.firstname )
    {
        document.querySelector('#error-firstname').className = "fw-bold text-danger";
        document.querySelector('#error-firstname').innerHTML = errors.firstname;
    }
    else
    {
        document.querySelector('#error-firstname').className = "d-none fw-bold text-danger";
        document.querySelector('#error-firstname').innerHTML = "";
    }

    if( errors.lastname )
    {
        document.querySelector('#error-lastname').className = "fw-bold text-danger";
        document.querySelector('#error-lastname').innerHTML = errors.lastname;
    }
    else
    {
        document.querySelector('#error-lastname').className = "d-none fw-bold text-danger";
        document.querySelector('#error-lastname').innerHTML = "";
    }

    if( errors.street )
    {
        document.querySelector('#error-street').className = "fw-bold text-danger";
        document.querySelector('#error-street').innerHTML = errors.street;
    }
    else
    {
        document.querySelector('#error-street').className = "d-none fw-bold text-danger";
        document.querySelector('#error-street').innerHTML = "";
    }

    if( errors.zip )
    {
        document.querySelector('#error-zip').className = "fw-bold text-danger";
        document.querySelector('#error-zip').innerHTML = errors.zip;
    }
    else
    {
        document.querySelector('#error-zip').className = "d-none fw-bold text-danger";
        document.querySelector('#error-zip').innerHTML = "";
    }

    if( errors.place )
    {
        document.querySelector('#error-place').className = "fw-bold text-danger";
        document.querySelector('#error-place').innerHTML = errors.place;
    }
    else
    {
        document.querySelector('#error-place').className = "d-none fw-bold text-danger";
        document.querySelector('#error-place').innerHTML = "";
    }
}

function updatePatient( patientId )
{
  const firstname = document.querySelector("#firstname").value;
  const lastname = document.querySelector("#lastname").value;
  const street = document.querySelector("#street").value;
  const zip = document.querySelector("#zip").value;
  const place = document.querySelector("#place").value;

  fetch("/patients", {
      method: 'PUT',
      body: JSON.stringify({
          id: patientId,
          firstname: firstname,
          lastname: lastname,
          street: street,
          zip: zip,
          place: place
      }),
      headers: {
          'Content-Type': 'application/json; charset=UTF-8'
      }

  })
    .then( response => response.json() )
    .then( data => {
        updateRowPatient(data);
        modalInstance.hide();
    } );
}

function deletePatient( id )
{
    fetch("/patients", {
        method: 'DELETE',
        body: JSON.stringify({
            id: id
        }),
        headers: {
            'Content-Type': 'application/json; charset=UTF-8'
        }

    })
    .then( response => response.json() )
    .then( data => {
        removePatient(data.id);
    });
}

function displayPatients(patients)
{
   // let tbody = document.querySelector('#patientTableShow');
   // tbody.innerHTML = "";

    patients.forEach( patient => {
        showNewPatient(patient);
    });

    document.querySelector('#patientShow').className = "";
}

function displayPatient( tr, patient)
{
    let tdFirstname = document.createElement("td");
    tdFirstname.setAttribute("scope", "row");
    tdFirstname.className = "col-1";
    tdFirstname.textContent = patient.firstname;

    let tdLastname = document.createElement("td");
    tdLastname.className = "col-1";
    tdLastname.textContent = patient.lastname;

    let tdStreet = document.createElement("td");
    tdStreet.className = "col-1";
    tdStreet.textContent = patient.street;

    let tdZip = document.createElement("td");
    tdZip.className = "col-1";
    tdZip.textContent = patient.zip;

    let tdPlace = document.createElement("td");
    tdPlace.className = "col-1";
    tdPlace.textContent = patient.place;

    let bearbeiten = document.createElement('i');
    bearbeiten.className = "bi bi-pencil-fill";
    bearbeiten.style.cursor = "pointer";

    let editLink = document.createElement('a');
    editLink.className = "d-inline-block";
    editLink.onclick = function() {
        fillPatientEditForm( patient );
    }
    editLink.dataset.bsToggle = "modal";
    editLink.dataset.bsTarget = "#patientFormModal";
    editLink.appendChild(bearbeiten);

    let löschen = document.createElement('i');
    löschen.className = "bi bi-trash";
    löschen.style.cursor = "pointer";

    let deleteLink = document.createElement('a');
    deleteLink.className = "d-inline-block";
    deleteLink.onclick = function() {
        deletePatient( patient.id );
    }
    deleteLink.appendChild(löschen);

    tr.appendChild(tdFirstname);
    tr.appendChild(tdLastname);
    tr.appendChild(tdStreet);
    tr.appendChild(tdZip);
    tr.appendChild(tdPlace);
    tr.appendChild(editLink);
    tr.appendChild(deleteLink);
}

function updateRowPatient( patient )
{
    let tbody = document.querySelector('#patientTableShow');

    const row = document.querySelector("#patient_" + patient.id);
    row.innerHTML = "";

    displayPatient( row, patient);

    tbody.appendChild(row);

}

function showNewPatient( patient )
{
    let tbody = document.querySelector('#patientTableShow');

    let tr = document.createElement("tr");
    tr.id = "patient_" + patient.id;

    displayPatient(tr, patient);

    tbody.appendChild(tr);

}

function removePatient( id )
{
    document.querySelector("#patient_" + id).remove();
  
}

function fillPatientEditForm( patient )
{
    document.querySelector('#patientId').value = patient.id;
    document.querySelector('#firstname').value = patient.firstname;
    document.querySelector('#lastname').value = patient.lastname;
    document.querySelector('#street').value = patient.street;
    document.querySelector('#zip').value = patient.zip;
    document.querySelector('#place').value = patient.place;
}

loadPatients();