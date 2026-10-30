document.addEventListener('DOMContentLoaded', () => {
    const printButton = document.getElementById('printPatient');

    if (printButton) {
        printButton.addEventListener('click', () => {
            window.print();
        });
    }
});


document.addEventListener('DOMContentLoaded', () => {
    const patientSelect = document.getElementById('patient_id');
    const appointmentSelect = document.getElementById('appointment_id');

    if (!patientSelect || !appointmentSelect) {
        return;
    }

    const filterAppointments = () => {
        const selectedPatient = patientSelect.value;

        Array.from(appointmentSelect.options).forEach(option => {
            if (!option.value) {
                option.hidden = false;
                option.disabled = false;
                return;
            }

            const allowed =
                selectedPatient !== '' &&
                option.dataset.patientId === selectedPatient;

            option.hidden = !allowed;
            option.disabled = !allowed;
        });

        const selectedOption = appointmentSelect.selectedOptions[0];

        if (selectedOption?.disabled) {
            appointmentSelect.value = '';
        }
    };

    patientSelect.addEventListener('change', filterAppointments);

    filterAppointments();
});