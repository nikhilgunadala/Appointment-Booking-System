<x-layouts.admin>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 fw-bold">User Management</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">+ Add New User</button>
    </div>

    <!-- Filter Bar -->
   <div class="card mb-4">
    <form action="{{ route('admin.users.index') }}" method="GET">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="search" class="form-label">Search</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <label for="role" class="form-label">Filter by Role</label>
                <select name="role" id="role" class="form-select">
                    <option value="all">All Roles</option>
                    <option value="patient">Patient</option>
                    <option value="doctor">Doctor</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-50">Filter</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light w-50">Clear</a>
                </div>
            </div>
        </div>
    </form>
</div>


    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if ($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <!-- User Cards Layout -->
    <div class="row row-cols-1 row-cols-md-3 row-cols-xl-4 g-4">
        @forelse ($users as $user)
            <div class="col">
                <div class="card user-card h-100">
                    <div class="card-body text-center d-flex flex-column">
                        <span class="badge rounded-pill text-capitalize mb-2 align-self-center @switch($user->role) @case('patient') bg-primary-subtle text-primary-emphasis @break @case('doctor') bg-info-subtle text-info-emphasis @break @case('admin') bg-secondary-subtle text-secondary-emphasis @break @endswitch">{{ $user->role }}</span>
                        <h5 class="card-title fw-bold mt-2">{{ $user->name }}</h5>
                        <p class="card-text text-muted small">{{ $user->email }}</p>
                        <div class="mt-auto pt-3">
                            <button class="btn btn-sm btn-outline-secondary view-btn" data-user-id="{{ $user->id }}" data-bs-toggle="modal" data-bs-target="#viewUserModal">View</button>
                            <button class="btn btn-sm btn-outline-primary edit-btn" data-user-id="{{ $user->id }}" data-bs-toggle="modal" data-bs-target="#editUserModal">Edit</button>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger">Delete</button></form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="card p-5 text-center"><p class="text-muted mb-0">No users found.</p></div></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $users->appends(request()->query())->links() }}</div>

    <!-- MODALS -->
    <!-- 1. View User Modal -->
    <div class="modal fade" id="viewUserModal" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content custom-modal-content"><div class="custom-modal-header"><div class="icon-box"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16"><path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/><path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/></svg></div><h4 class="modal-title" id="viewUserModalTitle"></h4><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body p-4" id="viewUserDetails"></div></div></div></div>
    <!-- 2. Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
         <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content custom-modal-content">
                <div class="custom-modal-header">
                    <div class="icon-box"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-person-plus-fill" viewBox="0 0 16 16"><path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/><path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5"/></svg></div>
                    <h4 class="modal-title">Add New User</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="{{ route('admin.users.store') }}" method="POST" x-data="{ role: 'patient' }">
                        @csrf
                        <div class="row g-3">
                            <h6 class="mb-0">Account Information</h6>
                            <div class="col-md-6"><label class="form-label">Full Name*</label><input type="text" name="name" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label">Email*</label><input type="email" name="email" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label">Password*</label><input type="password" name="password" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label">Confirm Password*</label><input type="password" name="password_confirmation" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label">Role*</label><select name="role" class="form-select" x-model="role" required><option value="patient">Patient</option><option value="doctor">Doctor</option><option value="admin">Admin</option></select></div>
                            <hr>
                            <h6 class="mb-0">Personal Details (Optional)</h6>
                            <div class="col-md-6"><label class="form-label">Phone Number</label><input type="tel" name="phone_number" class="form-control"></div>
                            <div class="col-md-6"><label class="form-label">Date of Birth</label><input type="date" name="date_of_birth" class="form-control"></div>
                            <div class="col-md-6"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="">Select</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>
                            <div class="col-12"><label class="form-label">Address</label><input type="text" name="address" class="form-control"></div>
                            
                            <div class="col-12" x-show="role === 'doctor'" x-transition>
                                <hr><h6 class="mb-3">Professional Details (Required for Doctors)</h6>
                                <div class="row g-3">
                                    <div class="col-md-4"><label class="form-label">Specialty</label><input type="text" name="specialty" class="form-control"></div>
                                    <div class="col-md-4"><label class="form-label">License Number</label><input type="text" name="license_number" class="form-control"></div>
                                    <div class="col-md-4"><label class="form-label">Years of Experience</label><input type="number" name="experience_years" class="form-control"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer mt-3 px-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 3. REDESIGNED Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content custom-modal-content">
                <div class="custom-modal-header">
                    <div class="icon-box"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/></svg></div>
                    <h4 class="modal-title" id="editUserModalTitle"></h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="editUserForm" method="POST">
                        @csrf @method('PATCH')
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Full Name</label><input type="text" name="name" id="edit_name" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" id="edit_email" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label">Role</label><select name="role" id="edit_role" class="form-select" required><option value="patient">Patient</option><option value="doctor">Doctor</option><option value="admin">Admin</option></select></div>
                            <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone_number" id="edit_phone_number" class="form-control"></div>
                            <div class="col-md-6"><label class="form-label">Gender</label><select name="gender" id="edit_gender" class="form-select"><option value="">Select</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>
                            <div class="col-md-6"><label class="form-label">Date of Birth</label><input type="date" name="date_of_birth" id="edit_date_of_birth" class="form-control"></div>
                            <div class="col-12"><label class="form-label">Address</label><input type="text" name="address" id="edit_address" class="form-control"></div>
                            <div class="col-12" id="edit_doctor_fields" style="display: none;"><hr><h6 class="mb-3">Professional Details</h6><div class="row g-3"><div class="col-md-4"><label class="form-label">Specialty</label><input type="text" name="specialty" id="edit_specialty" class="form-control"></div><div class="col-md-4"><label class="form-label">License #</label><input type="text" name="license_number" id="edit_license_number" class="form-control"></div><div class="col-md-4"><label class="form-label">Experience</label><input type="number" name="experience_years" id="edit_experience_years" class="form-control"></div></div></div>
                            <div class="col-12"><hr><h6 class="mb-3">Change Password (Optional)</h6><div class="row g-3"><div class="col-md-6"><label class="form-label">New Password</label><input type="password" name="password" class="form-control"></div><div class="col-md-6"><label class="form-label">Confirm Password</label><input type="password" name="password_confirmation" class="form-control"></div></div></div>
                        </div>
                        <div class="modal-footer mt-4 px-0"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save Changes</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>.user-card{transition:transform .2s ease-in-out,box-shadow .2s ease-in-out}.user-card:hover{transform:translateY(-5px);box-shadow:0 8px 25px rgba(0,0,0,.1)}.custom-modal-content{border-radius:1rem;border:none;box-shadow:0 10px 25px rgba(0,0,0,.1);overflow:hidden}.custom-modal-header{background:linear-gradient(135deg,#0066CC,#00B4A6);color:white;padding:1.5rem;text-align:center;position:relative}.custom-modal-header .icon-box{width:60px;height:60px;border-radius:50%;background-color:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem}.custom-modal-header .btn-close{position:absolute;top:1rem;right:1rem}.modal.fade .modal-dialog{transform:scale(.9);transition:transform .2s ease-out}.modal.show .modal-dialog{transform:scale(1)}</style>

    @push('scripts')
    <script>
        // SCRIPT FOR VIEW MODAL
        const viewUserModal = document.getElementById('viewUserModal');
        viewUserModal.addEventListener('show.bs.modal', async (event) => {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const modalTitle = viewUserModal.querySelector('.modal-title');
            const modalBody = viewUserModal.querySelector('#viewUserDetails');
            modalTitle.textContent = 'Loading...';
            modalBody.innerHTML = '<div class="text-center p-5"><div class="spinner-border"></div></div>';
            const response = await fetch(`/admin/users/${userId}`);
            const user = await response.json();
            modalTitle.textContent = user.name;
            let professionalDetails = user.role === 'doctor' ? `<h6 class="mt-4">Professional Info</h6><div class="row"><div class="col-md-6"><p><small class="text-muted">Specialty</small><br>${user.specialty || 'N/A'}</p></div><div class="col-md-6"><p><small class="text-muted">License #</small><br>${user.license_number || 'N/A'}</p></div></div>` : '';
            let manageAvailabilityBtn = user.role === 'doctor' ? `<div class="mt-4"><a href="/admin/doctors/${user.id}/availability" class="btn btn-primary">Manage Availability</a></div>` : '';
            let appointmentStats = user.stats ? `<h6 class="mt-4">Appointment Summary</h6><div class="row text-center"><div class="col-4"><p class="fs-4 fw-bold mb-0">${user.stats.total}</p><small class="text-muted">Total</small></div><div class="col-4"><p class="fs-4 fw-bold mb-0">${user.stats.upcoming}</p><small class="text-muted">Upcoming</small></div><div class="col-4"><p class="fs-4 fw-bold mb-0">${user.stats.completed}</p><small class="text-muted">Completed</small></div></div>` : '';
            modalBody.innerHTML = `<div class="p-2"><h6 class="mt-4">Personal Info</h6><div class="row"><div class="col-md-6"><p><small class="text-muted">Email</small><br>${user.email}</p></div><div class="col-md-6"><p><small class="text-muted">Phone</small><br>${user.phone_number || 'N/A'}</p></div><div class="col-md-6"><p><small class="text-muted">Gender</small><br>${user.gender ? user.gender.charAt(0).toUpperCase() + user.gender.slice(1) : 'N/A'}</p></div><div class="col-md-6"><p><small class="text-muted">DOB</small><br>${user.date_of_birth || 'N/A'}</p></div><div class="col-12"><p><small class="text-muted">Address</small><br>${user.address || 'N/A'}</p></div></div>${professionalDetails}${manageAvailabilityBtn}${appointmentStats}</div>`;
        });

        // SCRIPT FOR EDIT MODAL
        const editUserModal = document.getElementById('editUserModal');
        const doctorFields = document.getElementById('edit_doctor_fields');
        const roleSelect = document.getElementById('edit_role');
        editUserModal.addEventListener('show.bs.modal', async (event) => {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const response = await fetch(`/admin/users/${userId}`);
            const user = await response.json();
            const form = editUserModal.querySelector('#editUserForm');
            const title = editUserModal.querySelector('.modal-title');
            
            title.textContent = `Edit User: ${user.name}`;
            form.action = `/admin/users/${user.id}`;
            form.querySelector('#edit_name').value = user.name;
            form.querySelector('#edit_email').value = user.email;
            roleSelect.value = user.role;
            form.querySelector('#edit_phone_number').value = user.phone_number;
            form.querySelector('#edit_gender').value = user.gender;
            form.querySelector('#edit_date_of_birth').value = user.date_of_birth;
            form.querySelector('#edit_address').value = user.address;
            if (user.role === 'doctor') {
                form.querySelector('#edit_specialty').value = user.specialty;
                form.querySelector('#edit_license_number').value = user.license_number;
                form.querySelector('#edit_experience_years').value = user.experience_years;
            }
            // Trigger the change event to show/hide doctor fields correctly
            roleSelect.dispatchEvent(new Event('change'));
        });
        
        // Show/hide doctor fields based on role selection
        roleSelect.addEventListener('change', () => {
            doctorFields.style.display = roleSelect.value === 'doctor' ? 'block' : 'none';
        });
    </script>
    @endpush
</x-layouts.admin>