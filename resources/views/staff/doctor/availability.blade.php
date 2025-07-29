<x-layouts.doctor>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 fw-bold">Manage Availability</h2>
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addBlockModal">+ Block a Time Range</button>
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

    <!-- Calendar Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button type="button" id="prev-month" class="btn btn-light">‹</button>
                <h5 id="current-month-year" class="fw-bold mb-0"></h5>
                <button type="button" id="next-month" class="btn btn-light">›</button>
            </div>
            <div id="calendar" class="calendar-grid"></div>
        </div>
    </div>

    <!-- Schedule Section -->
    <h3 class="h4 fw-bold mb-3" id="schedule-title"></h3>
    <div class="row row-cols-1 row-cols-md-2 g-3" id="schedule-list-container"></div>

    <!-- Add Time Block Modal -->
    <div class="modal fade" id="addBlockModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal-content">
                <div class="custom-modal-header">
                    <div class="icon-box">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-calendar-x" viewBox="0 0 16 16">
                            <path d="M6.146 7.146a.5.5 0 0 1 .708 0L8 8.293l1.146-1.147a.5.5 0 1 1 .708.708L8.707 9l1.147 1.146a.5.5 0 0 1-.708.708L8 9.707l-1.146 1.147a.5.5 0 0 1-.708-.708L7.293 9 6.146 7.854a.5.5 0 0 1 0-.708z"/>
                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                        </svg>
                    </div>
                    <h4 class="modal-title">Block Time Slot</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="{{ route('doctor.availability.store') }}" method="POST">
                        @csrf
                        <div class="mb-3"><label for="start_time" class="form-label">Start Time</label><input type="datetime-local" name="start_time" id="start_time" class="form-control" required></div>
                        <div class="mb-3"><label for="end_time" class="form-label">End Time</label><input type="datetime-local" name="end_time" id="end_time" class="form-control" required></div>
                        <div class="mb-3"><label for="reason" class="form-label">Reason (Optional)</label><input type="text" name="reason" id="reason" class="form-control" placeholder="e.g., Lunch Break, Meeting"></div>
                        <div class="modal-footer mt-4 px-0"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger">Block Time</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Styles -->
    <style>
        .custom-modal-content {
            border-radius: 1rem;
            border: none;
            box-shadow: 0 10px 25px rgba(0,0,0,.1);
            overflow: hidden;
        }
        .custom-modal-header {
            background: linear-gradient(135deg, #0066CC, #00B4A6);
            color: white;
            padding: 1.5rem;
            text-align: center;
            position: relative;
        }
        .custom-modal-header .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: rgba(255,255,255,.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        .custom-modal-header .btn-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: .5rem;
            text-align: center;
        }
        .calendar-day {
            padding: 0.75rem 0.5rem;
            border-radius: 5%;
            cursor: pointer;
        }
        .calendar-day.disabled {
            color: #adb5bd;
            cursor: not-allowed;
            background-color: #f8f9fa;
        }
        .calendar-day.empty {
            cursor: default;
        }
        .calendar-day:not(.disabled):not(.empty):hover {
            background-color: #96afffff;
        }
        .calendar-day.selected {
            background-color: #0066CC;
            color: #fff;
            font-weight: 700;
        }
        .calendar-day.today {
            border: 1px solid #0066CC;
        }
    </style>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            const monthYearEl = document.getElementById('current-month-year');
            const prevBtn = document.getElementById('prev-month');
            const nextBtn = document.getElementById('next-month');
            const scheduleTitle = document.getElementById('schedule-title');
            const scheduleContainer = document.getElementById('schedule-list-container');
            let currentDate = new Date();
            currentDate.setDate(1);

            function renderCalendar() {
                calendarEl.innerHTML = `<div class="fw-bold small">Sun</div><div class="fw-bold small">Mon</div><div class="fw-bold small">Tue</div><div class="fw-bold small">Wed</div><div class="fw-bold small">Thu</div><div class="fw-bold small">Fri</div><div class="fw-bold small">Sat</div>`;
                const year = currentDate.getFullYear();
                const month = currentDate.getMonth();
                monthYearEl.textContent = `${currentDate.toLocaleString('default', { month: 'long' })} ${year}`;
                const firstDayOfMonth = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();

                for (let i = 0; i < firstDayOfMonth; i++) {
                    const emptyDiv = document.createElement('div');
                    emptyDiv.classList.add('calendar-day', 'empty');
                    calendarEl.appendChild(emptyDiv);
                }

                const today = new Date();
                today.setHours(0, 0, 0, 0);

                for (let day = 1; day <= daysInMonth; day++) {
                    const dayEl = document.createElement('div');
                    dayEl.classList.add('calendar-day');
                    dayEl.textContent = day;
                    const loopDate = new Date(year, month, day);

                    if (loopDate < today) {
                        dayEl.classList.add('disabled');
                    } else {
                        if (loopDate.toDateString() === today.toDateString()) {
                            dayEl.classList.add('today');
                        }
                        dayEl.addEventListener('click', () => {
                            document.querySelectorAll('.calendar-day.selected').forEach(d => d.classList.remove('selected'));
                            dayEl.classList.add('selected');
                            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                            loadScheduleForDate(dateStr);
                        });
                    }
                    calendarEl.appendChild(dayEl);
                }
            }

            async function loadScheduleForDate(dateStr) {
                scheduleTitle.textContent = `Schedule for ${dateStr}`;
                scheduleContainer.innerHTML = '<div class="col text-center text-muted">Loading...</div>';
                const response = await fetch(`{{ route('doctor.availability.schedule') }}?date=${dateStr}`);
                const schedule = await response.json();
                scheduleContainer.innerHTML = '';
                if (schedule.length > 0) {
                    schedule.forEach(slot => {
                        const time = new Date(`1970-01-01T${slot.time}`).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                        const col = document.createElement('div');
                        col.className = 'col';
                        const card = document.createElement('div');
                        card.className = 'card p-3 d-flex flex-row justify-content-between align-items-center';
                        let actionButton = '';
                        if (slot.status === 'available') {
                            actionButton = `<button class="btn btn-sm btn-outline-danger block-btn" data-time="${dateStr}T${slot.time}">Block</button>`;
                        } else if (slot.status === 'unavailable') {
                            actionButton = `<button class="btn btn-sm btn-outline-success unblock-btn" data-time="${dateStr}T${slot.time}">Unblock</button>`;
                        } else {
                            actionButton = `<span class="badge bg-secondary">Booked by Patient</span>`;
                        }
                        card.innerHTML = `<span>${time}</span>${actionButton}`;
                        col.appendChild(card);
                        scheduleContainer.appendChild(col);
                    });
                } else {
                    scheduleContainer.innerHTML = '<div class="col text-center text-muted">No schedule for this day.</div>';
                }
            }

            scheduleContainer.addEventListener('click', function (e) {
                const timeToBlock = e.target.dataset.time;
                if (e.target.matches('.block-btn')) {
                    const start = new Date(timeToBlock);
                    const end = new Date(start.getTime() + 30 * 60000);
                    const formatForInput = (date) => new Date(date.getTime() - (date.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
                    document.getElementById('start_time').value = formatForInput(start);
                    document.getElementById('end_time').value = formatForInput(end);
                    new bootstrap.Modal(document.getElementById('addBlockModal')).show();
                }
                if (e.target.matches('.unblock-btn')) {
                    if (confirm('Are you sure you want to make this time slot available?')) {
                        fetch('{{ route("doctor.availability.destroy") }}', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ time_to_clear: timeToBlock })
                        }).then(response => {
                            if (response.ok) {
                                loadScheduleForDate(timeToBlock.slice(0, 10));
                            } else {
                                alert('Could not unblock the time slot.');
                            }
                        });
                    }
                }
            });

            prevBtn.addEventListener('click', () => {
                currentDate.setMonth(currentDate.getMonth() - 1);
                renderCalendar();
            });
            nextBtn.addEventListener('click', () => {
                currentDate.setMonth(currentDate.getMonth() + 1);
                renderCalendar();
            });

            renderCalendar();
            const todayStr = new Date().toISOString().slice(0, 10);
            loadScheduleForDate(todayStr);
            document.querySelector('.calendar-day.today')?.classList.add('selected');
        });
    </script>
    @endpush
</x-layouts.doctor>
