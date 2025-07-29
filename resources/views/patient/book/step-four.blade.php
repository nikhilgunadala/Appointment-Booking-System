<x-layouts.dashboard>
    <div class="container">
        @include('patient.book.partials.header', ['step' => 4])
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form method="POST" action="{{ route('patient.book.store') }}">
                    @csrf
                    <div class="card">
                        <h3 class="h5 fw-bold mb-2">Confirmation</h3>
                        <p class="text-muted">Please review your appointment details.</p>
                        <div class="row g-4 mt-3">
                            <div class="col-md-6"><strong>Patient</strong><p>{{ $booking['patient']->name }}</p></div>
                            <div class="col-md-6"><strong>Doctor</strong><p>{{ $booking['doctor']->name }}</p></div>
                            <div class="col-md-6"><strong>Specialty</strong><p>{{ $booking['doctor']->specialty }}</p></div>
                            <div class="col-md-6"><strong>Date</strong><p>{{ \Carbon\Carbon::parse($booking['appointment_time'])->format('Y-m-d') }}</p></div>
                            <div class="col-md-6"><strong>Time</strong><p>{{ \Carbon\Carbon::parse($booking['appointment_time'])->format('H:i A') }}</p></div>
                            <div class="col-12">
                                <label for="reason" class="form-label">Additional Notes / Reason for Visit (Optional)</label>
                                <textarea name="reason" id="reason" class="form-control" rows="3" placeholder="Please describe your symptoms..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('patient.book.create.step.three') }}" class="btn btn-secondary">← Back</a>
                        <button type="submit" class="btn btn-success">Confirm Appointment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.dashboard>