<x-layouts.admin>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 fw-bold">Appointment History</h2>
        <a href="{{ route('admin.history.export.admin') }}" class="btn btn-secondary">
            Export History (PDF)
        </a>
    </div>

    <!-- Appointments Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($appointments as $appointment)
                        <tr>
                            <td>{{ $appointment->id }}</td>
                            <td>{{ $appointment->patient->name ?? 'N/A' }}</td>
                            <td>{{ $appointment->doctor->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y @ h:i A') }}</td>
                            <td>
                                <span class="badge rounded-pill text-capitalize 
                                    @if($appointment->status == 'completed') bg-success-subtle text-success-emphasis 
                                    @else bg-danger-subtle text-danger-emphasis @endif
                                ">{{ $appointment->status }}</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal" data-appointment-id="{{ $appointment->id }}">View</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No past appointments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $appointments->links() }}</div>

    <!-- View Appointment Modal -->
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

    <style>.custom-modal-content{border-radius:1rem;border:none;box-shadow:0 10px 25px rgba(0,0,0,.1);overflow:hidden}.custom-modal-header{background:linear-gradient(135deg,#0066CC,#00B4A6);color:white;padding:1.5rem;text-align:center;position:relative}.custom-modal-header .icon-box{width:60px;height:60px;border-radius:50%;background-color:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem}.custom-modal-header .btn-close{position:absolute;top:1rem;right:1rem}.modal.fade .modal-dialog{transform:scale(.9);transition:transform .2s ease-out}.modal.show .modal-dialog{transform:scale(1)}</style>

    @push('scripts')
    <script>
        const viewAppointmentModal = document.getElementById('viewAppointmentModal');
        if (viewAppointmentModal) {
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
        }
    </script>
    @endpush
</x-layouts.admin>