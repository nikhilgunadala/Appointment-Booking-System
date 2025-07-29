<x-layouts.admin>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 fw-bold">All Appointments</h2>
    <a href="{{ route('admin.schedule.export.admin') }}" class="btn btn-primary">
        Export Today's Schedule (PDF)
    </a>
    </div>

    <!-- Filter Bar -->
    <div class="card mb-4">
        <form action="{{ route('admin.appointments.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-3"><label class="form-label">Patient Name</label><input type="text" name="patient_name" class="form-control" value="{{ request('patient_name') }}"></div>
                <div class="col-md-3"><label class="form-label">Doctor Name</label><input type="text" name="doctor_name" class="form-control" value="{{ request('doctor_name') }}"></div>
                <div class="col-md-2"><label class="form-label">Status</label><select name="status" class="form-select"><option value="all">All</option><option value="scheduled">Scheduled</option><option value="confirmed">Confirmed</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select></div>
                <div class="col-md-2"><label class="form-label">Date Range</label><select name="date_range" class="form-select"><option value="all">All</option><option value="today">Today</option><option value="upcoming">Upcoming</option><option value="past">Past</option></select></div>
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light w-50">Clear</a>
                    </div>
                </div>
            </div> 
        </form>
    </div>

     <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)" x-cloak>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
    </div>

    <!-- Appointments Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>ID</th><th>Patient</th><th>Doctor</th><th>Date & Time</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse ($appointments as $appointment)
                        <tr>
                            <td>{{ $appointment->id }}</td>
                            <td>{{ $appointment->patient->name ?? 'N/A' }}</td>
                            <td>{{ $appointment->doctor->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y @ h:i A') }}</td>
                            <td><span class="badge rounded-pill text-capitalize @switch($appointment->status) @case('scheduled') bg-warning-subtle text-warning-emphasis @break @case('confirmed') bg-info-subtle text-info-emphasis @break @case('completed') bg-success-subtle text-success-emphasis @break @case('cancelled') bg-danger-subtle text-danger-emphasis @break @endswitch">{{ $appointment->status }}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal" data-appointment-id="{{ $appointment->id }}">View</button>
                                <a href="{{ route('admin.appointments.edit', $appointment) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.appointments.destroy', $appointment) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure? This will cancel the appointment.');">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No appointments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $appointments->appends(request()->query())->links() }}</div>

    <!-- MODALS -->
    <!-- 1. View Appointment Modal -->
    <div class="modal fade" id="viewAppointmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content custom-modal-content">
                <div class="custom-modal-header">
                    <div class="icon-box"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-calendar2-check" viewBox="0 0 16 16"><path d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0z"/><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z"/><path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5z"/></svg></div>
                    <h4 class="modal-title" id="viewAppointmentModalTitle">Appointment Details</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="viewAppointmentDetails"></div>
            </div>
        </div>
    </div>
    
    <!-- 2. Edit Appointment Modal -->
    <div class="modal fade" id="editAppointmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content custom-modal-content">
                <div class="custom-modal-header">
                    <div class="icon-box"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/></svg></div>
                    <h4 class="modal-title" id="editAppointmentModalTitle">Edit Appointment</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="editAppointmentForm" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" id="edit_appointment_id" name="appointment_id">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Date</label><input type="text" name="appointment_date" id="edit_appointment_date" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label">Time</label><select name="appointment_time" id="edit_appointment_time" class="form-select" required></select></div>
                            <div class="col-md-6"><label class="form-label">Assign Doctor</label><select name="doctor_id" id="edit_doctor_id" class="form-select" required>@foreach($doctors as $doctor)<option value="{{ $doctor->id }}">{{ $doctor->name }}</option>@endforeach</select></div>
                            <div class="col-md-6"><label class="form-label">Status</label><select name="status" id="edit_status" class="form-select" required><option value="scheduled">Scheduled</option><option value="confirmed">Confirmed</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select></div>
                        </div>
                        <div class="modal-footer mt-4 px-0"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save Changes</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>.custom-modal-content{border-radius:1rem;border:none;box-shadow:0 10px 25px rgba(0,0,0,.1);overflow:hidden}.custom-modal-header{background:linear-gradient(135deg,#0066CC,#00B4A6);color:white;padding:1.5rem;text-align:center;position:relative}.custom-modal-header .icon-box{width:60px;height:60px;border-radius:50%;background-color:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem}.custom-modal-header .btn-close{position:absolute;top:1rem;right:1rem}.modal.fade .modal-dialog{transform:scale(.9);transition:transform .2s ease-out}.modal.show .modal-dialog{transform:scale(1)}</style>

    @push('scripts')
    <script>
        // SCRIPT FOR VIEW MODAL
        const viewAppointmentModal = document.getElementById('viewAppointmentModal');
        viewAppointmentModal.addEventListener('show.bs.modal', async (event) => {
            const appointmentId = event.relatedTarget.getAttribute('data-appointment-id');
            const modalBody = viewAppointmentModal.querySelector('#viewAppointmentDetails');
            modalBody.innerHTML = '<div class="text-center p-5"><div class="spinner-border"></div></div>';
            const response = await fetch(`/admin/appointments/${appointmentId}`);
            const app = await response.json();
            const appDate = new Date(app.appointment_date);
            modalBody.innerHTML = `
                <div class="p-2">
                    <div class="row text-center mb-3"><div class="col"><span class="badge fs-6 text-capitalize bg-info-subtle text-info-emphasis">${app.status}</span></div></div>
                    <h6 class="mt-4">Patient Information</h6><div class="row"><div class="col-md-6"><p><small class="text-muted">Name</small><br>${app.patient.name}</p></div><div class="col-md-6"><p><small class="text-muted">Contact</small><br>${app.patient.phone_number}</p></div></div>
                    <hr><h6 class="mt-4">Doctor Information</h6><div class="row"><div class="col-md-6"><p><small class="text-muted">Name</small><br>${app.doctor.name}</p></div><div class="col-md-6"><p><small class="text-muted">Specialty</small><br>${app.doctor.specialty}</p></div></div>
                    <hr><h6 class="mt-4">Appointment Details</h6><div class="row"><div class="col-md-6"><p><small class="text-muted">Date & Time</small><br>${appDate.toLocaleString('en-US', { dateStyle: 'long', timeStyle: 'short' })}</p></div><div class="col-md-6"><p><small class="text-muted">Reason</small><br>${app.reason || 'Not given'}</p></div></div>
                </div>
            `;
        });

        // SCRIPT FOR EDIT MODAL
        const editAppointmentModal = document.getElementById('editAppointmentModal');
        const dateInput = document.getElementById('edit_appointment_date');
        const timeSelect = document.getElementById('edit_appointment_time');
        const doctorSelect = document.getElementById('edit_doctor_id');
        const appointmentIdInput = document.getElementById('edit_appointment_id');
        let fp = flatpickr(dateInput, { dateFormat: "Y-m-d", minDate: "today" });

        async function fetchAvailableSlots() {
            const date = dateInput.value;
            const doctorId = doctorSelect.value;
            const appointmentId = appointmentIdInput.value;
            if (!date || !doctorId) return;

            timeSelect.innerHTML = '<option>Loading...</option>';
            const response = await fetch(`{{ route("admin.api.available_slots") }}?date=${date}&doctor_id=${doctorId}&appointment_id=${appointmentId}`);
            const slots = await response.json();
            
            timeSelect.innerHTML = '';
            if (slots.length > 0) {
                slots.forEach(slot => {
                    const option = new Option(new Date(`1970-01-01T${slot}`).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}), slot);
                    timeSelect.add(option);
                });
            } else {
                timeSelect.innerHTML = '<option value="" disabled>No slots available</option>';
            }
        }

        dateInput.addEventListener('change', fetchAvailableSlots);
        doctorSelect.addEventListener('change', fetchAvailableSlots);

        editAppointmentModal.addEventListener('show.bs.modal', async (event) => {
            const appointmentId = event.relatedTarget.getAttribute('data-appointment-id');
            const response = await fetch(`/admin/appointments/${appointmentId}`);
            const appointment = await response.json();
            const form = editAppointmentModal.querySelector('#editAppointmentForm');
            
            form.action = `/admin/appointments/${appointment.id}`;
            appointmentIdInput.value = appointment.id;
            fp.setDate(appointment.appointment_date, true);
            doctorSelect.value = appointment.doctor_id;
            document.getElementById('edit_status').value = appointment.status;
            
            const selectedTime = new Date(appointment.appointment_date).toTimeString().slice(0,5);
            await fetchAvailableSlots();
            
            if (![...timeSelect.options].some(o => o.value === selectedTime)) {
                const option = new Option(new Date(`1970-01-01T${selectedTime}`).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}), selectedTime);
                timeSelect.add(option, 0); // Add to the top
            }
            timeSelect.value = selectedTime;
        });
    </script>
    @endpush
</x-layouts.admin>