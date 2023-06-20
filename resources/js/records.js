let modalInstanceRecord = null;
let modalElemRecord = document.querySelector('#recordFormModal');

modalElemRecord.addEventListener('shown.bs.modal', function (){
  modalInstanceRecord = bootstrap.Modal.getInstance(modalElemRecord);
});

modalElemRecord.addEventListener('hidden.bs.modal', function() {
    document.querySelector('#recordId').value = 0;
    document.querySelector('#recordPatient').value = 0;
    document.querySelector('#description').value = "";
});

/* records stuff */

function loadRecords()
{
    fetch("/records")
        .then(response => response.json())
        .then(data =>{
            console.log(data); // ausgabe
            displayRecords(data);
        });
}

// "Polling" -> methode die regelmässig status von etwas prüft, promise-objekt was gelöst oder abgelehnt wird
function getRecordTableShow() {
    return new Promise((resolve, reject) => {
      function checkElement() {
        const tbody = document.querySelector('#recordTableShow'); // hier wird element nach id gesucht
        if (tbody) {
          resolve(tbody); // falls gefunden wirds ausgelöst
        } else {
          setTimeout(checkElement, 100); // timeout falls nicht gefunden
        }
      }
      checkElement(); // in diesem fall ist es eine Rekursion und das else dient als "break", indem fall neuer versuch nach timeout
    });
}

function filterRecords()
{
    let patientId = document.querySelector("#patientFilterTwo").value;
    getRecordTableShow().then(tbody => {
        tbody.innerHTML = "";  // leert nur den Inhalt der Tabelle anstatt den ganzen Container
        fetch("/records?patient=" + patientId)
            .then(response => response.json())
            .then(data => displayFilteredRecords(data)); // nachdem leeren, wird hier appointments nach patient geladen
        });
}

function saveRecord()
{
    const recordId = document.querySelector('#recordId').value;

    if( recordId != null && recordId > 0 )
    {
        updateRecord( recordId );
    }
    else
    {
        postRecord();
    }

    return false;
}

function postRecord()
{
  const patient = document.querySelector("#recordPatient").value;
  const description = document.querySelector("#description").value;

  fetch("/records", {
      method: 'POST',
      body: JSON.stringify({
          patient: patient,
          description: description
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
    .then(record => {
      showNewRecord(record); // um den neuen Befund anzuzeigen
      loadRecords(); // danach loadRecord um tabelle zu refreshen ohne web refresh
    })
    .catch(error => {
        const errors = JSON.parse(error.message);

        showErrorsRecord(errors);
    });
}

function showErrorsRecord( errors )
{
    if( errors.patient )
    {
        document.querySelector('#error-recordPatient').className = "fw-bold text-danger";
        document.querySelector('#error-recordPatient').innerHTML = errors.patient;
    }
    else
    {
        document.querySelector('#error-recordPatient').className = "d-none fw-bold text-danger";
        document.querySelector('#error-recordPatient').innerHTML = "";
    }

    if( errors.description )
    {
        document.querySelector('#error-description').className = "fw-bold text-danger";
        document.querySelector('#error-description').innerHTML = errors.description;
    }
    else
    {
        document.querySelector('#error-description').className = "d-none fw-bold text-danger";
        document.querySelector('#error-description').innerHTML = "";
    }
}

function updateRecord( id )
{
  const patient = document.querySelector("#recordPatient").value;
  const description = document.querySelector("#description").value;

  fetch("/records", {
      method: 'PUT',
      body: JSON.stringify({
          id: id,
          patient: patient,
          description: description
      }),
      headers: {
          'Content-Type': 'application/json; charset=UTF-8'
      }

  })
    .then( response => response.json() )
    .then( data => {
        updateRowRecord(data);
        modalInstanceRecord.hide();
    } );
}

function deleteRecord( id )
{
    fetch("/records", {
        method: 'DELETE',
        body: JSON.stringify({
            id: id
        }),
        headers: {
            'Content-Type': 'application/json; charset=UTF-8'
        }

    })
    .then( response => response.json() )
    .then( data => removeRecord( data.id ));
}

function displayFilteredRecords(records) {

    records.forEach(record => {
      showNewRecord(record);
    });
  
}

function displayRecords(records) {
    let tbody = document.querySelector('#recordTableShow');
    tbody.innerHTML = "";
  
    records.forEach(record => {
      showNewRecord(record);
    });
  
    document.querySelector('#recordShow').className = "";
}

function showNewRecord( record )
{
    let tbody = document.querySelector('#recordTableShow');

    let tr = document.createElement("tr");
    tr.id = "record_" + record.id;

    displayRecord(tr, record);

    tbody.appendChild(tr);
}

function displayRecord( tr, record)
{
    let tdPatient = document.createElement("td");
    tdPatient.setAttribute("scope", "row");
    tdPatient.className = "col-2";
    tdPatient.textContent = record.patient.firstname + " " + record.patient.lastname;

    let tdDescription = document.createElement("td");
    tdDescription.className = "col-1";
    tdDescription.textContent = record.description;

    let tdCreated = document.createElement("td");
    tdCreated.className = "col-1";
    tdCreated.textContent = record.created_at;

    let tdUpdated = document.createElement("td");
    tdUpdated.className = "col-1";
    tdUpdated.textContent = record.updated_at;

    let bearbeiten = document.createElement('i');
    bearbeiten.className = "bi bi-pencil-fill";
    bearbeiten.style.cursor = "pointer";

    let editLink = document.createElement('a');
    editLink.className = "d-inline-block";
    editLink.onclick = function() {
        fillRecordEditForm( record );
    }
    editLink.dataset.bsToggle = "modal";
    editLink.dataset.bsTarget = "#recordFormModal";
    editLink.appendChild(bearbeiten);

    let löschen = document.createElement('i');
    löschen.className = "bi bi-trash";
    löschen.style.cursor = "pointer";

    let deleteLink = document.createElement('a');
    deleteLink.className = "d-inline-block";
    deleteLink.onclick = function() {
        deleteRecord( record.id );
    }
    deleteLink.appendChild(löschen);

    tr.appendChild(tdPatient);
    tr.appendChild(tdDescription);
    tr.appendChild(tdCreated);
    tr.appendChild(tdUpdated);
    tr.appendChild(editLink);
    tr.appendChild(deleteLink);
}

function updateRowRecord( record )
{

    const row = document.querySelector("#record_" + record.id);
    row.innerHTML = "";

    displayRecord( row, record);

}

function removeRecord( id )
{
    document.querySelector("#record_" + id).remove();
  
}

function fillRecordEditForm( record )
{
    document.querySelector('#recordId').value = record.id;
    document.querySelector('#recordPatient').value = record.patient.id;
    document.querySelector('#description').value = record.description;
}