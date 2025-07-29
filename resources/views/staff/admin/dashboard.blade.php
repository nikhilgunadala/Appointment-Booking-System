<x-layouts.admin>
    <div class="mb-4">
        <h1 class="h2 fw-bold">Admin Dashboard</h1>
        <p class="text-muted">High-level overview of the HealthCare Plus system.</p>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card">
                <h5>Total Patients</h5>
                <p class="fs-2 fw-bold text-primary mb-0">{{ $totalPatients }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <h5>Total Doctors</h5>
                <p class="fs-2 fw-bold text-info mb-0">{{ $totalDoctors }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <h5>Appointments Today</h5>
                <p class="fs-2 fw-bold text-success mb-0">{{ $appointmentsToday }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <h5>Pending Appointments</h5>
                <p class="fs-2 fw-bold text-warning mb-0">{{ $pendingAppointments }}</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <h3 class="h4 fw-bold mb-3">Recent Registrations</h3>
            <div class="card">
                <ul class="list-group list-group-flush">
                    @forelse ($recentUsers as $user)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <p class="fw-bold mb-0">{{ $user->name }}</p>
                                <p class="small text-muted mb-0">{{ $user->email }}</p>
                            </div>
                            <span class="badge rounded-pill 
                                @if($user->role == 'patient') bg-primary-subtle text-primary-emphasis
                                @else bg-info-subtle text-info-emphasis @endif
                            ">{{ ucfirst($user->role) }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">No recent registrations.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="col-md-6">
            <h3 class="h4 fw-bold mb-3">Upcoming Appointments</h3>
            <div class="card">
                 <ul class="list-group list-group-flush">
                    @forelse ($upcomingAppointments as $appointment)
                        <li class="list-group-item">
                            <p class="fw-bold mb-0">{{ $appointment->patient->name ?? 'N/A' }} with {{ $appointment->doctor->name ?? 'N/A' }}</p>
                            <p class="small text-muted mb-0">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y \a\t h:i A') }}</p>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">No upcoming appointments.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-layouts.admin>