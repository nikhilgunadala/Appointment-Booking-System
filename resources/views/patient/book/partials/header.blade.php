<h2 class="h3 fw-bold mb-4">Book New Appointment</h2>

<div class="d-flex justify-content-between mb-5">
    @php
        $steps = ['Patient Info', 'Select Doctor', 'Date & Time', 'Confirmation'];
    @endphp

    @foreach($steps as $index => $stepName)
        @php
            $currentStep = $index + 1;
            $isCompleted = $step > $currentStep;
            $isActive = $step == $currentStep;
        @endphp
        <div class="text-center">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-circle" 
                     style='width: 32px; height: 32px; 
                            background-color: {{ $isCompleted ? '#16a34a' : ($isActive ? '#2563eb' : '#e2e8f0') }};
                            color: white;'>
                    @if($isCompleted)
                        <span>&#10003;</span> @else
                        {{ $currentStep }}
                    @endif
                </div>
                <span class="ms-2 fw-bold {{ $isActive || $isCompleted ? 'text-dark' : 'text-muted' }}">{{ $stepName }}</span>
            </div>
        </div>
        @if(!$loop->last)
        <div class="flex-fill" style="height: 2px; background-color: {{$isCompleted ? '#16a34a' : '#e2e8f0' }}; margin: auto 1rem;"></div>
        @endif
    @endforeach
</div>