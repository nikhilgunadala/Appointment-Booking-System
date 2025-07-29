<x-layouts.dashboard>
    <div class="container">
        @include('patient.book.partials.header', ['step' => 2])
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <form id="doctor-selection-form" method="POST" action="{{ route('patient.book.store.step.two') }}">
                    @csrf
                    <div class="card">
                        <h3 class="h5 fw-bold mb-2">Select Doctor</h3>
                        <p class="text-muted">Choose your preferred healthcare provider.</p>
                        <div class="row g-3 mt-3">
                            @foreach($doctors as $doctor)
                            <div class="col-md-6">
                                <label class="d-block card-radio">
                                    <input type="radio" name="doctor_id" value="{{ $doctor->id }}" class="d-none doctor-radio" required>
                                    <div class="card card-body">
                                        <h5 class="fw-bold">{{ $doctor->name }}</h5>
                                        <p class="mb-1 fw-bold" style="color:#0066CC;">{{ $doctor->specialty }}</p>
                                        <p class="text-muted small">{{ $doctor->department }}</p>
                                        <p class="text-muted small">⭐ {{ $doctor->rating }} ({{ $doctor->experience_years }} years exp.)</p>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('patient.book.create.step.one') }}" class="btn btn-secondary">← Back</a>
                        <button type="submit" id="next-btn" class="btn btn-primary" disabled>Next →</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <style>.card-radio input:checked + .card { border-color: #0066CC; box-shadow: 0 0 0 2px #0066CC; }</style>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('doctor-selection-form');
            const nextButton = document.getElementById('next-btn');
            form.addEventListener('change', function(event) {
                if (event.target.name === 'doctor_id') {
                    nextButton.disabled = false;
                }
            });
        });
    </script>
    @endpush
</x-layouts.dashboard>