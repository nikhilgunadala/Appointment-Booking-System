<x-layouts.admin>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Edit Appointment for: {{ $appointment->patient->name }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.appointments.update', $appointment) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" id="edit_appointment_id" value="{{ $appointment->id }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Date</label>
                        <input type="text" name="appointment_date" id="edit_appointment_date" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Time</label>
                        <select name="appointment_time" id="edit_appointment_time" class="form-select" required></select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Assign Doctor</label>
                        <select name="doctor_id" id="edit_doctor_id" class="form-select" required>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" @selected($appointment->doctor_id == $doctor->id)>{{ $doctor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" id="edit_status" class="form-select" required>
                            <option value="scheduled" @selected($appointment->status == 'scheduled')>Scheduled</option>
                            <option value="confirmed" @selected($appointment->status == 'confirmed')>Confirmed</option>
                            <option value="completed" @selected($appointment->status == 'completed')>Completed</option>
                            <option value="cancelled" @selected($appointment->status == 'cancelled')>Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dateInput = document.getElementById('edit_appointment_date');
            const timeSelect = document.getElementById('edit_appointment_time');
            const doctorSelect = document.getElementById('edit_doctor_id');
            const appointmentIdInput = document.getElementById('edit_appointment_id');
            
            let fp = flatpickr(dateInput, {
                dateFormat: "Y-m-d",
                minDate: "today",
                defaultDate: '{{ \Carbon\Carbon::parse($appointment->appointment_date)->format("Y-m-d") }}'
            });

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

            // Initial load
            async function initialize() {
                const selectedTime = '{{ \Carbon\Carbon::parse($appointment->appointment_date)->format("H:i") }}';
                await fetchAvailableSlots();
                
                if (![...timeSelect.options].some(o => o.value === selectedTime)) {
                    const option = new Option(new Date(`1970-01-01T${selectedTime}`).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}), selectedTime);
                    timeSelect.add(option, 0);
                }
                timeSelect.value = selectedTime;
            }

            initialize();
        });
    </script>
    @endpush
</x-layouts.admin>