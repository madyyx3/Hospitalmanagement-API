function filterHospital()
{
    let filterValue = document.querySelector("#hospitalFilter").value;

    if (filterValue == 0) {
        document.querySelector('#patientContainer').className = '';
        document.querySelector('#appointmentContainer').className = '';
        document.querySelector('#recordContainer').className = '';

    } else if(filterValue == 1) {
        document.querySelector('#patientContainer').className = '';
        document.querySelector('#appointmentContainer').className = 'd-none';
        document.querySelector('#recordContainer').className = 'd-none';

    } else if(filterValue == 2) {
        document.querySelector('#patientContainer').className = 'd-none';
        document.querySelector('#appointmentContainer').className = '';
        document.querySelector('#recordContainer').className = 'd-none';

    } else {
        document.querySelector('#patientContainer').className = 'd-none';
        document.querySelector('#appointmentContainer').className = 'd-none';
        document.querySelector('#recordContainer').className = '';
    }
}