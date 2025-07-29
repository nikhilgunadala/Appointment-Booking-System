<x-layouts.dashboard>
    <div class="container">
        @include('patient.book.partials.header', ['step' => 3])
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <form id="bookingForm" method="POST" action="{{ route('patient.book.store.step.three') }}">
                    @csrf
                    <input type="hidden" name="appointment_time" id="appointment_time" required>
                        <div class="card">
                            <h3 class="h5 fw-bold mb-2">Select Date & Time</h3>
                            <p class="text-muted">Choose your preferred appointment slot for {{ $booking['doctor']->name ?? 'your doctor' }}.</p>

                            <div class="row mt-4">
                <!-- Calendar Section with Dropdowns -->
                                <div class="col-lg-7 mb-4 mb-lg-0">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                            <button type="button" id="prev-month" class="btn btn-light">‹</button>
                                        <div class="d-flex gap-2">
                                             <select id="month-select" class="form-select"></select>
                                            <select id="year-select" class="form-select"></select>
                                        </div>
                                        <button type="button" id="next-month" class="btn btn-light">›</button>
                                     </div>
                                <div id="calendar" class="calendar-grid">
                                    <div class="fw-bold">Sun</div><div class="fw-bold">Mon</div><div class="fw-bold">Tue</div><div class="fw-bold">Wed</div><div class="fw-bold">Thu</div><div class="fw-bold">Fri</div><div class="fw-bold">Sat</div>
                                </div>
                            </div>

                <!-- Time Slots Section -->
                <div class="col-lg-5">
                    <h5 class="fw-bold mb-3">Available Time Slots</h5>
                    <div id="time-slots" class="time-slots-grid">
                        <p class="text-muted">Please select a date to see available times.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('patient.book.create.step.two') }}" class="btn btn-secondary">← Back</a>
            <button type="submit" class="btn btn-primary" id="next-btn" disabled>Next →</button>
        </div>
    </form>
        </div>
        </div>
    </div>
<style>
    .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 0.5rem; text-align: center; }
    .calendar-day { padding: 0.75rem 0.5rem; border-radius: 5%; cursor: pointer; }
    .calendar-day.disabled { color: #adb5bd; cursor: not-allowed; background-color: #f8f9fa; }
    .calendar-day.empty { cursor: default; }
    .calendar-day:not(.disabled):not(.empty):hover { background-color: #b9c6f3ff; }
    .calendar-day.selected { background-color: #2563eb; color: white; font-weight: bold; }
    .time-slots-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
    .time-slot-btn.selected { background-color: #2563eb; color: white; border-color: #2563eb; }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const monthSelect = document.getElementById('month-select');
    const yearSelect = document.getElementById('year-select');
    const prevBtn = document.getElementById('prev-month');
    const nextBtn = document.getElementById('next-month');
    const timeSlotsContainer = document.getElementById('time-slots');
    const finalInput = document.getElementById('appointment_time');
    const nextButton = document.getElementById('next-btn');

    let currentDate = new Date('{{ $today->toDateString() }}');
    currentDate.setDate(1);
    let selectedDateStr = null;
    let selectedTime = null;
    const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

    function populateSelectors() {
        const currentYear = new Date().getFullYear();
        const currentMonth = currentDate.getMonth();
        
        // Populate Year Selector
        yearSelect.innerHTML = '';
        for (let i = 0; i < 5; i++) { // Show current year and next 4 years
            const yearOption = document.createElement('option');
            yearOption.value = currentYear + i;
            yearOption.textContent = currentYear + i;
            yearSelect.appendChild(yearOption);
        }
        yearSelect.value = currentDate.getFullYear();

        // Populate Month Selector
        monthSelect.innerHTML = '';
        months.forEach((month, index) => {
            const monthOption = document.createElement('option');
            monthOption.value = index;
            monthOption.textContent = month;
            monthSelect.appendChild(monthOption);
        });
        monthSelect.value = currentMonth;
    }

    function renderCalendar() {
        calendarEl.innerHTML = `<div class="fw-bold">Sun</div><div class="fw-bold">Mon</div><div class="fw-bold">Tue</div><div class="fw-bold">Wed</div><div class="fw-bold">Thu</div><div class="fw-bold">Fri</div><div class="fw-bold">Sat</div>`;
        
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        // Sync dropdowns
        yearSelect.value = year;
        monthSelect.value = month;

        const firstDayOfMonth = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        for (let i = 0; i < firstDayOfMonth; i++) {
            calendarEl.appendChild(document.createElement('div')).classList.add('calendar-day', 'empty');
        }

        const today = new Date();
        today.setHours(0,0,0,0);

        for (let day = 1; day <= daysInMonth; day++) {
            const dayEl = document.createElement('div');
            dayEl.classList.add('calendar-day');
            dayEl.textContent = day;
            
            const loopDate = new Date(year, month, day);

            if (loopDate < today) {
                dayEl.classList.add('disabled');
            } else {
                dayEl.addEventListener('click', () => {
                    if (dayEl.classList.contains('disabled')) return;
                    
                    document.querySelectorAll('.calendar-day.selected').forEach(d => d.classList.remove('selected'));
                    dayEl.classList.add('selected');
                    
                    selectedDateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    fetchAvailableSlots(selectedDateStr);
                    updateFinalInput();
                });
            }
            calendarEl.appendChild(dayEl);
        }
    }

    async function fetchAvailableSlots(date) {
        timeSlotsContainer.innerHTML = '<p class="text-muted">Loading...</p>';
        nextButton.disabled = true;
        selectedTime = null;

        const doctorId = "{{ $booking['doctor']->id }}";

        // CORRECTED: Using the route() helper to build the URL
        let urlTemplate = "{{ route('patient.api.doctors.slots', ['doctor' => ':doctorId']) }}";
        const url = `${urlTemplate.replace(':doctorId', doctorId)}?date=${date}`;

        try {
            const response = await fetch(url);
            const slots = await response.json();

            timeSlotsContainer.innerHTML = '';
            if (Object.keys(slots).length > 0) {
                slots.forEach(time => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'btn btn-outline-primary time-slot-btn';
                    button.dataset.time = time;
                    button.textContent = time;
                    timeSlotsContainer.appendChild(button);
                });
            } else {
                timeSlotsContainer.innerHTML = '<p class="text-muted">No available slots for this day.</p>';
            }
        } catch (error) {
            console.error('Error fetching slots:', error);
            timeSlotsContainer.innerHTML = '<p class="text-danger">Could not load time slots.</p>';
        }
    }

    timeSlotsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('time-slot-btn')) {
            document.querySelectorAll('.time-slot-btn.selected').forEach(b => b.classList.remove('selected'));
            e.target.classList.add('selected');
            selectedTime = e.target.dataset.time;
            updateFinalInput();
        }
    });

    function updateFinalInput() {
        if (selectedDateStr && selectedTime) {
            finalInput.value = `${selectedDateStr} ${selectedTime}`;
            nextButton.disabled = false;
        } else {
            finalInput.value = '';
            nextButton.disabled = true;
        }
    }
    
    // Event Listeners for controls
    prevBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });

    nextBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });

    monthSelect.addEventListener('change', (e) => {
        currentDate.setMonth(parseInt(e.target.value));
        renderCalendar();
    });

    yearSelect.addEventListener('change', (e) => {
        currentDate.setFullYear(parseInt(e.target.value));
        renderCalendar();
    });

    // Initial setup
    populateSelectors();
    renderCalendar();
});
</script>
@endpush
</x-layouts.dashboard>