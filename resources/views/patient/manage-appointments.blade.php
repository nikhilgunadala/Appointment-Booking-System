<x-layouts.dashboard>
    <header class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 fw-bold">Manage Appointments</h2>
        <a href="{{ route('patient.book.create.step.one') }}" class="btn btn-primary">+ Book Appointment</a>
    </header>

    <!-- Filter Bar -->
    <div class="card mb-4">
        <form action="{{ route('patient.appointments.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-4"><label for="search" class="form-label">Search</label><input type="text" name="search" id="search" class="form-control" placeholder="Search by doctor or specialty..." value="{{ request('search') }}"></div>
                <div class="col-md-3"><label for="status" class="form-label">Status</label><select name="status" id="status" class="form-select"><option value="all">All Statuses</option><option value="scheduled">Scheduled</option><option value="confirmed">Confirmed</option></select></div>
                <div class="col-md-3"><label for="date_range" class="form-label">Date Range</label><select name="date_range" id="date_range" class="form-select"><option value="all">All Dates</option><option value="today">Today</option><option value="upcoming">Upcoming</option></select></div>
                 <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                        <a href="{{ route('patient.appointments.index') }}" class="btn btn-light w-50">Clear</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

   <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)" x-cloak>
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
    </div>
    <!-- Appointments List -->
    @forelse ($appointments as $appointment)
        <div class="card" x-data="{ isEditing: false }">
            <!-- Edit View -->
            <div x-show="isEditing" x-cloak>
                <h5 class="fw-bold mb-3">Reschedule Appointment</h5>
                <form action="{{ route('patient.appointments.update', $appointment) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Date</label>
                            <input type="text" name="appointment_date" class="form-control flatpickr-date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Time</label>
                            <select name="appointment_time" class="form-select appointment-time-select" required></select>
                        </div>
                        <!-- ADDED: Reason Textarea -->
                        <div class="col-12">
                            <label class="form-label">Reason for Visit (Optional)</label>
                            <textarea name="reason" class="form-control appointment-reason" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-success btn-sm">Save Changes</button>
                        <button type="button" @click="isEditing = false" class="btn btn-secondary btn-sm">Cancel</button>
                    </div>
                </form>
            </div>

            <!-- Default View -->
            <div x-show="!isEditing">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="badge rounded-pill @if($appointment->status == 'scheduled') bg-warning-subtle text-warning-emphasis @else bg-info-subtle text-info-emphasis @endif text-capitalize">{{ $appointment->status }}</span>
                        <span class="text-muted ms-2">ID: {{ $appointment->id }}</span>
                    </div>
                     @if ($appointment->status === 'scheduled' || $appointment->status === 'confirmed')
                    <div>
                         <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal" data-appointment-id="{{ $appointment->id }}">View</button>
                        <a href="{{ route('patient.appointments.export.patient', $appointment) }}" class="btn btn-outline-info btn-sm">Print</a>
                        <!-- UPDATED: Pass reason to the init function -->
                        <button @click="isEditing = true; initEditForm($event.target.closest('.card'), {{ $appointment->doctor_id }}, '{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d') }}', '{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i') }}', {{ json_encode($appointment->reason) }} )" class="btn btn-outline-primary btn-sm">Edit</button>
                        <form action="{{ route('patient.appointments.destroy', $appointment) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">Cancel</button>
                        </form>
                    </div>
                    @endif
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4"><p class="mb-1 text-muted small">Doctor</p><h6 class="fw-bold">{{ $appointment->doctor_name }}</h6><p class="text-muted">{{ $appointment->doctor_specialty }}</p></div>
                    <div class="col-md-4"><p class="mb-1 text-muted small">Date</p><h6 class="fw-bold">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d') }}</h6></div>
                    <div class="col-md-4"><p class="mb-1 text-muted small">Time</p><h6 class="fw-bold">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('h:i A') }}</h6></div>
                </div>
            </div>
        </div>
    @empty
        <div class="card text-center"><p class="text-muted mb-0">No active appointments match your filters.</p></div>
    @endforelse
    <style> [x-cloak] { display: none !important; } </style>
     <div class="modal fade" id="viewAppointmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content custom-modal-content">
                <div class="custom-modal-header">
                    <div class="icon-box"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-calendar2-check" viewBox="0 0 16 16"><path d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0z"/><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z"/><path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5z"/></svg></div>
                    <h4 class="modal-title">Appointment Details</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="viewAppointmentDetails"></div>
            </div>
        </div>
    </div>
     <style>
        .custom-modal-content { border-radius: 1rem; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1); overflow: hidden; }
        .custom-modal-header { background: linear-gradient(135deg, #0066CC, #00B4A6); color: white; padding: 1.5rem; text-align: center; position: relative; }
        .custom-modal-header .icon-box { width: 60px; height: 60px; border-radius: 50%; background-color: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; }
        .custom-modal-header .btn-close { position: absolute; top: 1rem; right: 1rem; }
        .modal.fade .modal-dialog { transform: scale(0.9); transition: transform 0.2s ease-out; }
        .modal.show .modal-dialog { transform: scale(1); }
    </style>

    @push('scripts')
    <script>
        async function fetchAvailableSlots(date, doctorId, timeSelectElement, originalTime = null) {
            if (!date || !doctorId) return;

            timeSelectElement.innerHTML = '<option>Loading...</option>';
            let urlTemplate = "{{ route('patient.api.doctors.slots', ['doctor' => ':doctorId']) }}";
            const url = `${urlTemplate.replace(':doctorId', doctorId)}?date=${date}`;
            
            const response = await fetch(url);
            const slots = await response.json();
            
            timeSelectElement.innerHTML = '';
            if (originalTime && !slots.includes(originalTime)) {
                slots.unshift(originalTime);
            }

            if (slots.length > 0) {
                slots.forEach(slot => {
                    const option = new Option(new Date(`1970-01-01T${slot}`).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}), slot);
                    timeSelectElement.add(option);
                });
            } else {
                timeSelectElement.innerHTML = '<option value="" disabled>No slots available</option>';
            }
        }

        // UPDATED: Function now accepts the reason
        function initEditForm(cardElement, doctorId, initialDate, initialTime, initialReason) {
            const dateInput = cardElement.querySelector('.flatpickr-date');
            const timeSelect = cardElement.querySelector('.appointment-time-select');
            const reasonTextarea = cardElement.querySelector('.appointment-reason'); // Get the textarea
            
            // Populate the reason textarea
            reasonTextarea.value = initialReason || '';

            const fp = flatpickr(dateInput, {
                dateFormat: "Y-m-d",
                minDate: "today",
                defaultDate: initialDate,
                onChange: function(selectedDates, dateStr, instance) {
                    fetchAvailableSlots(dateStr, doctorId, timeSelect, initialTime);
                }
            });

            fetchAvailableSlots(initialDate, doctorId, timeSelect, initialTime).then(() => {
                timeSelect.value = initialTime;
            });
        }

         const viewAppointmentModal = document.getElementById('viewAppointmentModal');
        viewAppointmentModal.addEventListener('show.bs.modal', async (event) => {
            const appointmentId = event.relatedTarget.getAttribute('data-appointment-id');
            const modalBody = viewAppointmentModal.querySelector('#viewAppointmentDetails');
            modalBody.innerHTML = '<div class="text-center p-5"><div class="spinner-border"></div></div>';
            
            const response = await fetch(`/patient/appointments/${appointmentId}`);
            const app = await response.json();
            const appDate = new Date(app.appointment_date);

            modalBody.innerHTML = `
                <div class="p-2">
                    <div class="row text-center mb-3"><div class="col"><span class="badge fs-6 text-capitalize bg-info-subtle text-info-emphasis">${app.status}</span></div></div>
                    <h6 class="mt-4">Doctor Information</h6>
                    <div class="row">
                        <div class="col-md-6"><p><small class="text-muted">Name</small><br>${app.doctor.name}</p></div>
                        <div class="col-md-6"><p><small class="text-muted">Specialty</small><br>${app.doctor.specialty}</p></div>
                    </div>
                    <hr>
                    <h6 class="mt-4">Appointment Details</h6>
                    <div class="row">
                        <div class="col-md-6"><p><small class="text-muted">Date & Time</small><br>${appDate.toLocaleString('en-US', { dateStyle: 'long', timeStyle: 'short' })}</p></div>
                        <div class="col-md-6"><p><small class="text-muted">Reason</small><br>${app.reason || 'Not given'}</p></div>
                    </div>
                </div>
            `;
        });
    </script>
    @endpush
</x-layouts.dashboard>